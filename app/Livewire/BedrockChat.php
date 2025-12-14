<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\BedrockConversation;
use App\Models\WebSearchLog;
use App\Services\BedrockService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class BedrockChat extends Component
{
    public string $prompt = '';

    public string $model = '';

    public array $messages = [];

    public bool $useInternet = false;

    public ?int $conversationId = null;

    public bool $showSidebar = false;

    public bool $sending = false;

    public ?string $context = null;

    public array $faqSuggestions = [];

    /**
     * Validation rules for the component
     */
    protected $rules = [
        'model' => 'required|in:opus,sonnet,haiku,nova_micro,nova_lite,nova_pro,titan_text_lite,titan_text_express',
        'prompt' => 'required|string|min:1|max:4000',
        'useInternet' => 'boolean',
    ];

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'model.required' => 'Sila pilih model AI sebelum menghantar mesej.',
            'model.in' => 'Model yang dipilih tidak sah.',
            'prompt.required' => 'Sila masukkan mesej anda.',
            'prompt.min' => 'Mesej terlalu pendek.',
            'prompt.max' => 'Mesej terlalu panjang (maksimum 4000 aksara).',
        ];
    }

    public function mount(?int $id = null): void
    {
        // Handle context parameter from request
        $this->context = request()->get('context');

        // Set FAQ suggestions if context is FAQ
        if ($this->context === 'faq') {
            $this->faqSuggestions = [
                'Bagaimana cara menghantar tiket helpdesk?',
                'Apakah prosedur untuk memohon pinjaman aset ICT?',
                'Bagaimana cara menyemak status permohonan saya?',
                'Siapa yang boleh menggunakan sistem ICTServe?',
                'Bagaimana cara mendaftar akaun baru?',
            ];
        }

        if ($id) {
            $this->loadConversation($id);
        } else {
            $this->initializeConversation();
        }

        // Restore conversation from session if available (for page refresh)
        $sessionMessages = session('bedrock_temp_messages');
        if ($sessionMessages && empty($this->messages)) {
            $this->messages = $sessionMessages;
        }
    }

    public function initializeConversation(): void
    {
        $this->messages = [];
        $this->conversationId = null;

        // Add system message based on context
        if ($this->context === 'faq') {
            $this->messages[] = [
                'role' => 'system',
                'content' => 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC. Bantu pengguna dengan soalan berkaitan perkhidmatan helpdesk, pinjaman aset ICT, dan penggunaan sistem. Jawab dalam Bahasa Melayu.',
            ];
        }
    }

    public function newConversation(): void
    {
        $this->initializeConversation();
        $this->prompt = '';
        $this->model = '';
    }

    public function loadConversation(int $id): void
    {
        $conversation = BedrockConversation::find($id);

        if ($conversation && ($conversation->user_id === Auth::id() || ! Auth::check())) {
            $this->conversationId = $conversation->id;
            $this->messages = $conversation->messages ?? [];
        } else {
            $this->initializeConversation();
        }
    }

    public function deleteConversation(int $id): void
    {
        $conversation = BedrockConversation::find($id);

        if ($conversation && ($conversation->user_id === Auth::id() || ! Auth::check())) {
            $conversation->delete();

            if ($this->conversationId === $id) {
                $this->newConversation();
            }
        }
    }

    public function useFaqSuggestion(string $suggestion): void
    {
        $this->prompt = $suggestion;
        $this->send();
    }

    public function send(): void
    {
        // Validate before sending
        $this->validate();

        if (empty($this->prompt)) {
            return;
        }

        $this->sending = true;

        try {
            // Add user message
            $this->messages[] = [
                'role' => 'user',
                'content' => $this->prompt,
            ];

            // Get AI response
            $response = $this->getAIResponse();

            if ($response && ($response['success'] ?? false)) {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => $response['content'],
                    'model' => $this->model,
                    'tokens' => $response['usage']['output_tokens'] ?? null,
                ];
            } else {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => 'Maaf, terdapat ralat semasa memproses permintaan anda. Sila cuba lagi.',
                    'model' => $this->model,
                    'error' => true,
                ];
            }

            // Save conversation
            $this->saveConversation();

            // Clear prompt
            $this->prompt = '';
        } catch (\Exception $e) {
            Log::error('Bedrock chat error: '.$e->getMessage());

            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Maaf, terdapat ralat semasa memproses permintaan anda. Sila cuba lagi.',
                'model' => $this->model,
                'error' => true,
            ];

            // Save conversation even on error
            $this->saveConversation();

            // Clear prompt
            $this->prompt = '';
        } finally {
            $this->sending = false;
        }
    }

    private function getAIResponse(): ?array
    {
        $bedrockService = app(BedrockService::class);

        // Call Bedrock service using the invoke method with the current prompt
        return $bedrockService->invoke(
            prompt: $this->prompt,
            maxTokens: 1000,
            modelId: $this->getModelId(),
            context: [
                'user_id' => Auth::id(),
                'context' => $this->context,
                'use_internet' => $this->useInternet,
            ]
        );
    }

    private function getModelId(): string
    {
        return match ($this->model) {
            // Claude 4.5 Models
            'opus' => config('bedrock.models.opus'),
            'sonnet' => config('bedrock.models.sonnet'),
            'haiku' => config('bedrock.models.haiku'),

            // Amazon Nova Models
            'nova_micro' => config('bedrock.models.nova_micro'),
            'nova_lite' => config('bedrock.models.nova_lite'),
            'nova_pro' => config('bedrock.models.nova_pro'),

            // Amazon Titan Models
            'titan_text_lite' => config('bedrock.models.titan_text_lite'),
            'titan_text_express' => config('bedrock.models.titan_text_express'),

            // Default fallback
            default => config('bedrock.models.sonnet'),
        };
    }

    private function performWebSearch(string $query): ?array
    {
        try {
            // Log the search
            WebSearchLog::create([
                'user_id' => Auth::id(),
                'query' => $query,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Perform search (implement your web search logic here)
            // This is a placeholder - you would integrate with your preferred search API
            return [
                'query' => $query,
                'results' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Web search error: '.$e->getMessage());

            return null;
        }
    }

    private function saveConversation(): void
    {
        // Always save to session for page refresh persistence
        session(['bedrock_temp_messages' => $this->messages]);

        if (! $this->conversationId) {
            // Create new conversation
            $conversation = BedrockConversation::create([
                'user_id' => Auth::id(),
                'title' => $this->generateConversationTitle(),
                'messages' => $this->messages,
                'context' => $this->context,
            ]);

            $this->conversationId = $conversation->id;
        } else {
            // Update existing conversation
            $conversation = BedrockConversation::find($this->conversationId);
            if ($conversation) {
                $conversation->update([
                    'messages' => $this->messages,
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function generateConversationTitle(): string
    {
        $userMessages = array_filter($this->messages, fn ($message) => $message['role'] === 'user');

        if (! empty($userMessages)) {
            $firstMessage = reset($userMessages)['content'];

            return Str::limit($firstMessage, 50);
        }

        return 'Perbualan Baharu';
    }

    public function getConversationsProperty(): Collection
    {
        if (Auth::check()) {
            return BedrockConversation::where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
        }

        return collect();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.bedrock-chat', [
            'conversations' => $this->conversations,
        ]);
    }
}
