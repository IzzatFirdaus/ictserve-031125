# AWS Bedrock Troubleshooting Guide

## Common Errors and Fixes

### 1. Model Access Denied

**Error**:

```
ValidationException: You don't have access to the model with the specified model ID.
```

**Cause**: Model not enabled in AWS Bedrock Console.

**Fix**:

1. Go to AWS Bedrock Console (us-east-1)
2. Navigate to **Model access**
3. Click **Manage model access**
4. Enable Claude Opus 4.5, Sonnet 4.5, Haiku 4.5
5. Wait 2-5 minutes for approval

---

### 2. Inference Profile Required

**Error**:

```
ValidationException: The provided model identifier is invalid.
Model ID: anthropic.claude-opus-4-5-20251101-v1:0
```

**Cause**: Direct model IDs don't work with on-demand throughput. Must use inference profiles.

**Fix**: Use inference profile format:

```env
# ❌ WRONG - Direct model ID
AWS_BEDROCK_MODEL_ID=anthropic.claude-opus-4-5-20251101-v1:0

# ✅ CORRECT - Global inference profile
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0

# ✅ CORRECT - US inference profile
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-haiku-4-5-20251001-v1:0
```

**Inference Profile Formats**:

- `global.anthropic.claude-*` - Global routing (Opus 4.5 only)
- `us.anthropic.claude-*` - US region routing (all models)

---

### 3. Opus 4.5 Not Available

**Error**:

```
ValidationException: The provided model identifier is invalid.
Model ID: us.anthropic.claude-opus-4-5-20251101-v1:0
```

**Cause**: Opus 4.5 requires **global** inference profile, not US profile.

**Fix**:

```env
# ❌ WRONG - US profile doesn't work for Opus 4.5
AWS_BEDROCK_MODEL_ID=us.anthropic.claude-opus-4-5-20251101-v1:0

# ✅ CORRECT - Use global profile
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0
```

---

### 4. Livewire toJSON Error

**Error**:

```javascript
Uncaught TypeError: component.toJSON is not a function
```

**Cause**: Multiple root elements in Blade view or complex objects in component properties.

**Fix 1**: Ensure single root div in Blade view:

```blade
{{-- ❌ WRONG - Multiple root elements --}}
<div>Content 1</div>
<div>Content 2</div>

{{-- ✅ CORRECT - Single root element --}}
<div>
    <div>Content 1</div>
    <div>Content 2</div>
</div>
```

**Fix 2**: Pass collections to view, don't store in properties:

```php
// ❌ WRONG - Storing collection in property
public Collection $conversations;

public function render()
{
    return view('livewire.bedrock-chat');
}

// ✅ CORRECT - Pass to view directly
public function render()
{
    return view('livewire.bedrock-chat', [
        'conversations' => BedrockConversation::latest()->get(),
    ]);
}
```

**Fix 3**: Clear all caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

### 5. Markdown Not Rendering

**Error**: Assistant responses show raw markdown (e.g., `**bold**`, `# heading`).

**Cause**: Missing CommonMark library or typography plugin.

**Fix**:

```bash
# Install CommonMark
composer require league/commonmark

# Install Tailwind typography
npm install @tailwindcss/typography

# Update tailwind.config.js
```

```javascript
// tailwind.config.js
import typography from '@tailwindcss/typography';

export default {
    plugins: [typography],
};
```

**Blade Template**:

```blade
@foreach($messages as $message)
    @if($message['role'] === 'assistant')
        <div class="prose prose-sm max-w-none">
            {!! (new \League\CommonMark\CommonMarkConverter())->convert($message['content'])->getContent() !!}
        </div>
    @endif
@endforeach
```

**Rebuild assets**:

```bash
npm run build
```

---

### 5a. CommonMarkConverter Static Call Error

**Error**:

```
Non-static method League\CommonMark\MarkdownConverter::convert() cannot be called statically
```

**Cause**: Calling `convert()` statically instead of instantiating the converter.

**Fix**:

```blade
{{-- ❌ WRONG - Static call --}}
{!! \League\CommonMark\CommonMarkConverter::convert($message['content']) !!}

{{-- ✅ CORRECT - Instantiate first --}}
{!! (new \League\CommonMark\CommonMarkConverter())->convert($message['content'])->getContent() !!}
```

**Clear caches**:

```bash
php artisan view:clear
php artisan cache:clear
```

---

### 6. "Sending..." Button Stuck

**Error**: Button shows "Sending..." permanently after clicking "New Chat".

**Cause**: `$sending` property not reset in `newConversation()` method.

**Fix**:

```php
// BedrockChat.php
public function newConversation(): void
{
    $this->conversationId = null;
    $this->messages = [];
    $this->prompt = '';
    $this->sending = false; // ✅ Add this line
}
```

**Blade Template**:

