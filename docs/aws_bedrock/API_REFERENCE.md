# API Reference

## BedrockService

**Namespace**: `App\Services\BedrockService`

**Purpose**: Laravel service class for AWS Bedrock Runtime API interactions.

---

### Constructor

```php
public function __construct(
    private BedrockRuntimeClient $client
)
```

**Parameters**:

- `$client` - AWS Bedrock Runtime client (auto-injected)

**Example**:

```php
$bedrock = app(\App\Services\BedrockService::class);
```

---

### invoke()

Invoke AWS Bedrock model with a prompt.

```php
public function invoke(
    string $prompt, 
    int $maxTokens = 1000, 
    ?string $modelId = null
): array
```

**Parameters**:

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `$prompt` | string | Yes | - | User message to send to model |
| `$maxTokens` | int | No | 1000 | Maximum tokens in response |
| `$modelId` | string\|null | No | null | Override default model ID |

**Returns**: `array`

```php
[
    'success' => bool,      // True if API call succeeded
    'content' => string,    // Model response text
    'usage' => [
        'input_tokens' => int,   // Tokens in prompt
        'output_tokens' => int,  // Tokens in response
    ]
]
```

**Example**:

```php
use App\Services\BedrockService;

$bedrock = app(BedrockService::class);

// Basic usage
$result = $bedrock->invoke('Hello, how are you?');

// With custom max tokens
$result = $bedrock->invoke('Explain quantum computing', 2000);

// With specific model
$result = $bedrock->invoke(
    'Write a poem', 
    500, 
    'us.anthropic.claude-haiku-4-5-20251001-v1:0'
);

// Check result
if ($result['success']) {
    echo $result['content'];
    echo "Tokens used: " . $result['usage']['total_tokens'];
} else {
    echo "Error: " . $result['content'];
}
```

**Error Handling**:

```php
try {
    $result = $bedrock->invoke('Test prompt');
    
    if (!$result['success']) {
        Log::error('Bedrock failed: ' . $result['content']);
    }
} catch (\Exception $e) {
    Log::error('Exception: ' . $e->getMessage());
}
```

---

## Model IDs

### Available Models

```php
// Claude 4.x Models
const OPUS_4_5 = 'global.anthropic.claude-opus-4-5-20251101-v1:0';
const OPUS_4_1 = 'us.anthropic.claude-opus-4-1-20250805-v1:0';
const SONNET_4_5 = 'us.anthropic.claude-sonnet-4-5-20250929-v1:0';
const HAIKU_4_5 = 'us.anthropic.claude-haiku-4-5-20251001-v1:0';
```

### Model Comparison

| Model | Speed | Cost | Max Tokens | Use Case |
|-------|-------|------|------------|----------|
| **Opus 4.5** | Slow | High | 200K | Complex reasoning, analysis |
| **Opus 4.1** | Slow | High | 200K | Legacy complex tasks |
| **Sonnet 4.5** | Medium | Medium | 200K | Balanced performance |
| **Haiku 4.5** | Fast | Low | 200K | Quick responses |

### Usage Examples

```php
// Quick response (Haiku)
$result = $bedrock->invoke(
    'What is 2+2?', 
    100, 
    'us.anthropic.claude-haiku-4-5-20251001-v1:0'
);

// Balanced (Sonnet)
$result = $bedrock->invoke(
    'Write a function to sort an array', 
    1000, 
    'us.anthropic.claude-sonnet-4-5-20250929-v1:0'
);

// Complex reasoning (Opus)
$result = $bedrock->invoke(
    'Analyze this codebase architecture', 
    4000, 
    'global.anthropic.claude-opus-4-5-20251101-v1:0'
);
```

---

## Configuration

### Config File

**Location**: `config/bedrock.php`

```php
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

### Environment Variables

```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_BEDROCK_REGION=us-east-1
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0
```

### Accessing Config

```php
// Get region
$region = config('bedrock.region');

// Get default model
$modelId = config('bedrock.model_id');

// Get credentials
$credentials = config('bedrock.credentials');
```

---

## Response Format

### Success Response

```php
[
    'success' => true,
    'content' => 'Hello! I\'m doing well, thank you for asking. How can I assist you today?',
    'usage' => [
        'input_tokens' => 13,
        'output_tokens' => 18,
    ]
]
```

### Error Response

```php
[
    'success' => false,
    'content' => 'Error: ValidationException - You don\'t have access to the model',
    'usage' => []
]
```

---

## Advanced Usage

### Conversation Context

```php
$messages = [
    ['role' => 'user', 'content' => 'Hello'],
    ['role' => 'assistant', 'content' => 'Hi there!'],
    ['role' => 'user', 'content' => 'How are you?'],
];

// Build context
$context = '';
foreach ($messages as $msg) {
    $context .= "{$msg['role']}: {$msg['content']}\n";
}

