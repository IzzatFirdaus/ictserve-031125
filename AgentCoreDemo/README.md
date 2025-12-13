# AgentCore Demo Agent

This is a simple demonstration of an AWS Bedrock AgentCore agent using the Strands framework.

## Features

- Basic agent with two tools:
  - `get_current_time`: Returns the current timestamp
  - `calculate_sum`: Adds two numbers together
- Configured for AWS Bedrock with Claude 3.5 Sonnet
- Ready for local development and cloud deployment

## Setup

1. Install dependencies:

   ```bash
   pip install -r requirements.txt
   ```

2. Set up AWS credentials (for Bedrock access):

   ```bash
   aws configure
   ```

## Local Development

1. Start the development server:

   ```bash
   agentcore dev
   ```

2. Test the agent in another terminal:

   ```bash
   agentcore invoke --dev '{"prompt": "What time is it?"}'
   agentcore invoke --dev '{"prompt": "Calculate 15 + 27"}'
   ```

## Deployment to AWS

1. Configure for deployment:

   ```bash
   agentcore configure --entrypoint main.py --non-interactive
   ```

2. Deploy to AWS:

   ```bash
   agentcore launch
   ```

3. Test the deployed agent:

   ```bash
   agentcore invoke '{"prompt": "Hello from the cloud!"}'
   ```

4. Check status:

   ```bash
   agentcore status
   ```

5. Clean up when done:

   ```bash
   agentcore destroy --dry-run  # Preview what will be deleted
   agentcore destroy            # Actually delete resources
   ```

## File Structure

- `main.py`: Main agent code with tools and entrypoint
- `requirements.txt`: Python dependencies
- `.bedrock_agentcore.yaml`: AgentCore configuration
- `README.md`: This documentation
