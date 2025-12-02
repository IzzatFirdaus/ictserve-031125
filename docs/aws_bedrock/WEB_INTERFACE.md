# Web Interface Documentation

## Overview

The Bedrock Chat web interface provides an interactive chat experience with AWS Bedrock Claude models. Built with Livewire 3 and Tailwind CSS, it features conversation management, model selection, and internet search capabilities.

## Features

- ✅ **Multi-Model Selection**: Switch between Opus, Sonnet, and Haiku
- ✅ **Conversation History**: Save and load past conversations
- ✅ **Internet Search**: Web-augmented responses via DuckDuckGo
- ✅ **Markdown Rendering**: Properly formatted code blocks and lists
- ✅ **Responsive Design**: Mobile-friendly interface
- ✅ **Real-time Updates**: Livewire reactive components
- ✅ **Sidebar Navigation**: Collapsible conversation list

## Access

**URL**: `http://localhost:8000/bedrock-chat`

**Route**: `GET /bedrock-chat/{id?}`

**Optional Parameter**: `{id}` - Load specific conversation

## User Interface

### Layout

```
┌─────────────────────────────────────────────────────────┐
│  ☰  Bedrock Chat                    [Model ▼] [🌐]     │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────────┐  ┌──────────────────────────────┐   │
│  │  Sidebar     │  │  Chat Area                   │   │
│  │              │  │                               │   │
│  │  [+ New]     │  │  User: Hello                 │   │
│  │              │  │  Assistant: Hi there!        │   │
│  │  Conv 1      │  │                               │   │
│  │  Conv 2      │  │  User: How are you?          │   │
│  │  Conv 3      │  │  Assistant: I'm doing well!  │   │
│  │              │  │                               │   │
│  └──────────────┘  └──────────────────────────────┘   │
│                                                          │
│  ┌──────────────────────────────────────────────────┐  │
│  │  [Type your message...]              [Send]     │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Components

#### 1. Header

- **Hamburger Menu**: Toggle sidebar visibility
- **Title**: "Bedrock Chat"
- **Model Selector**: Dropdown (Opus/Sonnet/Haiku)
- **Internet Toggle**: Enable/disable web search

#### 2. Sidebar

- **New Chat Button**: Start fresh conversation
- **Conversation List**: Recent conversations with titles
- **Delete Button**: Remove conversations

#### 3. Chat Area

- **Message Display**: User and assistant messages
- **Markdown Rendering**: Code blocks, lists, formatting
- **Loading Indicator**: Shows during API calls

#### 4. Input Area

- **Text Input**: Multi-line message input
- **Send Button**: Submit message (disabled when empty)
- **Loading State**: "Sending..." during API calls

## Usage Guide

### Starting a New Chat

1. Click **"+ New Chat"** button in sidebar
2. Select model from dropdown (Opus/Sonnet/Haiku)
3. Type message in input field
4. Click **"Send"** or press Enter

### Loading Previous Conversation

1. Click conversation title in sidebar
2. Previous messages load automatically
3. Continue conversation from where you left off

### Deleting Conversation

1. Hover over conversation in sidebar
2. Click **"Delete"** button (trash icon)
3. Conversation removed from database

### Using Internet Search

1. Toggle **"Use Internet"** switch in header
2. Type question requiring web context
3. DuckDuckGo search results included in prompt
4. Assistant responds with web-augmented answer

### Switching Models

1. Click model dropdown in header
2. Select desired model:
   - **Opus 4.5**: Complex reasoning (slower, expensive)
   - **Sonnet 4.5**: Balanced performance (medium speed/cost)
   - **Haiku 4.5**: Quick responses (fast, cheap)
3. Next message uses selected model

## Technical Implementation

### Livewire Component

**File**: `app/Livewire/BedrockChat.php`

**Key Properties**:

```php
public string $prompt = '';           // Current input
public string $model = 'opus';        // Selected model
public array $messages = [];          // Conversation history
public bool $useInternet = false;     // Web search toggle
public ?int $conversationId = null;   // Current conversation
public bool $showSidebar = true;      // Sidebar visibility
public bool $sending = false;         // Loading state
```

**Key Methods**:

- `mount(?int $id)` - Initialize with optional conversation
- `send()` - Send message to Bedrock API
- `newConversation()` - Start fresh chat
- `loadConversation(int $id)` - Load existing conversation
- `deleteConversation(int $id)` - Remove conversation
- `saveConversation()` - Persist to database
- `searchWeb(string $query)` - DuckDuckGo search

### Blade Template

**File**: `resources/views/livewire/bedrock-chat.blade.php`

**Structure**:

```blade
<div class="flex h-screen">
    <!-- Sidebar -->
    <div x-show="showSidebar" class="w-64 bg-gray-800">
        <button wire:click="newConversation">+ New Chat</button>
        
        @foreach($conversations as $conversation)
            <div wire:click="loadConversation({{ $conversation->id }})">
                {{ $conversation->title }}
                <button wire:click="deleteConversation({{ $conversation->id }})">
                    Delete
                </button>
            </div>
        @endforeach
    </div>
    
    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="bg-white border-b p-4">
            <button @click="showSidebar = !showSidebar">☰</button>
            
            <select wire:model.live="model">
                <option value="opus">Opus 4.5</option>
                <option value="sonnet">Sonnet 4.5</option>
                <option value="haiku">Haiku 4.5</option>
            </select>
            
            <label>
                <input type="checkbox" wire:model="useInternet">
                Use Internet
            </label>
        </div>
        
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4">
            @foreach($messages as $message)
                @if($message['role'] === 'user')
                    <div class="text-right">
                        <div class="bg-blue-600 text-white rounded-lg p-3">
                            {{ $message['content'] }}
                        </div>
                    </div>
                @else
                    <div class="text-left">
                        <div class="bg-gray-200 rounded-lg p-3 prose prose-sm">
                            {!! (new \League\CommonMark\CommonMarkConverter())->convert($message['content'])->getContent() !!}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        
        <!-- Input -->
        <div class="border-t p-4">
            <form wire:submit="send">
                <textarea 
                    wire:model="prompt" 
                    placeholder="Type your message..."
                    rows="3"
                ></textarea>
                
                <button 
                    type="submit"
                    wire:target="send"
                    wire:loading.attr="disabled"
                    :disabled="!prompt.trim()"
                >
                    <span wire:loading.remove wire:target="send">Send</span>
                    <span wire:loading wire:target="send">Sending...</span>
                </button>
            </form>
        </div>
    </div>
