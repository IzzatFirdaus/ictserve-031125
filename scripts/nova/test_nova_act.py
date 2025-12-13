# -*- coding: utf-8 -*-
"""
Simple Nova Act Test for ICTServe
Based on official Nova Act template
"""

from nova_act import NovaAct
import os
from dotenv import load_dotenv

# Load API key
load_dotenv('.env.nova')

# Browser debugging enabled
os.environ["NOVA_ACT_BROWSER_ARGS"] = "--remote-debugging-port=9222"

print("[1/4] Initializing Nova Act...")
nova = NovaAct(starting_page="https://www.google.com", headless=False)

print("[2/4] Starting browser...")
nova.start()

print("[3/4] Running test task...")
result = nova.act("Search for 'ICTServe Laravel' and return the first result title.")

print("[4/4] Test completed!")
print(f"Result: {result}")

# Keep browser open for inspection
# Uncomment to close: nova.stop()