```blade
<button 
    wire:click="send" 
    wire:target="send"
    wire:loading.attr="disabled"
    :disabled="!prompt.trim()"
>
    <span wire:loading.remove wire:target="send">Send</span>
    <span wire:loading wire:target="send">Sending...</span>
</button>
```

---

### 7. Sidebar Auto-Hiding

**Error**: Sidebar closes automatically when clicking conversation history.

**Cause**: `loadConversation()` method sets `$showSidebar = false`.

**Fix**:

```php
// ❌ WRONG - Auto-hides sidebar
public function loadConversation(int $id): void
{
    $conversation = BedrockConversation::findOrFail($id);
    $this->conversationId = $id;
    $this->messages = $conversation->messages;
    $this->model = $conversation->model;
    $this->showSidebar = false; // Remove this line
}

// ✅ CORRECT - Keep sidebar open
public function loadConversation(int $id): void
{
    $conversation = BedrockConversation::findOrFail($id);
    $this->conversationId = $id;
    $this->messages = $conversation->messages;
    $this->model = $conversation->model;
    $this->sending = false;
}
```

---

### 8. DuckDuckGo Search Returns Empty

**Error**: Web search returns no results or limited data.

**Cause**: DuckDuckGo JSON API has limited data.

**Fix**: Use HTML endpoint with regex parsing:

```php
private function searchWeb(string $query): string
{
    try {
        // ❌ WRONG - JSON API (limited data)
        $url = 'https://api.duckduckgo.com/?q=' . urlencode($query) . '&format=json';
        
        // ✅ CORRECT - HTML endpoint
        $url = 'https://html.duckduckgo.com/html/?q=' . urlencode($query);
        
        $html = file_get_contents($url);
        
        // Extract snippets with regex
        preg_match_all('/<a class="result__snippet"[^>]*>(.*?)<\/a>/s', $html, $matches);
        
        $results = array_slice($matches[1], 0, 5);
        return implode("\n\n", array_map(fn($r) => strip_tags($r), $results));
    } catch (\Exception $e) {
        return '';
    }
}
```

---

### 9. AWS Credentials Not Found

**Error**:

```
Error retrieving credentials from the instance profile metadata service.
```

**Cause**: `.env` file missing or AWS credentials not set.

**Fix**:

```bash
# Check .env exists
ls -la .env

# Verify credentials
cat .env | grep AWS_
```

Expected output:

```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_BEDROCK_REGION=us-east-1
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0
```

**Clear config cache**:

```bash
php artisan config:clear
```

---

### 10. MCP Server Not Starting

**Error**:

```
Error: Cannot find module '@modelcontextprotocol/sdk'
```

**Cause**: MCP dependencies not installed.

**Fix**:

```bash
cd mcp-servers
npm install @modelcontextprotocol/sdk
```

**Verify installation**:

```bash
node bedrock-server.js
```

Expected output:

```
MCP Server running on stdio
```

---

## Debugging Commands

### Check Service Registration

```bash
php artisan tinker
```

```php
// Test BedrockService
$bedrock = app(\App\Services\BedrockService::class);
dd($bedrock);

// Test AWS Client
$client = app(\Aws\BedrockRuntime\BedrockRuntimeClient::class);
dd($client);
```

### Check Configuration

```bash
php artisan tinker
```

```php
// Check config values
config('bedrock.region');
config('bedrock.model_id');
config('bedrock.credentials');
```

### Check Database

```bash
php artisan tinker
```

```php
// Check conversations table
\App\Models\BedrockConversation::count();
\App\Models\BedrockConversation::latest()->first();
```

### Check Routes

```bash
php artisan route:list | grep bedrock
```

Expected output:

```
GET|HEAD  bedrock-chat/{id?} ... BedrockChat
```

### Check Livewire Components

```bash
php artisan livewire:list
```

Expected output:

```
bedrock-chat ... App\Livewire\BedrockChat
```

---

## Performance Issues

### Slow Response Times

**Cause**: Opus 4.5 is slower than Sonnet/Haiku.

**Fix**: Use appropriate model for task:

- **Quick responses**: Haiku 4.5
- **Balanced**: Sonnet 4.5
- **Complex reasoning**: Opus 4.5

### High Token Usage

**Cause**: Long conversation history sent with each request.

**Fix**: Limit context window:

```php
// Limit to last 10 messages
$recentMessages = array_slice($this->messages, -10);
```

---

## Getting Help

1. Check [AWS Bedrock Documentation](https://docs.aws.amazon.com/bedrock/)
2. Review [Implementation Details](IMPLEMENTATION.md)
3. Check Laravel logs: `storage/logs/laravel.log`
4. Enable debug mode: `APP_DEBUG=true` in `.env`
5. Contact support: <ict@bpm.gov.my>

---

## Error Log Locations

- **Laravel**: `storage/logs/laravel.log`
- **MCP Server**: `scripts/mcp-debug.log`
- **Browser Console**: F12 → Console tab
- **Network**: F12 → Network tab (check Livewire requests)
