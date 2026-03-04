"""
Bank Accounts — Console Error Monitoring During Navigation & Account Switching
Tests for JS errors when:
1. Navigating from other pages to bank-accounts
2. Switching between accounts
3. Navigating away and back
4. Rapid account switching
"""
import sys
import os
import time

os.environ["PYTHONIOENCODING"] = "utf-8"
sys.stdout.reconfigure(encoding='utf-8', errors='replace')
sys.stderr.reconfigure(encoding='utf-8', errors='replace')

from playwright.sync_api import sync_playwright

BASE_URL = "http://127.0.0.1:8000"
SCREENSHOTS_DIR = "/tmp/bank_accounts_console"

# Collect all console messages
console_errors = []
console_warnings = []
page_errors = []

# Known/expected errors to ignore
IGNORED_ERRORS = [
    'darkTheme is not defined',  # TallStackUI theme-switch component
    'leaving is not defined',     # TallStackUI modal transition
    'show is not defined',        # TallStackUI modal transition
]

def is_ignored(msg):
    return any(ignored in str(msg) for ignored in IGNORED_ERRORS)

def setup_console_capture(page, label):
    """Set up console capture with a label for the test phase."""
    def on_console(msg):
        text = msg.text
        if msg.type == 'error' and not is_ignored(text):
            console_errors.append(f"[{label}] {text}")
        elif msg.type == 'warning':
            console_warnings.append(f"[{label}] {text}")

    def on_page_error(err):
        err_str = str(err)
        if not is_ignored(err_str):
            page_errors.append(f"[{label}] {err_str}")

    page.on("console", on_console)
    page.on("pageerror", on_page_error)

def clear_listeners(page):
    """Remove existing listeners."""
    page.remove_listener("console", lambda m: None)
    page.remove_listener("pageerror", lambda e: None)