// Send with context
$result = $bedrock->invoke($context . "\nassistant:", 1000);
```

### Streaming (Future Enhancement)

```php
// Not yet implemented
$bedrock->invokeStream('Tell me a story', function($chunk) {
    echo $chunk;
});
```

### Batch Processing

```php
$prompts = [
    'What is PHP?',
    'What is Laravel?',
    'What is Livewire?',
];

$results = [];
foreach ($prompts as $prompt) {
    $results[] = $bedrock->invoke($prompt, 500);
}
```

---

## Error Codes

### AWS Bedrock Errors

| Error | Cause | Solution |
|-------|-------|----------|
| `ValidationException` | Invalid model ID or parameters | Check model ID format |
| `AccessDeniedException` | Model not enabled | Enable in AWS Console |
| `ThrottlingException` | Rate limit exceeded | Implement retry logic |
| `ServiceQuotaExceededException` | Quota exceeded | Request quota increase |
| `ModelTimeoutException` | Request timeout | Reduce maxTokens or retry |

### Laravel Errors

| Error | Cause | Solution |
|-------|-------|----------|
| `BindingResolutionException` | Service not registered | Check AppServiceProvider |
| `InvalidArgumentException` | Invalid parameters | Validate input |
| `RuntimeException` | AWS SDK error | Check credentials |

---

## Rate Limiting

### AWS Bedrock Limits

| Model | Requests/min | Tokens/min |
|-------|--------------|------------|
| Opus 4.5 | 10 | 20,000 |
| Sonnet 4.5 | 20 | 40,000 |
| Haiku 4.5 | 50 | 100,000 |

### Implementing Rate Limiting

```php
use Illuminate\Support\Facades\RateLimiter;

// In controller or service
RateLimiter::attempt(
    'bedrock:' . auth()->id(),
    $perMinute = 10,
    function() use ($bedrock, $prompt) {
        return $bedrock->invoke($prompt);
    }
);
```

---

## Testing

### Unit Test

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
    
    public function test_invoke_with_custom_model(): void
    {
        $bedrock = app(BedrockService::class);
        $result = $bedrock->invoke(
            'Test', 
            100, 
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );
        
        $this->assertTrue($result['success']);
    }
}
```

### Mocking

```php
use Mockery;
use App\Services\BedrockService;

public function test_with_mock(): void
{
    $mock = Mockery::mock(BedrockService::class);
    $mock->shouldReceive('invoke')
        ->once()
        ->with('Test prompt', 1000, null)
        ->andReturn([
            'success' => true,
            'content' => 'Mocked response',
            'usage' => ['input_tokens' => 2, 'output_tokens' => 2],
        ]);
    
    $this->app->instance(BedrockService::class, $mock);
    
    // Test code using mocked service
}
```

---

## Performance Tips

1. **Use Haiku for Quick Tasks**: 5x faster than Opus
2. **Limit Max Tokens**: Lower tokens = faster response
3. **Cache Common Responses**: Store frequently asked questions
4. **Batch Similar Requests**: Process multiple prompts together
5. **Monitor Token Usage**: Track costs and optimize

---

## Security

### Input Validation

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make(['prompt' => $prompt], [
    'prompt' => 'required|string|max:10000',
]);

if ($validator->fails()) {
    return ['success' => false, 'content' => 'Invalid input'];
}
```

### Output Sanitization

```php
use Illuminate\Support\Str;

$result = $bedrock->invoke($prompt);
$sanitized = Str::limit($result['content'], 5000);
```

### Credential Protection

```php
// ❌ NEVER do this
$bedrock->invoke("My API key is: " . env('AWS_SECRET_ACCESS_KEY'));

// ✅ Always validate and sanitize
$bedrock->invoke(strip_tags($userInput));
```

---

## Logging

### Enable Debug Logging

```php
use Illuminate\Support\Facades\Log;

// In BedrockService
Log::debug('Bedrock request', [
    'model' => $modelId,
    'prompt_length' => strlen($prompt),
    'max_tokens' => $maxTokens,
]);

$result = $this->client->invokeModel(...);

Log::debug('Bedrock response', [
    'success' => $result['success'],
    'tokens_used' => $result['usage']['total_tokens'] ?? 0,
]);
```

### Log Rotation

```php
// config/logging.php
'bedrock' => [
    'driver' => 'daily',
    'path' => storage_path('logs/bedrock.log'),
    'level' => 'debug',
    'days' => 14,
],
```

---

## References

- [AWS Bedrock API Documentation](https://docs.aws.amazon.com/bedrock/latest/APIReference/)
- [Claude Model Documentation](https://docs.anthropic.com/claude/docs)
- [Laravel Service Container](https://laravel.com/docs/container)
