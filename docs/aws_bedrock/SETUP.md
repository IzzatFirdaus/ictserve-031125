# AWS Bedrock Setup Guide

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- AWS Account with Bedrock access
- MySQL database

## Step 1: AWS Configuration

### 1.1 Create IAM User

1. Go to AWS IAM Console
2. Create new user: `ictserve-bedrock`
3. Attach policy: `AmazonBedrockFullAccess`
4. Generate access keys

### 1.2 Enable Bedrock Models

1. Go to AWS Bedrock Console (us-east-1 region)
2. Navigate to **Model access**
3. Request access for:
   - Claude Opus 4.5
   - Claude Sonnet 4.5
   - Claude Haiku 4.5

**Note**: Model access approval may take a few minutes.

### 1.3 Configure Environment

Add to `.env`:

```env
# AWS Bedrock Configuration
AWS_ACCESS_KEY_ID=AKIAR5RCBVDCQX45JBVE
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_BEDROCK_REGION=us-east-1
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0
```

**Available Model IDs**:

```env
# Claude 4.x Models (Recommended)
# Opus 4.5 (Global Inference Profile - REQUIRED for on-demand)
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0

# Opus 4.1 (US Inference Profile)
# AWS_BEDROCK_MODEL_ID=us.anthropic.claude-opus-4-1-20250805-v1:0

# Sonnet 4.5 (US Inference Profile)
# AWS_BEDROCK_MODEL_ID=us.anthropic.claude-sonnet-4-5-20250929-v1:0

# Haiku 4.5 (US Inference Profile)
# AWS_BEDROCK_MODEL_ID=us.anthropic.claude-haiku-4-5-20251001-v1:0
```

## Step 2: Install Dependencies

### 2.1 PHP Dependencies

```bash
composer require aws/aws-sdk-php
```

### 2.2 JavaScript Dependencies

```bash
npm install @modelcontextprotocol/sdk
npm install @tailwindcss/typography
```

### 2.3 Markdown Rendering

```bash
composer require league/commonmark
```

## Step 3: Configuration Files

### 3.1 Create Bedrock Config

File: `config/bedrock.php`

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

### 3.2 Register Service Provider

File: `app/Providers/AppServiceProvider.php`

```php
use Aws\BedrockRuntime\BedrockRuntimeClient;
use App\Services\BedrockService;

public function register(): void
{
    $this->app->singleton(BedrockRuntimeClient::class, function ($app) {
        return new BedrockRuntimeClient([
            'region' => config('bedrock.region'),
            'version' => config('bedrock.version'),
            'credentials' => config('bedrock.credentials'),
        ]);
    });

    $this->app->singleton(BedrockService::class);
}
```

## Step 4: Database Setup

### 4.1 Run Migration

```bash
php artisan migrate
```

This creates `bedrock_conversations` table:

- `id` (primary key)
- `title` (nullable)
- `messages` (json)
- `model` (default: 'opus')
- `timestamps`

## Step 5: MCP Server Setup (Optional)

### 5.1 Install MCP Server

```bash
cd mcp-servers
npm install
```

### 5.2 Configure Amazon Q

File: `.amazonq/mcp.json`

```json
{
  "mcpServers": {
    "bedrock-opus": {
      "command": "node",
      "args": ["mcp-servers/bedrock-server.js"],
      "env": {
        "AWS_ACCESS_KEY_ID": "$env:AWS_ACCESS_KEY_ID",
        "AWS_SECRET_ACCESS_KEY": "$env:AWS_SECRET_ACCESS_KEY",
        "AWS_BEDROCK_REGION": "$env:AWS_BEDROCK_REGION"
      },
      "autoApprove": [
        "invoke_claude_opus",
        "invoke_claude_sonnet",
        "invoke_claude_haiku"
      ]
    }
  }
}
```

### 5.3 Configure Kiro AI

File: `.kiro/settings/mcp.json`

```json
{
  "mcpServers": {
    "bedrock-opus": {
      "command": "node",
      "args": ["mcp-servers/bedrock-server.js"],
      "env": {
        "AWS_ACCESS_KEY_ID": "$env:AWS_ACCESS_KEY_ID",
        "AWS_SECRET_ACCESS_KEY": "$env:AWS_SECRET_ACCESS_KEY",
        "AWS_BEDROCK_REGION": "$env:AWS_BEDROCK_REGION"
      },
      "autoApprove": [
        "invoke_claude_opus",
        "invoke_claude_sonnet",
        "invoke_claude_haiku"
      ]
    }
  }
}
```

## Step 6: Build Frontend Assets

```bash
npm run build
```

## Step 7: Test Installation

### 7.1 Test via Tinker

```bash
php artisan tinker
```

```php
$bedrock = app(\App\Services\BedrockService::class);
$result = $bedrock->invoke('Hello, how are you?', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');
print_r($result);
```

Expected output:

```php
Array
(
    [success] => 1
    [content] => Hello, it's wonderful to meet you!
    [usage] => Array
        (
            [input_tokens] => 13
            [output_tokens] => 12
        )
)
```

### 7.2 Test Web Interface

Navigate to: `http://localhost:8000/bedrock-chat`

1. Select model (Opus/Sonnet/Haiku)
2. Type a message
3. Click "Send"
4. Verify response appears

## Step 8: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Verification Checklist

- [ ] AWS credentials configured in `.env`
- [ ] Bedrock models enabled in AWS Console
- [ ] Dependencies installed (composer + npm)
- [ ] Migration run successfully
- [ ] Service provider registered
- [ ] Frontend assets built
- [ ] Tinker test passes
- [ ] Web interface accessible
- [ ] MCP server configured (optional)

## Next Steps

- Review [Implementation Details](IMPLEMENTATION.md)
- Configure [MCP Server](MCP_SERVER.md)
- Explore [Web Interface](WEB_INTERFACE.md)
- Check [Troubleshooting](TROUBLESHOOTING.md) if issues occur
