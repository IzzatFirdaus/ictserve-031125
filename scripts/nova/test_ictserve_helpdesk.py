# -*- coding: utf-8 -*-
"""Test ICTServe Helpdesk Submission"""
from nova_act import NovaAct
import os
from dotenv import load_dotenv

load_dotenv('.env.nova')
os.environ["NOVA_ACT_BROWSER_ARGS"] = "--remote-debugging-port=9222"

print("[1/3] Starting Nova Act on ICTServe helpdesk...")
nova = NovaAct(starting_page="http://localhost:8000", headless=False, ignore_https_errors=True)
nova.start()

print("[2/3] Submitting test helpdesk ticket...")
result = nova.act("""
Fill out the helpdesk form with:
- Name: Test User
- Email: test@example.com
- Issue: Test automation issue
Then submit the form.
""")

print("[3/3] Test completed!")
print(f"Result: {result}")
# nova.stop()
