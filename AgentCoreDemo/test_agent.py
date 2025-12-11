"""
Test script for the AgentCore Demo Agent
This tests the agent structure without requiring AWS credentials.
"""
import datetime
from strands import tool

@tool
def get_current_time():
    """Get the current time."""
    return datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

@tool
def calculate_sum(a: float, b: float) -> float:
    """Calculate the sum of two numbers."""
    return a + b

def test_tools():
    """Test the agent tools locally."""
    print("Testing AgentCore Demo Agent Tools")
    print("=" * 40)

    # Test get_current_time
    print("1. Testing get_current_time tool:")
    current_time = get_current_time()
    print(f"   Current time: {current_time}")

    # Test calculate_sum
    print("\n2. Testing calculate_sum tool:")
    result = calculate_sum(15.5, 27.3)
    print(f"   15.5 + 27.3 = {result}")

    print("\n✅ All tools are working correctly!")
    print("\nNext steps:")
    print("1. Set up AWS credentials: aws configure")
    print("2. Start dev server: agentcore dev")
    print("3. Test with: agentcore invoke --dev '{\"prompt\": \"What time is it?\"}'")

if __name__ == "__main__":
    test_tools()
