# MCP Server Documentation

## Overview

The MCP (Model Context Protocol) server exposes AWS Bedrock Claude models to AI assistants like Amazon Q and Kiro AI. It provides three tools for invoking different Claude 4.x models.

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    AI Assistant                          │
│              (Amazon Q / Kiro AI)                        │
└────────────────────┬────────────────────────────────────┘
                     │ MCP Protocol (stdio)
                     ▼
┌─────────────────────────────────────────────────────────┐
│              bedrock-server.js (Node.js)                 │
│  ┌─────────────────────────────────────────────────┐   │
│  │  Tools:                                          │   │
│  │  - invoke_claude_opus    (Opus 4.5)            │   │
│  │  - invoke_claude_sonnet  (Sonnet 4.5)          │   │
│  │  - invoke_claude_haiku   (Haiku 4.5)           │   │
│  └─────────────────────────────────────────────────┘   │
└────────────────────┬────────────────────────────────────┘
                     │ AWS SDK
                     ▼
┌─────────────────────────────────────────────────────────┐
│              AWS Bedrock Runtime API                     │
│                   (us-east-1)                            │
└─────────────────────────────────────────────────────────┘
```

## Installation

### 1. Install Dependencies

```bash
cd mcp-servers
npm install @modelcontextprotocol/sdk @aws-sdk/client-bedrock-runtime
```

### 2. Create Server File

**File**: `mcp-servers/bedrock-server.js`

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

### 3. Make Executable (Linux/Mac)

```bash
chmod +x mcp-servers/bedrock-server.js
```

## Configuration

### Amazon Q Configuration

**File**: `.amazonq/mcp.json`

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

### Kiro AI Configuration

**File**: `.kiro/settings/mcp.json`

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

**Key Points**:

- `$env:VARIABLE_NAME` references environment variables from `.env`
- `autoApprove` allows tools to run without confirmation
- `command` and `args` specify how to run the server

## Tools Reference

### 1. invoke_claude_opus

**Description**: Invoke Claude Opus 4.5 (most powerful model)

**Parameters**:

- `prompt` (string, required): The prompt to send to Claude
- `maxTokens` (number, optional): Maximum tokens to generate (default: 4096)

**Example**:

```javascript
invoke_claude_opus({
  prompt: "Explain quantum computing in simple terms",
  maxTokens: 2000
})
```

**Response**:

```json
{
  "content": [
    {
      "type": "text",
      "text": "Quantum computing is a revolutionary approach..."
    }
  ]
}
```

**Use Cases**:

- Complex reasoning and analysis
- Long-form content generation
- Multi-step problem solving
- Code review and architecture design

---

### 2. invoke_claude_sonnet

**Description**: Invoke Claude Sonnet 4.5 (balanced performance)

**Parameters**:

- `prompt` (string, required): The prompt to send to Claude
- `maxTokens` (number, optional): Maximum tokens to generate (default: 4096)

**Example**:

```javascript
invoke_claude_sonnet({
  prompt: "Write a Python function to calculate fibonacci numbers",
  maxTokens: 1000
})
```

**Use Cases**:

- General-purpose tasks
- Code generation
- Documentation writing
- Data analysis

---

### 3. invoke_claude_haiku

**Description**: Invoke Claude Haiku 4.5 (fastest model)

**Parameters**:

- `prompt` (string, required): The prompt to send to Claude
- `maxTokens` (number, optional): Maximum tokens to generate (default: 4096)

**Example**:

```javascript
invoke_claude_haiku({
  prompt: "What is the capital of France?",
  maxTokens: 100
})
```

**Use Cases**:

- Quick questions
- Simple code snippets
- Fact checking
- Rapid prototyping

## Testing

### Manual Test

```bash
# Start server manually
cd mcp-servers
node bedrock-server.js
```

Expected output:

```
MCP Server running on stdio
```

### Test with Amazon Q

1. Open Amazon Q in VS Code
2. Type: `@bedrock-opus`
3. Select tool: `invoke_claude_opus`
4. Enter prompt: "Hello, how are you?"
5. Verify response appears

### Test with Kiro AI

1. Open Kiro AI
2. Use command: `/mcp bedrock-opus invoke_claude_haiku`
3. Enter prompt: "Test message"
4. Verify response

## Debugging

### Enable Debug Logging

Add to `bedrock-server.js`:

```javascript
import fs from 'fs';