def login(page):
    page.goto(f"{BASE_URL}/login", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    page.fill('input[type="email"]', "admin@gmail.com")
    page.fill('input[type="password"]', "password")
    page.click('button[type="submit"]')
    page.wait_for_url("**/dashboard**", timeout=30000)
    print("[OK] Logged in")

def wait_for_livewire(page, ms=3000):
    """Wait for Livewire to finish loading."""
    page.wait_for_timeout(ms)

def test_navigate_dashboard_to_bank_accounts(page):
    """Test 1: Navigate from Dashboard to Bank Accounts via sidebar."""
    print("\n=== Test 1: Dashboard -> Bank Accounts ===")
    console_errors.clear()
    page_errors.clear()

    setup_console_capture(page, "dashboard->bank")

    # Start at dashboard
    page.goto(f"{BASE_URL}/dashboard", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    wait_for_livewire(page, 2000)

    # Navigate to Bank Accounts via sidebar link
    bank_link = page.locator("a[href*='bank-accounts']").first
    if bank_link.is_visible():
        bank_link.click()
        page.wait_for_load_state("networkidle", timeout=30000)
        wait_for_livewire(page, 4000)

        page.screenshot(path=f"{SCREENSHOTS_DIR}/01_dashboard_to_bank.png", full_page=True)

        errors = [e for e in page_errors if "dashboard->bank" in e]
        if errors:
            print(f"  [ERROR] Page errors during navigation:")
            for e in errors:
                print(f"    - {e}")
        else:
            print("  [OK] No page errors during navigation")
    else:
        print("  [WARN] Bank accounts link not found in sidebar")

    print("[DONE] Test 1")

def test_navigate_invoices_to_bank_accounts(page):
    """Test 2: Navigate from Invoices to Bank Accounts."""
    print("\n=== Test 2: Invoices -> Bank Accounts ===")
    console_errors.clear()
    page_errors.clear()

    setup_console_capture(page, "invoices->bank")

    # Go to invoices first
    page.goto(f"{BASE_URL}/invoices", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    wait_for_livewire(page, 2000)

    # Navigate to Bank Accounts
    bank_link = page.locator("a[href*='bank-accounts']").first
    if bank_link.is_visible():
        bank_link.click()
        page.wait_for_load_state("networkidle", timeout=30000)
        wait_for_livewire(page, 4000)

        page.screenshot(path=f"{SCREENSHOTS_DIR}/02_invoices_to_bank.png", full_page=True)

        # Check for canvas (charts should render)
        canvases = page.locator("canvas")
        print(f"  [INFO] Canvas elements after navigation: {canvases.count()}")

        errors = [e for e in page_errors if "invoices->bank" in e]
        if errors:
            print(f"  [ERROR] Page errors:")
            for e in errors:
                print(f"    - {e}")
        else:
            print("  [OK] No page errors")
    else:
        print("  [WARN] Bank accounts link not found")

    print("[DONE] Test 2")

def test_account_switching(page):
    """Test 3: Switch between accounts (if multiple exist, or click same account)."""
    print("\n=== Test 3: Account Switching ===")
    console_errors.clear()
    page_errors.clear()

    setup_console_capture(page, "switch")

    # Make sure we're on bank accounts
    page.goto(f"{BASE_URL}/bank-accounts", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    wait_for_livewire(page, 4000)

    # Find account items in sidebar
    sidebar = page.locator(".lg\\:col-span-3")
    account_buttons = sidebar.locator("button[wire\\:click*='selectAccount']")
    account_count = account_buttons.count()
    print(f"  [INFO] Found {account_count} account button(s)")

    if account_count > 0:
        # Click account multiple times (simulate rapid switching)
        for i in range(3):
            account_buttons.first.click()
            page.wait_for_timeout(2000)
            print(f"  [OK] Click {i+1}: Account selected")

        page.screenshot(path=f"{SCREENSHOTS_DIR}/03_account_switch.png", full_page=True)

        # Check canvas after switching
        canvases = page.locator("canvas")
        print(f"  [INFO] Canvas elements after switching: {canvases.count()}")

        errors = [e for e in page_errors if "switch" in e]
        if errors:
            print(f"  [ERROR] Page errors during switching:")
            for e in errors:
                print(f"    - {e}")
        else:
            print("  [OK] No page errors during switching")

    print("[DONE] Test 3")

def test_navigate_away_and_back(page):
    """Test 4: Navigate away from bank accounts and come back."""
    print("\n=== Test 4: Bank Accounts -> Other Page -> Bank Accounts ===")
    console_errors.clear()
    page_errors.clear()

    setup_console_capture(page, "away-back")

    # Start at bank accounts
    page.goto(f"{BASE_URL}/bank-accounts", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    wait_for_livewire(page, 4000)
    print("  [OK] Bank accounts loaded initially")

    # Navigate to dashboard
    dashboard_link = page.locator("a[href*='dashboard']").first
    if dashboard_link.is_visible():
        dashboard_link.click()
        page.wait_for_load_state("networkidle", timeout=30000)
        wait_for_livewire(page, 2000)
        print("  [OK] Navigated to dashboard")

    # Navigate back to bank accounts
    bank_link = page.locator("a[href*='bank-accounts']").first
    if bank_link.is_visible():
        bank_link.click()
        page.wait_for_load_state("networkidle", timeout=30000)
        wait_for_livewire(page, 5000)
        print("  [OK] Navigated back to bank accounts")

        page.screenshot(path=f"{SCREENSHOTS_DIR}/04_back_to_bank.png", full_page=True)

        # Check charts render on return
        canvases = page.locator("canvas")
        print(f"  [INFO] Canvas elements on return: {canvases.count()}")

        errors = [e for e in page_errors if "away-back" in e]
        if errors:
            print(f"  [ERROR] Page errors on return:")
            for e in errors:
                print(f"    - {e}")
        else:
            print("  [OK] No page errors on return navigation")

    print("[DONE] Test 4")

def test_rapid_tab_switching(page):
    """Test 5: Rapidly switch between Transactions and Payments tabs."""
    print("\n=== Test 5: Rapid Tab Switching ===")
    console_errors.clear()
    page_errors.clear()

    setup_console_capture(page, "rapid-tabs")

    page.goto(f"{BASE_URL}/bank-accounts", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    wait_for_livewire(page, 4000)

    trx_tab = page.locator("button[\\@click=\"activeTab = 'transactions'\"]")
    pmt_tab = page.locator("button[\\@click=\"activeTab = 'payments'\"]")

    if trx_tab.count() > 0 and pmt_tab.count() > 0:
        for i in range(5):
            pmt_tab.first.click()
            page.wait_for_timeout(500)
            trx_tab.first.click()
            page.wait_for_timeout(500)
        print(f"  [OK] Rapidly switched tabs 5 times")

        page.wait_for_timeout(2000)
        page.screenshot(path=f"{SCREENSHOTS_DIR}/05_rapid_tabs.png", full_page=True)

        errors = [e for e in page_errors if "rapid-tabs" in e]
        if errors:
            print(f"  [ERROR] Page errors during rapid tab switching:")
            for e in errors:
                print(f"    - {e}")
        else:
            print("  [OK] No page errors during rapid tab switching")
    else:
        print("  [WARN] Tab buttons not found")

    print("[DONE] Test 5")

def test_navigate_back_charts_persist(page):
    """Test 6: After navigating back, check if bar chart actually rendered (not blank)."""
    print("\n=== Test 6: Chart Render Verification After Navigation ===")
    console_errors.clear()
    page_errors.clear()

    setup_console_capture(page, "chart-verify")

    # Navigate to clients
    page.goto(f"{BASE_URL}/clients", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    wait_for_livewire(page, 2000)
    print("  [OK] On clients page")

    # Navigate to bank accounts
    bank_link = page.locator("a[href*='bank-accounts']").first
    if bank_link.is_visible():
        bank_link.click()
        page.wait_for_load_state("networkidle", timeout=30000)
        wait_for_livewire(page, 5000)

        # Check if Chart.js is loaded
        chart_loaded = page.evaluate("typeof Chart !== 'undefined'")
        print(f"  [INFO] Chart.js loaded: {chart_loaded}")

        # Check canvas dimensions (rendered chart has non-zero dimensions)
        canvases = page.locator("canvas")
        canvas_count = canvases.count()
        print(f"  [INFO] Canvas count: {canvas_count}")

        for i in range(canvas_count):
            canvas = canvases.nth(i)
            if canvas.is_visible():
                box = canvas.bounding_box()
                if box:
                    has_content = box['width'] > 0 and box['height'] > 0
                    print(f"  [INFO] Canvas {i}: {box['width']}x{box['height']} — {'has content' if has_content else 'EMPTY'}")
                else:
                    print(f"  [WARN] Canvas {i}: no bounding box")

        page.screenshot(path=f"{SCREENSHOTS_DIR}/06_chart_verify.png", full_page=True)

        errors = [e for e in page_errors if "chart-verify" in e]
        if errors:
            print(f"  [ERROR] Page errors:")
            for e in errors:
                print(f"    - {e}")
        else:
            print("  [OK] No page errors")

    print("[DONE] Test 6")


def main():
    os.makedirs(SCREENSHOTS_DIR, exist_ok=True)

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={"width": 1440, "height": 900})
        page = context.new_page()

        try:
            login(page)

            tests = [
                ("Dashboard -> Bank Accounts", test_navigate_dashboard_to_bank_accounts),
                ("Invoices -> Bank Accounts", test_navigate_invoices_to_bank_accounts),
                ("Account Switching", test_account_switching),
                ("Navigate Away & Back", test_navigate_away_and_back),
                ("Rapid Tab Switching", test_rapid_tab_switching),
                ("Chart Render After Navigation", test_navigate_back_charts_persist),
            ]

            all_errors = []

            for name, test_fn in tests:
                try:
                    test_fn(page)
                except Exception as e:
                    print(f"[EXCEPTION] {name}: {e}")
                    page.screenshot(path=f"{SCREENSHOTS_DIR}/FAIL_{name.replace(' ', '_')}.png", full_page=True)

            # Final summary
            print("\n" + "=" * 70)
            print("CONSOLE ERROR SUMMARY")
            print("=" * 70)

            # Collect all unique non-ignored errors
            all_page_errors = [e for e in page_errors if not is_ignored(e)]
            all_console_errors = [e for e in console_errors if not is_ignored(e)]

            if all_page_errors:
                print(f"\nPage Errors ({len(all_page_errors)}):")
                for e in all_page_errors:
                    print(f"  [x] {e}")
            else:
                print("\n[v] No page errors (excluding known TallStackUI issues)")

            if all_console_errors:
                print(f"\nConsole Errors ({len(all_console_errors)}):")
                for e in all_console_errors:
                    print(f"  [x] {e}")
            else:
                print("[v] No console errors")

            if console_warnings:
                print(f"\nConsole Warnings ({len(console_warnings)}):")
                for w in console_warnings[:10]:  # Show max 10
                    print(f"  [!] {w}")
                if len(console_warnings) > 10:
                    print(f"  ... and {len(console_warnings) - 10} more")

            total_errors = len(all_page_errors) + len(all_console_errors)
            print(f"\nTotal actionable errors: {total_errors}")
            print(f"Screenshots saved to: {SCREENSHOTS_DIR}/")

        finally:
            browser.close()

    return 0 if total_errors == 0 else 1

if __name__ == "__main__":
    sys.exit(main())
