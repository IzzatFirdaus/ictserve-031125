"""
Nova Act Tasks for ICTServe Automation
Based on official Nova Act template pattern
"""

from nova_act import NovaAct
from nova_act_config import BASE_URL, HELPDESK_URL, LOAN_URL, ADMIN_URL


def test_helpdesk_submission():
    """Test helpdesk ticket submission"""
    print("[1/4] Initializing Nova Act for helpdesk test...")
    nova = NovaAct(starting_page=HELPDESK_URL, headless=False, ignore_https_errors=True)
    
    print("[2/4] Starting browser...")
    nova.start()
    
    print("[3/4] Testing helpdesk form...")
    result = nova.act("""
        Fill in the helpdesk form with test data:
        - Name: Test User
        - Email: test@example.com
        - Subject: Test Ticket
        - Description: This is a test ticket
        Then submit the form and verify success message.
    """)
    
    print("[4/4] Test completed!")
    print(f"Result: {result}")
    
    # Keep browser open for inspection
    # nova.stop()
    return result


def test_loan_application():
    """Test asset loan application"""
    print("[1/4] Initializing Nova Act for loan test...")
    nova = NovaAct(starting_page=LOAN_URL, headless=False, ignore_https_errors=True)
    
    print("[2/4] Starting browser...")
    nova.start()
    
    print("[3/4] Testing loan application...")
    result = nova.act("""
        Fill in the loan application form with test data:
        - Name: Test User
        - Email: test@example.com
        - Asset: Select first available asset
        - Purpose: Testing
        Then submit and verify success.
    """)
    
    print("[4/4] Test completed!")
    print(f"Result: {result}")
    
    # Keep browser open
    # nova.stop()
    return result


def test_accessibility():
    """Test WCAG 2.2 AA accessibility compliance"""
    print("[1/4] Initializing Nova Act for accessibility test...")
    nova = NovaAct(starting_page=BASE_URL, headless=False, ignore_https_errors=True)
    
    print("[2/4] Starting browser...")
    nova.start()
    
    print("[3/4] Checking accessibility...")
    result = nova.act("""
        Check this page for accessibility issues:
        - Verify all images have alt text
        - Check color contrast ratios
        - Test keyboard navigation
        - Verify ARIA labels
        Report any WCAG 2.2 AA violations found.
    """)
    
    print("[4/4] Test completed!")
    print(f"Result: {result}")
    
    # Keep browser open
    # nova.stop()
    return result


def test_bilingual_content():
    """Test bilingual (MS/EN) content switching"""
    print("[1/4] Initializing Nova Act for bilingual test...")
    nova = NovaAct(starting_page=BASE_URL, headless=False, ignore_https_errors=True)
    
    print("[2/4] Starting browser...")
    nova.start()
    
    print("[3/4] Testing language switching...")
    result = nova.act("""
        Test bilingual functionality:
        1. Find and click language switcher
        2. Switch to Bahasa Melayu
        3. Verify content is in Malay
        4. Switch to English
        5. Verify content is in English
        Report if both languages work correctly.
    """)
    
    print("[4/4] Test completed!")
    print(f"Result: {result}")
    
    # Keep browser open
    # nova.stop()
    return result


if __name__ == "__main__":
    print("=== ICTServe Nova Act Test Suite ===\n")
    
    # Run tests
    print("\n--- Test 1: Helpdesk Submission ---")
    test_helpdesk_submission()
    
    print("\n--- Test 2: Loan Application ---")
    test_loan_application()
    
    print("\n--- Test 3: Accessibility ---")
    test_accessibility()
    
    print("\n--- Test 4: Bilingual Content ---")
    test_bilingual_content()
    
    print("\n=== All Tests Completed ===")
