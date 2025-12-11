"""
Simple AgentCore Demo Agent
This demonstrates the basic structure of an AgentCore agent using Strands framework.
"""
import datetime
from strands import Agent, tool

@tool
def get_current_time():
    """Get the current time."""
    return datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

@tool
def calculate_sum(a: float, b: float) -> float:
    """Calculate the sum of two numbers."""
    return a + b

# Initialize the agent directly as the app
app = Agent(
    name="DemoAgent",
    model="global.anthropic.claude-opus-4-5-20251101-v1:0",
    system_prompt="You are a helpful assistant that can answer questions and perform simple tasks. Use the available tools when appropriate.",
    tools=[get_current_time, calculate_sum]
)

if __name__ == "__main__":
    print("AgentCore Demo Agent is ready!")
    print("Available tools:")
    print("- get_current_time: Get the current time")
    print("- calculate_sum: Calculate the sum of two numbers")
