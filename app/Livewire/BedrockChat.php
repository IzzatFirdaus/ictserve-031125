<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\BedrockConversation;
use App\Models\WebSearchLog;
use App\Services\BedrockService;
use App\Services\ModelRouter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class BedrockChat extends Component
{
    public string $prompt = '';

    public string $model = 'opus';

    public array $messages = [];

    public bool $useInternet = false;

    public ?int $conversationId = null;

    public bool $showSidebar = false;

    public bool $sending = false;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $conversation = BedrockConversation::find($id);
            if ($conversation) {
                $this->conversationId = $conversation->id;
                $this->messages = $conversation->messages;
                $this->model = $conversation->model;
            }
        }
    }

    public function send(): void
    {
        if (empty($this->prompt) || $this->sending) {
            return;
        }

        $this->sending = true;

        $requestId = (string) Str::uuid();

        $modelMap = [
            'opus' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
            'sonnet' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
            'haiku' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        ];

        $prompt = $this->prompt;

        if ($this->useInternet && ! empty($this->prompt)) {
            $searchResults = $this->searchWeb($this->prompt, $requestId);
            $prompt = "Hasil carian web:\n{$searchResults}\n\nSoalan pengguna: {$prompt}";
        }

        $this->messages[] = ['role' => 'user', 'content' => $this->prompt];

        $router = app(ModelRouter::class);
        $route = $router->routeTextGeneration($prompt, [
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'operation_type' => 'bedrock_chat',
        ]);

        if (($route['provider'] ?? null) !== 'bedrock') {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Permintaan tidak boleh diproses menggunakan Bedrock. '.(string) ($route['reason'] ?? 'Sila cuba lagi.'),
                'model' => $this->model,
            ];

            $this->saveConversation();
            $this->prompt = '';
            $this->sending = false;

            return;
        }

        $bedrock = app(BedrockService::class);
        $effectiveModelId = (string) ($route['model_id'] ?? ($modelMap[$this->model] ?? config('bedrock.model_id')));
        $result = $bedrock->invoke($prompt, 4096, $effectiveModelId, [
            'request_id' => $requestId,
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'operation_type' => 'bedrock_chat',
        ]);

        if ($result['success']) {
            $assistantMessage = [
                'role' => 'assistant',
                'content' => $result['content'],
                'model' => (string) ($route['model_key'] ?? $this->model),
            ];

            if (isset($result['usage']) && is_array($result['usage']) && array_key_exists('output_tokens', $result['usage'])) {
                $assistantMessage['tokens'] = $result['usage']['output_tokens'];
            }

            $this->messages[] = $assistantMessage;
        } else {
            Log::warning('Bedrock invoke failed', [
                'model' => $this->model,
                'error_code' => $result['error_code'] ?? null,
                'content' => $result['content'] ?? null,
            ]);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => (string) ($result['content'] ?? 'Maaf, permintaan tidak berjaya. Sila cuba lagi.'),
                'model' => (string) ($route['model_key'] ?? $this->model),
            ];
        }

        $this->saveConversation();
        $this->prompt = '';
        $this->sending = false;
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->sending = false;
    }

    public function loadConversation(int $id): void
    {
        $conversation = BedrockConversation::find($id);
        if ($conversation) {
            $this->conversationId = $conversation->id;
            $this->messages = $conversation->messages;
            $this->model = $conversation->model;
            $this->sending = false;
        }
    }

    public function deleteConversation(int $id): void
    {
        BedrockConversation::find($id)?->delete();
        if ($this->conversationId === $id) {
            $this->newConversation();
        }
    }

    private function saveConversation(): void
    {
        $title = $this->messages[0]['content'] ?? 'Perbualan Baharu';
        $title = function_exists('mb_substr')
            ? mb_substr($title, 0, 50)
            : substr($title, 0, 50);

        if ($this->conversationId) {
            BedrockConversation::find($this->conversationId)?->update([
                'messages' => $this->messages,
                'model' => $this->model,
            ]);
        } else {
            $conversation = BedrockConversation::create([
                'title' => $title,
                'messages' => $this->messages,
                'model' => $this->model,
            ]);
            $this->conversationId = $conversation->id;
        }
    }

    private function searchWeb(string $query, ?string $requestId = null): string
    {
        try {
            // Use DuckDuckGo HTML search and parse results
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->get('https://html.duckduckgo.com/html/', ['q' => $query]);

            if ($response->successful()) {
                $html = $response->body();

                // Extract search result snippets using regex
                preg_match_all('/<a class="result__snippet"[^>]*>([^<]+)<\/a>/i', $html, $matches);

                if (! empty($matches[1])) {
                    $results = collect($matches[1])
                        ->take(5)
                        ->map(fn ($text) => html_entity_decode(strip_tags($text)))
                        ->filter()
                        ->join("\n\n");

                    try {
                        WebSearchLog::query()->create([
                            'request_id' => $requestId ?? (string) Str::uuid(),
                            'search_query' => $query,
                            'provider' => 'duckduckgo',
                            'results_count' => count($matches[1]),
                            'sources_used' => [],
                            'cost' => null,
                            'user_id' => Auth::id(),
                        ]);
                    } catch (\Throwable $e) {
                        // Jangan gagalkan carian jika logging gagal.
                    }

                    return $results ?: 'Tiada hasil carian yang relevan ditemui.';
                }

                try {
                    WebSearchLog::query()->create([
                        'request_id' => $requestId ?? (string) Str::uuid(),
                        'search_query' => $query,
                        'provider' => 'duckduckgo',
                        'results_count' => 0,
                        'sources_used' => [],
                        'cost' => null,
                        'user_id' => Auth::id(),
                    ]);
                } catch (\Throwable $e) {
                    // Jangan gagalkan carian jika logging gagal.
                }

                return 'Tiada hasil carian ditemui.';
            }
        } catch (\Exception $e) {
            Log::error('Carian web gagal', ['error' => $e->getMessage()]);

            return 'Carian tidak tersedia buat sementara waktu.';
        }

        return 'Carian tidak tersedia.';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.bedrock-chat', [
            'conversations' => BedrockConversation::latest()->get(),
        ]);
    }
}
