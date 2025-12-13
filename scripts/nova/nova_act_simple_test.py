# -*- coding: utf-8 -*-
"""
Simple Nova Act Test - Verify Installation
"""

import sys

try:
    from nova_act import NovaAct
    print("[OK] NovaAct imported successfully")
    print(f"Available classes: {dir(NovaAct)[:10]}")
    
    # Check if AWS credentials are configured
    import os
    has_aws_key = bool(os.getenv('AWS_ACCESS_KEY_ID'))
    has_nova_key = bool(os.getenv('NOVA_ACT_API_KEY'))
    
    print(f"\nConfiguration Status:")
    print(f"  AWS_ACCESS_KEY_ID: {'[OK] Set' if has_aws_key else '[X] Not set'}")
    print(f"  NOVA_ACT_API_KEY: {'[OK] Set' if has_nova_key else '[X] Not set'}")
    
    if not has_aws_key and not has_nova_key:
        print("\n[!] Authentication Required:")
        print("  1. Get API key from: https://nova.amazon.com/dev-apis")
        print("  2. Set environment variable:")
        print("     set NOVA_ACT_API_KEY=your_key")
        print("  OR")
        print("     set AWS_ACCESS_KEY_ID=your_key")
        print("     set AWS_SECRET_ACCESS_KEY=your_secret")
        sys.exit(1)
    
    print("\n[OK] Nova Act is ready to use!")
    
except ImportError as e:
    print(f"[X] Import error: {e}")
    sys.exit(1)
except Exception as e:
    print(f"[X] Error: {e}")
    sys.exit(1)
