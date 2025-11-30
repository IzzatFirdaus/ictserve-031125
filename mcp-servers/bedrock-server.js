#!/usr/bin/env node
import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { CallToolRequestSchema, ListToolsRequestSchema } from '@modelcontextprotocol/sdk/types.js';
import { BedrockRuntimeClient, InvokeModelCommand } from '@aws-sdk/client-bedrock-runtime';

const client = new BedrockRuntimeClient({
  region: process.env.AWS_BEDROCK_REGION || 'us-east-1',
  credentials: {
    accessKeyId: process.env.AWS_ACCESS_KEY_ID,
    secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY,
  },
});

const server = new Server(
  { name: 'bedrock-opus', version: '1.0.0' },
  { capabilities: { tools: {} } }
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
  const modelMap = {
    invoke_claude_opus: 'global.anthropic.claude-opus-4-5-20251101-v1:0',
    invoke_claude_sonnet: 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
    invoke_claude_haiku: 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
  };

  const modelId = modelMap[request.params.name];
  if (!modelId) throw new Error('Unknown tool');

  const { prompt, maxTokens = 4096 } = request.params.arguments;
  
  const command = new InvokeModelCommand({
    modelId,
    contentType: 'application/json',
    accept: 'application/json',
    body: JSON.stringify({
      anthropic_version: 'bedrock-2023-05-31',
      max_tokens: maxTokens,
      messages: [{ role: 'user', content: prompt }],
    }),
  });

  const response = await client.send(command);
  const result = JSON.parse(new TextDecoder().decode(response.body));
  
  return {
    content: [{
      type: 'text',
      text: result.content[0].text,
    }],
  };
});

const transport = new StdioServerTransport();
await server.connect(transport);
