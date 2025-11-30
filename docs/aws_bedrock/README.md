# AWS Bedrock Integration - ICTServe

## Overview

ICTServe integrates AWS Bedrock to provide AI-powered chat capabilities using Claude 4.x models (Opus 4.5, Sonnet 4.5, Haiku 4.5). The integration includes:

- **Backend Service**: Laravel service class for Bedrock API calls
- **Web Interface**: Livewire-based chat UI with conversation management
- **MCP Server**: Model Context Protocol server exposing models to AI assistants (Amazon Q, Kiro)
- **Internet Search**: DuckDuckGo integration for web-augmented responses

## Architecture

```text
┌─────────────────────────────────────────────────────────────┐
│                     ICTServe Application                     │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────┐         ┌──────────────────┐          │
│  │  BedrockService  │────────▶│  AWS Bedrock API │          │
│  │   (Laravel)      │         │   (us-east-1)    │          │
│  └──────────────────┘         └──────────────────┘          │
│           │                                                   │
│           ├──────────────┬──────────────┬──────────────┐    │
│           ▼              ▼              ▼              ▼    │
│    ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌────────┐│
│    │  Opus    │   │  Sonnet  │   │  Haiku   │   │  Web   ││
│    │   4.5    │   │   4.5    │   │   4.5    │   │ Search ││
│    └──────────┘   └──────────┘   └──────────┘   └────────┘│
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │           Livewire BedrockChat Component             │   │
│  │  - Conversation Management                           │   │
│  │  - Model Selection                                   │   │
│  │  - Internet Search Toggle                            │   │
│  │  - Markdown Rendering                                │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│              MCP Server (Model Context Protocol)             │
├─────────────────────────────────────────────────────────────┤
│  Exposes 3 tools to AI assistants:                          │
│  - invoke_claude_opus    (Opus 4.5)                         │
│  - invoke_claude_sonnet  (Sonnet 4.5)                       │
│  - invoke_claude_haiku   (Haiku 4.5)                        │
│                                                               │
│  Used by: Amazon Q, Kiro AI                                 │
└─────────────────────────────────────────────────────────────┘
```

## Documentation Index

1. **[Setup Guide](SETUP.md)** - Installation and configuration
2. **[Implementation Details](IMPLEMENTATION.md)** - Code structure and architecture
3. **[MCP Server](MCP_SERVER.md)** - Model Context Protocol integration
4. **[Web Interface](WEB_INTERFACE.md)** - Livewire chat component
5. **[Troubleshooting](TROUBLESHOOTING.md)** - Common errors and fixes
6. **[API Reference](API_REFERENCE.md)** - BedrockService methods and usage

## Quick Start

### 1. Configure Environment

```env
# AWS Bedrock Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_BEDROCK_REGION=us-east-1
AWS_BEDROCK_MODEL_ID=global.anthropic.claude-opus-4-5-20251101-v1:0
```

### 2. Install Dependencies

```bash
composer require aws/aws-sdk-php
npm install @modelcontextprotocol/sdk
```

### 3. Run Migration

```bash
php artisan migrate
```

### 4. Access Web Interface

Navigate to: `http://localhost:8000/bedrock-chat`

## Features

- ✅ **Multi-Model Support**: Switch between Opus, Sonnet, and Haiku
- ✅ **Conversation History**: Save and load past conversations
- ✅ **Internet Search**: Web-augmented responses via DuckDuckGo
- ✅ **Markdown Rendering**: Properly formatted code blocks and lists
- ✅ **MCP Integration**: Expose models to AI assistants
- ✅ **Responsive UI**: Mobile-friendly chat interface

## Model Comparison

| Model | Speed | Cost | Use Case |
|-------|-------|------|----------|
| **Opus 4.5** | Slow | High | Complex reasoning, analysis |
| **Sonnet 4.5** | Medium | Medium | Balanced performance |
| **Haiku 4.5** | Fast | Low | Quick responses, simple tasks |

## Security

- AWS credentials stored in `.env` (never committed)
- API keys referenced via environment variables in MCP config
- Rate limiting on Bedrock API calls
- Input validation and sanitization

## Version History

- **v1.0.0** (2025-11-30): Initial implementation with 3 Claude models
- **v1.1.0** (2025-11-30): Added conversation management
- **v1.2.0** (2025-11-30): Fixed markdown rendering and UI issues

## Support

For issues or questions:

- Check [Troubleshooting Guide](TROUBLESHOOTING.md)
- Review [AWS Bedrock Documentation](https://docs.aws.amazon.com/bedrock/)
- Contact: <ict@bpm.gov.my>