const log = (message) => {
  fs.appendFileSync('scripts/mcp-debug.log', `${new Date().toISOString()} - ${message}\n`);
};

// Add logging in handlers
server.setRequestHandler(CallToolRequestSchema, async (request) => {
  log(`Tool called: ${request.params.name}`);
  log(`Arguments: ${JSON.stringify(request.params.arguments)}`);
  
  // ... rest of handler
  
  log(`Response: ${result.content[0].text.substring(0, 100)}...`);
});
```

### Check Logs

```bash
tail -f scripts/mcp-debug.log
```

### Common Issues

#### Server Not Starting

**Error**: `Cannot find module '@modelcontextprotocol/sdk'`

**Fix**:

```bash
cd mcp-servers
npm install
```

#### Authentication Failed

**Error**: `The security token included in the request is invalid`

**Fix**: Check `.env` credentials:

```bash
cat .env | grep AWS_
```

#### Model Not Found

**Error**: `The provided model identifier is invalid`

**Fix**: Verify model IDs in `MODEL_IDS` object match AWS Bedrock available models.

## Security Best Practices

1. **Never Hardcode Credentials**: Use environment variables
2. **Limit Tool Access**: Only approve necessary tools
3. **Monitor Usage**: Track API calls and costs
4. **Rotate Keys**: Regularly update AWS access keys
5. **Use IAM Roles**: Prefer IAM roles over access keys in production

## Performance Optimization

### Token Limits

Set appropriate `maxTokens` based on use case:

```javascript
// Quick responses
invoke_claude_haiku({ prompt: "...", maxTokens: 500 })

// Balanced
invoke_claude_sonnet({ prompt: "...", maxTokens: 2000 })

// Complex tasks
invoke_claude_opus({ prompt: "...", maxTokens: 4096 })
```

### Caching (Future Enhancement)

```javascript
const cache = new Map();

const getCachedResponse = (prompt) => {
  const key = `${modelId}:${prompt}`;
  return cache.get(key);
};

const setCachedResponse = (prompt, response) => {
  const key = `${modelId}:${prompt}`;
  cache.set(key, response);
};
```

## Integration Examples

### Amazon Q Workflow

```
User: @bedrock-opus analyze this code
Amazon Q: [Calls invoke_claude_opus tool]
Bedrock: [Returns analysis]
Amazon Q: [Displays formatted response]
```

### Kiro AI Workflow

```
User: /mcp bedrock-opus invoke_claude_sonnet "Write tests"
Kiro: [Calls invoke_claude_sonnet tool]
Bedrock: [Returns test code]
Kiro: [Displays code with syntax highlighting]
```

## Monitoring

### Track Usage

Add usage tracking to server:

```javascript
const usage = {
  opus: { calls: 0, tokens: 0 },
  sonnet: { calls: 0, tokens: 0 },
  haiku: { calls: 0, tokens: 0 },
};

// In handler
const modelKey = name.replace('invoke_claude_', '');
usage[modelKey].calls++;
usage[modelKey].tokens += result.usage.total_tokens;

// Log periodically
setInterval(() => {
  console.log('Usage stats:', usage);
}, 60000); // Every minute
```

## Troubleshooting

See [Troubleshooting Guide](TROUBLESHOOTING.md) for common MCP server issues.

## References

- [MCP Protocol Specification](https://modelcontextprotocol.io)
- [AWS Bedrock Documentation](https://docs.aws.amazon.com/bedrock/)
- [Amazon Q MCP Integration](https://docs.aws.amazon.com/amazonq/)
