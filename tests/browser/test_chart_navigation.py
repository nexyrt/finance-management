"""
Focused test: Chart rendering after SPA navigation (wire:navigate).
Properly waits for URL changes during SPA navigation.
"""
import sys
import os

os.environ["PYTHONIOENCODING"] = "utf-8"
sys.stdout.reconfigure(encoding='utf-8', errors='replace')
sys.stderr.reconfigure(encoding='utf-8', errors='replace')

from playwright.sync_api import sync_playwright

BASE_URL = "http://127.0.0.1:8000"
SCREENSHOTS_DIR = "/tmp/bank_accounts_chart_test"

def login(page):
    page.goto(f"{BASE_URL}/login", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    page.fill('input[type="email"]', "admin@gmail.com")
    page.fill('input[type="password"]', "password")
    page.click('button[type="submit"]')
    page.wait_for_url("**/dashboard**", timeout=30000)
    print("[OK] Logged in")

def spa_navigate_to(page, href_contains, expected_url_part, timeout=15000):
    """SPA navigate by clicking a link and waiting for URL change."""
    link = page.locator(f"a[href*='{href_contains}']").first
    try:
        link.wait_for(state="visible", timeout=10000)
    except Exception:
        print(f"  [WARN] Link with href containing '{href_contains}' not visible after waiting")
        return False

    link.click()

    # Wait for URL to contain the expected part
    try:
        page.wait_for_url(f"**{expected_url_part}**", timeout=timeout)
    except Exception:
        print(f"  [WARN] URL didn't change to contain '{expected_url_part}', current: {page.url}")
        return False

    # Wait for Livewire to finish loading (wire:init + lazy components)
    page.wait_for_timeout(2000)
    return True

def wait_for_bank_accounts_ready(page, timeout=30000):
    """Wait until bank accounts page is fully loaded including lazy components."""
    try:
        # Wait for the chart title to appear (means lazy QuickActionsOverview loaded)
        page.locator("text='Pemasukan vs Pengeluaran'").wait_for(state="visible", timeout=timeout)
        # Give charts time to render
        page.wait_for_timeout(2000)
        return True
    except Exception:
        # Fallback: check if at least the main data loaded
        has_account = page.locator("text='Mandiri'").count() > 0
        if has_account:
            print(f"  [WARN] Page loaded but lazy chart component still loading after {timeout}ms")
            page.wait_for_timeout(5000)  # Extra grace period
            return True
        print(f"  [WARN] Bank accounts data didn't load within {timeout}ms")
        return False

def check_chart_state(page, label):
    """Check Chart.js and Alpine data state."""
    print(f"\n  --- {label} ---")
    print(f"  Current URL: {page.url}")

    chart_js = page.evaluate("typeof Chart !== 'undefined'")
    alpine_ok = page.evaluate("typeof Alpine !== 'undefined'")
    print(f"  Chart.js loaded: {chart_js}")
    print(f"  Alpine loaded: {alpine_ok}")

    # Check canvas elements
    canvases = page.locator("canvas")
    canvas_count = canvases.count()
    print(f"  Canvas elements: {canvas_count}")

    for i in range(canvas_count):
        canvas = canvases.nth(i)
        if canvas.is_visible():
            box = canvas.bounding_box()
            if box:
                print(f"  Canvas {i}: {box['width']:.0f}x{box['height']:.0f}")

    # Check for Alpine x-data bankAccountCharts elements
    ba_elements = page.evaluate("""
        document.querySelectorAll('[x-data*="bankAccountCharts"]').length
    """)
    print(f"  Elements with x-data bankAccountCharts: {ba_elements}")

    # Check for the specific chart titles to confirm we're on bank accounts page
    has_income_title = page.locator("text='Pemasukan vs Pengeluaran'").count() > 0
    print(f"  Income vs Expense title visible: {has_income_title}")

    return canvas_count, ba_elements

def main():
    os.makedirs(SCREENSHOTS_DIR, exist_ok=True)

    errors = []

    def on_error(err):
        err_str = str(err)
        if 'darkTheme' not in err_str and 'leaving' not in err_str and 'show is not' not in err_str:
            errors.append(err_str)
            print(f"  [JS ERROR] {err_str}")

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={"width": 1440, "height": 900})
        page = context.new_page()
        page.on("pageerror", on_error)

        login(page)

        # === Scenario A: Direct page load ===
        print("\n=== Scenario A: Direct page load ===")
        page.goto(f"{BASE_URL}/bank-accounts", wait_until="domcontentloaded", timeout=30000)
        page.wait_for_load_state("networkidle", timeout=30000)
        wait_for_bank_accounts_ready(page)
        canvasA, elementsA = check_chart_state(page, "Direct load")
        page.screenshot(path=f"{SCREENSHOTS_DIR}/A_direct_load.png", full_page=True)

        # === Scenario B: SPA to dashboard, then back to bank-accounts ===
        print("\n=== Scenario B: Bank Accounts -> Dashboard -> Bank Accounts (SPA) ===")
        ok = spa_navigate_to(page, "dashboard", "/dashboard")
        if ok:
            print(f"  [OK] Navigated to dashboard: {page.url}")
            ok2 = spa_navigate_to(page, "bank-accounts", "/bank-accounts")
            if ok2:
                print(f"  [OK] Navigated back to bank-accounts: {page.url}")
                wait_for_bank_accounts_ready(page)
                canvasB, elementsB = check_chart_state(page, "After SPA return from dashboard")
                page.screenshot(path=f"{SCREENSHOTS_DIR}/B_spa_return.png", full_page=True)
            else:
                canvasB, elementsB = 0, 0
        else:
            canvasB, elementsB = 0, 0

        # === Scenario C: SPA to clients, then to bank-accounts ===
        print("\n=== Scenario C: Bank Accounts -> Clients -> Bank Accounts (SPA) ===")
        ok = spa_navigate_to(page, "clients", "/clients")
        if ok:
            print(f"  [OK] Navigated to clients: {page.url}")
            page.wait_for_timeout(2000)
            ok2 = spa_navigate_to(page, "bank-accounts", "/bank-accounts")
            if ok2:
                print(f"  [OK] Navigated to bank-accounts: {page.url}")
                wait_for_bank_accounts_ready(page)
                canvasC, elementsC = check_chart_state(page, "After SPA from clients")
                page.screenshot(path=f"{SCREENSHOTS_DIR}/C_spa_from_clients.png", full_page=True)
            else:
                canvasC, elementsC = 0, 0
        else:
            canvasC, elementsC = 0, 0

        # === Scenario D: Full page reload after SPA (simulate browser refresh) ===
        print("\n=== Scenario D: Full page reload on bank-accounts ===")
        try:
            page.goto(f"{BASE_URL}/bank-accounts", wait_until="domcontentloaded", timeout=60000)
            page.wait_for_load_state("networkidle", timeout=30000)
            wait_for_bank_accounts_ready(page)
            canvasD, elementsD = check_chart_state(page, "After full reload")
            page.screenshot(path=f"{SCREENSHOTS_DIR}/D_full_reload.png", full_page=True)
        except Exception as e:
            print(f"  [ERROR] Scenario D failed: {e}")
            canvasD, elementsD = -1, -1

        # === Summary ===
        print("\n" + "=" * 60)
        print("CHART NAVIGATION TEST SUMMARY")
        print("=" * 60)
        scenarios = [
            ("A - Direct load", canvasA, elementsA),
            ("B - SPA dashboard->bank", canvasB, elementsB),
            ("C - SPA clients->bank", canvasC, elementsC),
            ("D - Full reload", canvasD, elementsD),
        ]

        all_ok = True
        for name, canvases, elements in scenarios:
            status = "OK" if canvases >= 1 else "FAIL"
            if status == "FAIL":
                all_ok = False
            print(f"  [{status}] {name}: {canvases} canvas, {elements} x-data elements")

        print(f"\n  JS Errors: {len(errors)}")
        for e in errors:
            print(f"    - {e}")

        if all_ok:
            print("\n  [PASS] Charts render correctly in all scenarios!")
        else:
            print("\n  [FAIL] Charts missing in some scenarios")

        browser.close()

    return 0 if all_ok and len(errors) == 0 else 1

if __name__ == "__main__":
    sys.exit(main())
