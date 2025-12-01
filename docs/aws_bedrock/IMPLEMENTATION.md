# AWS Bedrock Implementation Details

## Architecture Overview

The Bedrock integration consists of four main components:

1. **BedrockService** - Laravel service for API calls
2. **BedrockChat** - Livewire component for web UI
3. **BedrockConversation** - Eloquent model for persistence
4. **MCP Server** - Node.js server for AI assistant integration

## Component Details

### 1. BedrockService

**Location**: `app/Services/BedrockService.php`

**Purpose**: Wrapper for AWS Bedrock Runtime API calls.

**Key Methods**:

```php
public function invoke(
    string $prompt, 
    int $maxTokens = 1000, 
    ?string $modelId = null
): array
```

**Parameters**:
- `$prompt` - User message
- `$maxTokens` - Maximum response length (default: 1000)
- `$modelId` - Override default model (optional)

**Returns**:
```php
[
    'success' => true,
    'content' => 'Assistant response',
    'usage' => [
        'input_tokens' => 13,
        'output_tokens' => 12
    ]
]
```

**Implementation**:

```php
<?php

namespace App\Services;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Support\Facades\Log;

class BedrockService
{
    public function __construct(
        private BedrockRuntimeClient $client
    ) {}

    public function invoke(string $prompt, int $maxTokens = 1000, ?string $modelId = null): array
    {
        try {
            $modelId = $modelId ?? config('bedrock.model_id');

            $response = $this->client->invokeModel([
                'modelId' => $modelId,
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode([
                    'anthropic_version' => 'bedrock-2023-05-31',
                    'max_tokens' => $maxTokens,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]),
            ]);

            $result = json_decode($response['body']->getContents(), true);

            return [
                'success' => true,
                'content' => $result['content'][0]['text'] ?? '',
                'usage' => $result['usage'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Bedrock API Error: ' . $e->getMessage());

            return [
                'success' => false,
                'content' => 'Error: ' . $e->getMessage(),
                'usage' => [],
            ];
        }
    }
}
```

**Error Handling**:
- Catches all exceptions
- Logs errors to `storage/logs/laravel.log`
- Returns error message in response

---

### 2. BedrockChat Component

**Location**: `app/Livewire/BedrockChat.php`

**Purpose**: Interactive chat interface with conversation management.

**Properties**:

```php
public string $prompt = '';           // Current user input
public string $model = 'opus';        // Selected model (opus/sonnet/haiku)
public array $messages = [];          // Conversation history
public bool $useInternet = false;     // Web search toggle
public ?int $conversationId = null;   // Current conversation ID
public bool $showSidebar = true;      // Sidebar visibility
public bool $sending = false;         // Loading state
```

**Key Methods**:

#### mount()
```php
public function mount(?int $id = null): void
{
    if ($id) {
        $this->loadConversation($id);
    }
}
```

Initializes component with optional conversation ID from route parameter.

#### send()
```php
public function send(): void
{
    if (empty(trim($this->prompt))) {
        return;
    }

    $this->sending = true;

    // Add user message
    $this->messages[] = [
        'role' => 'user',
        'content' => $this->prompt,
    ];

    // Web search if enabled
    $context = '';
    if ($this->useInternet) {
        $context = $this->searchWeb($this->prompt);
    }

    // Build prompt with context
    $fullPrompt = $context 
        ? "Context from web search:\n\n{$context}\n\nUser question: {$this->prompt}"
        : $this->prompt;

    // Map model name to ID
    $modelMap = [
        'opus' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
        'sonnet' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
        'haiku' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
    ];

    // Call Bedrock API
    $bedrock = app(BedrockService::class);
    $result = $bedrock->invoke($fullPrompt, 2000, $modelMap[$this->model]);

    // Add assistant response
    if ($result['success']) {
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $result['content'],
        ];
    }

    // Save conversation
    $this->saveConversation();

    // Reset input
    $this->prompt = '';
    $this->sending = false;
}
```

#### newConversation()
```php
public function newConversation(): void
{
    $this->conversationId = null;
    $this->messages = [];
    $this->prompt = '';
    $this->sending = false;
}
```

#### loadConversation()
```php
public function loadConversation(int $id): void
{
    $conversation = BedrockConversation::findOrFail($id);
    $this->conversationId = $id;
    $this->messages = $conversation->messages;
    $this->model = $conversation->model;
    $this->sending = false;
}
```

