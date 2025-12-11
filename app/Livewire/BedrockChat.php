<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\BedrockConversation;
use App\Services\BedrockService;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        if (empty($this->prompt) || $this->sending) return;

        $this->sending = true;

        $modelMap = [
            'opus' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
            'sonnet' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
            'haiku' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        ];

        $prompt = $this->prompt;

        if ($this->useInternet && !empty($this->prompt)) {
            $searchResults = $this->searchWeb($this->prompt);
            $prompt = "Web search results:\n{$searchResults}\n\nUser question: {$prompt}";
        }

        $this->messages[] = ['role' => 'user', 'content' => $this->prompt];

        $bedrock = app(BedrockService::class);
        $result = $bedrock->invoke($prompt, 4096, $modelMap[$this->model]);

        if ($result['success']) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $result['content'],
                'model' => $this->model,
                'tokens' => $result['usage']['output_tokens'],
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
        $title = $this->messages[0]['content'] ?? 'New Chat';
        $title = substr($title, 0, 50);

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

    private function searchWeb(string $query): string
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
                
                if (!empty($matches[1])) {
                    $results = collect($matches[1])
                        ->take(5)
                        ->map(fn($text) => html_entity_decode(strip_tags($text)))
                        ->filter()
                        ->join("\n\n");
                    
                    return $results ?: 'No relevant results found.';
                }
                
                return 'No search results found.';
            }
        } catch (\Exception $e) {
            \Log::error('Web search failed: ' . $e->getMessage());
            return 'Search temporarily unavailable.';
        }

        return 'Search unavailable.';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.bedrock-chat', [
            'conversations' => BedrockConversation::latest()->get()
        ]);
    }
}
