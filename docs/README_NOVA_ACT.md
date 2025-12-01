# Nova Act Integration - ICTServe

## Setup Complete

Nova Act v2.3.18.0 is installed with Playwright browsers.

## Authentication Required

Nova Act requires authentication to run. Choose one option:

### Option 1: Nova Act API Key (Recommended)

1. Get API key from: <https://nova.amazon.com/dev-apis>
2. Set environment variable:

   ```bash
   set NOVA_ACT_API_KEY=your_key
   ```

### Option 2: AWS Credentials

```bash
set AWS_ACCESS_KEY_ID=your_key
set AWS_SECRET_ACCESS_KEY=your_secret
set AWS_REGION=us-east-1
```

## Verify Installation

```bash
python nova_act_simple_test.py
```

## Usage (After Authentication)

```bash
python nova_act_tasks.py
```

### Available Tasks

```python
from nova_act_tasks import ICTServeAgent

agent = ICTServeAgent()

# Test helpdesk submission
await agent.test_helpdesk_submission()

# Test loan application
await agent.test_loan_application()

# Test accessibility (WCAG 2.2 AA)
await agent.test_accessibility()

# Test bilingual content (MS/EN)
await agent.test_bilingual_content()
```

## Configuration

Edit `nova_act_config.py`:

- Browser settings (chromium/firefox/webkit)
- Screenshot paths
- Log paths
- Base URLs

## Storage

- Screenshots: `storage/nova-act/screenshots/`
- Logs: `storage/nova-act/logs/`

## Files Created

- `nova_act_config.py` - Configuration
- `nova_act_tasks.py` - Automation tasks
- `nova_act_simple_test.py` - Installation verification
- `.env.nova` - Environment template
- `README_NOVA_ACT.md` - This file

## Next Steps

1. Obtain Nova Act API key
2. Set environment variable
3. Run verification: `python nova_act_simple_test.py`
4. Run tests: `python nova_act_tasks.py`