#### deleteConversation()
```php
public function deleteConversation(int $id): void
{
    BedrockConversation::findOrFail($id)->delete();
    
    if ($this->conversationId === $id) {
        $this->newConversation();
    }
}
```

#### saveConversation()
```php
private function saveConversation(): void
{
    if (empty($this->messages)) {
        return;
    }

    $title = $this->conversationId 
        ? BedrockConversation::find($this->conversationId)->title
        : substr($this->messages[0]['content'], 0, 50);

    if ($this->conversationId) {
        BedrockConversation::where('id', $this->conversationId)->update([
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
```

#### searchWeb()
```php
private function searchWeb(string $query): string
{
    try {
        $url = 'https://html.duckduckgo.com/html/?q=' . urlencode($query);
        $html = @file_get_contents($url);
        
        if ($html === false) {
            return '';
        }

        preg_match_all('/<a class="result__snippet"[^>]*>(.*?)<\/a>/s', $html, $matches);
        
        if (empty($matches[1])) {
            return '';
        }

        $results = array_slice($matches[1], 0, 5);
        $cleanResults = array_map(fn($r) => strip_tags($r), $results);
        
        return implode("\n\n", $cleanResults);
    } catch (\Exception $e) {
        return '';
    }
}
```

---

### 3. BedrockConversation Model

**Location**: `app/Models/BedrockConversation.php`

**Purpose**: Persist chat conversations to database.

**Schema**:

```php
Schema::create('bedrock_conversations', function (Blueprint $table) {
    $table->id();
    $table->string('title')->nullable();
    $table->json('messages');
    $table->string('model')->default('opus');
    $table->timestamps();
});
```

**Model Definition**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedrockConversation extends Model
{
    protected $fillable = [
        'title',
        'messages',
        'model',
    ];

    protected function casts(): array
    {
        return [
            'messages' => 'array',
        ];
    }
}
```

**Usage**:

```php
// Create conversation
$conversation = BedrockConversation::create([
    'title' => 'My Chat',
    'messages' => [
        ['role' => 'user', 'content' => 'Hello'],
        ['role' => 'assistant', 'content' => 'Hi there!'],
    ],
    'model' => 'opus',
]);

// Load conversation
$conversation = BedrockConversation::find(1);
$messages = $conversation->messages;

// Update conversation
$conversation->update([
    'messages' => array_merge($messages, [
        ['role' => 'user', 'content' => 'How are you?'],
    ]),
]);

// Delete conversation
$conversation->delete();
```

---

### 4. MCP Server

**Location**: `mcp-servers/bedrock-server.js`

**Purpose**: Expose Bedrock models to AI assistants via Model Context Protocol.

**Implementation**:

```javascript
#!/usr/bin/env node

import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { CallToolRequestSchema, ListToolsRequestSchema } from '@modelcontextprotocol/sdk/types.js';
import { BedrockRuntimeClient, InvokeModelCommand } from '@aws-sdk/client-bedrock-runtime';

const MODEL_IDS = {
  opus: 'global.anthropic.claude-opus-4-5-20251101-v1:0',
  sonnet: 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
  haiku: 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
};

const client = new BedrockRuntimeClient({
  region: process.env.AWS_BEDROCK_REGION || 'us-east-1',
  credentials: {
    accessKeyId: process.env.AWS_ACCESS_KEY_ID,
    secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY,
  },
});

const server = new Server(
  {
    name: 'bedrock-opus',
    version: '1.0.0',
  },
  {
    capabilities: {
      tools: {},
    },
  }
);

server.setRequestHandler(ListToolsRequestSchema, async () => ({
  tools: [
    {
      name: 'invoke_claude_opus',
      description: 'Invoke AWS Bedrock Claude Opus 4.5 (most powerful)',
      inputSchema: {
        type: 'object',
        properties: {
          prompt: { type: 'string', description: 'The prompt to send to Claude' },
          maxTokens: { type: 'number', description: 'Maximum tokens to generate', default: 4096 },
        },
        required: ['prompt'],
      },
    },
    {
      name: 'invoke_claude_sonnet',
      description: 'Invoke AWS Bedrock Claude Sonnet 4.5 (balanced)',
      inputSchema: {
        type: 'object',
        properties: {
          prompt: { type: 'string', description: 'The prompt to send to Claude' },
          maxTokens: { type: 'number', description: 'Maximum tokens to generate', default: 4096 },
        },
        required: ['prompt'],
      },
    },
    {
      name: 'invoke_claude_haiku',
      description: 'Invoke AWS Bedrock Claude Haiku 4.5 (fastest)',
      inputSchema: {
        type: 'object',
        properties: {
          prompt: { type: 'string', description: 'The prompt to send to Claude' },
          maxTokens: { type: 'number', description: 'Maximum tokens to generate', default: 4096 },
        },
        required: ['prompt'],
      },
    },
  ],
}));