</div>
```

### Markdown Rendering

**Library**: `league/commonmark`

**Implementation**:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
$html = $converter->convert($markdown)->getContent();
```

**Styling**: Tailwind Typography plugin (`@tailwindcss/typography`)

```html
<div class="prose prose-sm max-w-none">
    {!! $html !!}
</div>
```

**Supported Markdown**:

- **Headings**: `# H1`, `## H2`, `### H3`
- **Bold**: `**bold**`
- **Italic**: `*italic*`
- **Code**: `` `inline code` ``
- **Code Blocks**: ` ```language\ncode\n``` `
- **Lists**: `- item` or `1. item`
- **Links**: `[text](url)`
- **Blockquotes**: `> quote`

## Styling

### Tailwind Classes

**Chat Container**:

```html
<div class="flex h-screen bg-gray-100">
```

**Sidebar**:

```html
<div class="w-64 bg-gray-800 text-white overflow-y-auto">
```

**Message Bubbles**:

```html
<!-- User -->
<div class="bg-blue-600 text-white rounded-lg p-3 inline-block max-w-md">

<!-- Assistant -->
<div class="bg-gray-200 text-gray-900 rounded-lg p-3 inline-block max-w-2xl">
```

**Input Field**:

```html
<textarea class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500">
```

**Button**:

```html
<button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
```

### Responsive Design

**Mobile** (< 768px):

- Sidebar hidden by default
- Full-width chat area
- Hamburger menu visible

**Tablet** (768px - 1024px):

- Sidebar toggleable
- Optimized message width

**Desktop** (> 1024px):

- Sidebar always visible
- Wide chat area
- Multi-column layout

## Accessibility

### Keyboard Navigation

- **Tab**: Navigate between elements
- **Enter**: Send message (in textarea)
- **Escape**: Close sidebar (future enhancement)

### Screen Reader Support

```html
<button aria-label="Toggle sidebar">☰</button>
<select aria-label="Select AI model">...</select>
<textarea aria-label="Type your message">...</textarea>
```

### Focus Management

```html
<button class="focus:outline-none focus:ring-2 focus:ring-blue-500">
```

## Performance

### Optimization Techniques

1. **Lazy Loading**: Conversations loaded on demand
2. **Debouncing**: Input validation debounced
3. **Pagination**: Limit conversation list (future)
4. **Caching**: Cache common responses (future)

### Loading States

```html
<!-- Button loading -->
<button wire:loading.attr="disabled" wire:target="send">
    <span wire:loading.remove wire:target="send">Send</span>
    <span wire:loading wire:target="send">Sending...</span>
</button>

<!-- Spinner -->
<div wire:loading wire:target="send">
    <svg class="animate-spin">...</svg>
</div>
```

## Customization

### Change Default Model

```php
// BedrockChat.php
public string $model = 'haiku'; // Change from 'opus'
```

### Adjust Max Tokens

```php
// BedrockChat.php - send() method
$result = $bedrock->invoke($fullPrompt, 4000, $modelMap[$this->model]); // Change from 2000
```

### Customize Sidebar Width

```html
<!-- bedrock-chat.blade.php -->
<div class="w-80 bg-gray-800"> <!-- Change from w-64 -->
```

### Add Custom Styling

```html
<!-- Add to bedrock-chat.blade.php -->
<style>
    .chat-message {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
```

## Troubleshooting

### Messages Not Appearing

**Check**:

1. Browser console for JavaScript errors
2. Network tab for failed Livewire requests
3. Laravel logs: `storage/logs/laravel.log`

### Markdown Not Rendering

**Fix**:

```bash
composer require league/commonmark
npm install @tailwindcss/typography
npm run build
```

### Sidebar Not Toggling

**Check**: Alpine.js loaded correctly

```html
<!-- Should be in layout -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### Slow Response Times

**Solutions**:

1. Switch to Haiku model for faster responses
2. Reduce maxTokens parameter
3. Disable internet search if not needed

## Future Enhancements

1. **Streaming Responses**: Real-time token streaming
2. **File Uploads**: Image analysis with Claude
3. **Voice Input**: Speech-to-text integration
4. **Export Conversations**: PDF/Markdown export
5. **Shared Conversations**: Multi-user collaboration
6. **Custom System Prompts**: Per-conversation instructions
7. **Dark Mode**: Theme toggle
8. **Conversation Search**: Search within conversations
9. **Message Editing**: Edit previous messages
10. **Conversation Folders**: Organize conversations

## References

- [Livewire 3 Documentation](https://livewire.laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [CommonMark](https://commonmark.thephpleague.com)
