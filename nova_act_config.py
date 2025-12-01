"""
Nova Act Configuration for ICTServe Project
"""

import os
from pathlib import Path
from dotenv import load_dotenv

# Load environment
load_dotenv('.env.nova')

# Project paths
PROJECT_ROOT = Path(__file__).parent
STORAGE_PATH = PROJECT_ROOT / "storage" / "nova-act"
SCREENSHOTS_PATH = STORAGE_PATH / "screenshots"
LOGS_PATH = STORAGE_PATH / "logs"

# Create directories
STORAGE_PATH.mkdir(parents=True, exist_ok=True)
SCREENSHOTS_PATH.mkdir(parents=True, exist_ok=True)
LOGS_PATH.mkdir(parents=True, exist_ok=True)

# ICTServe URLs
BASE_URL = os.getenv("APP_URL", "http://localhost:8000")
HELPDESK_URL = f"{BASE_URL}/helpdesk"
LOAN_URL = f"{BASE_URL}/loans"
ADMIN_URL = f"{BASE_URL}/admin"

# Browser debugging
os.environ["NOVA_ACT_BROWSER_ARGS"] = "--remote-debugging-port=9222"