server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;

  const modelMap = {
    invoke_claude_opus: MODEL_IDS.opus,
    invoke_claude_sonnet: MODEL_IDS.sonnet,
    invoke_claude_haiku: MODEL_IDS.haiku,
  };

  const modelId = modelMap[name];
  if (!modelId) {
    throw new Error(`Unknown tool: ${name}`);
  }

  const command = new InvokeModelCommand({
    modelId,
    contentType: 'application/json',
    accept: 'application/json',
    body: JSON.stringify({
      anthropic_version: 'bedrock-2023-05-31',
      max_tokens: args.maxTokens || 4096,
      messages: [{ role: 'user', content: args.prompt }],
    }),
  });

  const response = await client.send(command);
  const result = JSON.parse(new TextDecoder().decode(response.body));

  return {
    content: [
      {
        type: 'text',
        text: result.content[0].text,
      },
    ],
  };
});

const transport = new StdioServerTransport();
await server.connect(transport);
```

**Tools Exposed**:

1. **invoke_claude_opus** - Claude Opus 4.5 (most powerful)
2. **invoke_claude_sonnet** - Claude Sonnet 4.5 (balanced)
3. **invoke_claude_haiku** - Claude Haiku 4.5 (fastest)

**Usage in AI Assistants**:

```javascript
// Amazon Q or Kiro can call:
invoke_claude_opus({
  prompt: "Explain quantum computing",
  maxTokens: 2000
})
```

---

## Configuration Files

### config/bedrock.php

```php
<?php

return [
    'region' => env('AWS_BEDROCK_REGION', 'us-east-1'),
    'version' => 'latest',
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
    'model_id' => env('AWS_BEDROCK_MODEL_ID', 'us.anthropic.claude-haiku-4-5-20251001-v1:0'),
];
```

### routes/web.php

```php
use App\Livewire\BedrockChat;

Route::get('/bedrock-chat/{id?}', BedrockChat::class)->name('bedrock-chat');
```

---

## Database Schema

```sql
CREATE TABLE `bedrock_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `messages` json NOT NULL,
  `model` varchar(255) NOT NULL DEFAULT 'opus',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Security Considerations

1. **Credentials**: Never commit `.env` file
2. **API Keys**: Use environment variables in MCP config
3. **Input Validation**: Sanitize user input before API calls
4. **Rate Limiting**: Consider implementing rate limits
5. **Error Messages**: Don't expose sensitive info in errors

---

## Performance Optimization

1. **Model Selection**: Use Haiku for quick responses
2. **Token Limits**: Set appropriate maxTokens (1000-2000)
3. **Context Window**: Limit conversation history
4. **Caching**: Cache common responses (future enhancement)
5. **Async Processing**: Queue long-running requests (future enhancement)

---

## Testing

### Unit Test Example

```php
use Tests\TestCase;
use App\Services\BedrockService;

class BedrockServiceTest extends TestCase
{
    public function test_invoke_returns_success(): void
    {
        $bedrock = app(BedrockService::class);
        $result = $bedrock->invoke('Hello', 100);
        
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['content']);
        $this->assertArrayHasKey('usage', $result);
    }
}
```

### Feature Test Example

```php
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\BedrockChat;

class BedrockChatTest extends TestCase
{
    public function test_can_send_message(): void
    {
        Livewire::test(BedrockChat::class)
            ->set('prompt', 'Hello')
            ->call('send')
            ->assertSet('prompt', '')
            ->assertCount('messages', 2);
    }
}
```

---

## Future Enhancements

1. **Streaming Responses**: Real-time token streaming
2. **File Uploads**: Image analysis with Claude
3. **Voice Input**: Speech-to-text integration
4. **Export Conversations**: PDF/Markdown export
5. **Shared Conversations**: Multi-user collaboration
6. **Custom System Prompts**: Per-conversation instructions
7. **Token Usage Tracking**: Cost monitoring dashboard
