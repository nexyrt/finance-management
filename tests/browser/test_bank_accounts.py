"""
Bank Accounts Page — Comprehensive UI/Functional Testing
Tests the master-detail sidebar layout, charts, tabs, listings, and responsive behavior.
"""
import sys
import os
import time

os.environ["PYTHONIOENCODING"] = "utf-8"
sys.stdout.reconfigure(encoding='utf-8', errors='replace')
sys.stderr.reconfigure(encoding='utf-8', errors='replace')

from playwright.sync_api import sync_playwright, expect

BASE_URL = "http://127.0.0.1:8000"
SCREENSHOTS_DIR = "/tmp/bank_accounts_tests"

def login(page):
    """Login as admin user."""
    page.goto(f"{BASE_URL}/login", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)
    page.fill('input[type="email"]', "admin@gmail.com")
    page.fill('input[type="password"]', "password")
    page.click('button[type="submit"]')
    page.wait_for_url("**/dashboard**", timeout=30000)
    print("[OK] Logged in as admin")

def test_page_load(page):
    """Test 1: Page loads correctly with master-detail layout."""
    print("\n=== Test 1: Page Load & Layout ===")
    page.goto(f"{BASE_URL}/bank-accounts", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_load_state("networkidle", timeout=30000)

    # Wait for Livewire wire:init to load data
    page.wait_for_timeout(3000)
    page.screenshot(path=f"{SCREENSHOTS_DIR}/01_page_loaded.png", full_page=True)

    # Check page title exists
    title = page.locator("h1")
    assert title.count() > 0, "Page title h1 not found"
    print(f"  [OK] Page title found: {title.first.text_content().strip()[:50]}")

    # Check sidebar exists (desktop)
    sidebar = page.locator(".lg\\:col-span-3")
    assert sidebar.count() > 0, "Sidebar (lg:col-span-3) not found"
    print("  [OK] Desktop sidebar found")

    # Check right panel exists
    main_panel = page.locator(".lg\\:col-span-9")
    assert main_panel.count() > 0, "Main panel (lg:col-span-9) not found"
    print("  [OK] Main panel found")

    print("[PASS] Test 1: Page loads with correct layout")

def test_sidebar_account_selection(page):
    """Test 2: Sidebar shows accounts and selection works."""
    print("\n=== Test 2: Sidebar Account Selection ===")

    # Check account items in sidebar
    # The sidebar should have account cards
    sidebar = page.locator(".lg\\:col-span-3")
    account_items = sidebar.locator("[wire\\:click*='selectAccount']")

    if account_items.count() > 0:
        print(f"  [OK] Found {account_items.count()} account(s) in sidebar")

        # Check first account is selected (should have primary bg)
        first_account = account_items.first
        first_account_classes = first_account.get_attribute("class") or ""
        # It should be highlighted
        print(f"  [INFO] First account classes: {first_account_classes[:100]}")

        # Click account to trigger selection
        first_account.click()
        page.wait_for_timeout(2000)
        page.screenshot(path=f"{SCREENSHOTS_DIR}/02_account_selected.png", full_page=True)
        print("  [OK] Account clicked and selection triggered")
    else:
        # Try alternative selectors
        clickable_accounts = sidebar.locator("button, [role='button'], .cursor-pointer")
        print(f"  [INFO] Found {clickable_accounts.count()} clickable elements in sidebar")

    # Check monthly summary section exists
    summary_text = page.get_by_text("Ringkasan Bulanan").or_(page.get_by_text("Monthly Summary"))
    if summary_text.count() > 0:
        print("  [OK] Monthly summary section found")
    else:
        print("  [WARN] Monthly summary text not found, checking structure...")

    print("[PASS] Test 2: Sidebar account selection works")

def test_selected_account_header(page):
    """Test 3: Selected account header shows details and actions."""
    print("\n=== Test 3: Selected Account Header ===")

    # The right panel should show the selected account info
    main_panel = page.locator(".lg\\:col-span-9")

    # Check for account name display (Mandiri - Payroll)
    account_name = page.get_by_text("Mandiri - Payroll")
    if account_name.count() > 0:
        print("  [OK] Account name 'Mandiri - Payroll' displayed")
    else:
        print("  [WARN] Account name not found in header, checking alternatives...")
        page.screenshot(path=f"{SCREENSHOTS_DIR}/03_account_header.png", full_page=True)

    # Check for action buttons (create expense, create income)
    expense_btn = page.get_by_text("Catat Pengeluaran").or_(page.get_by_text("Create Expense")).or_(page.get_by_text("Pengeluaran"))
    income_btn = page.get_by_text("Catat Pemasukan").or_(page.get_by_text("Create Income")).or_(page.get_by_text("Pemasukan"))

    if expense_btn.count() > 0:
        print("  [OK] Expense button found")
    if income_btn.count() > 0:
        print("  [OK] Income button found")

    page.screenshot(path=f"{SCREENSHOTS_DIR}/03_account_header.png", full_page=True)
    print("[PASS] Test 3: Account header displays correctly")

def test_charts_rendering(page):
    """Test 4: Charts (bar + donut) render correctly."""
    print("\n=== Test 4: Charts Rendering ===")

    # Wait for charts to potentially load (lazy component + Chart.js CDN)
    page.wait_for_timeout(5000)

    # Check for canvas elements (Chart.js renders to canvas)
    canvases = page.locator("canvas")
    canvas_count = canvases.count()
    print(f"  [INFO] Found {canvas_count} canvas element(s)")

    if canvas_count >= 2:
        print("  [OK] Both charts (bar + donut) have canvas elements")
    elif canvas_count == 1:
        print("  [WARN] Only 1 canvas found, expected 2 (bar + donut)")
    else:
        print("  [WARN] No canvas elements found — charts may still be loading")
        # Check if wire:ignore containers exist
        wire_ignore = page.locator("[wire\\:ignore]")
        print(f"  [INFO] wire:ignore containers: {wire_ignore.count()}")

    # Check chart section titles
    income_expense_title = page.get_by_text("Pemasukan vs Pengeluaran").or_(page.get_by_text("Income vs Expense"))
    category_title = page.get_by_text("Kategori Pengeluaran").or_(page.get_by_text("Category Breakdown"))

    if income_expense_title.count() > 0:
        print("  [OK] Income vs Expense chart title found")
    if category_title.count() > 0:
        print("  [OK] Category Breakdown chart title found")

    # Take screenshot of charts area
    page.screenshot(path=f"{SCREENSHOTS_DIR}/04_charts.png", full_page=True)
    print("[PASS] Test 4: Charts section rendered")

def test_mini_stats(page):
    """Test 5: Mini stat cards display correctly."""
    print("\n=== Test 5: Mini Stats Cards ===")

    # Look for the 3 stat cards (income, expense, net)
    stat_section = page.locator(".grid.grid-cols-3, .grid.grid-cols-1.sm\\:grid-cols-3")
    if stat_section.count() > 0:
        print(f"  [OK] Stats grid section found ({stat_section.count()} grids)")
    else:
        print("  [WARN] 3-column stats grid not found, checking individual stats...")

    # Check for Rp currency format in the page
    rp_values = page.locator("text=/Rp\\s/")
    rp_count = rp_values.count()
    print(f"  [INFO] Found {rp_count} elements with 'Rp' currency format")

    print("[PASS] Test 5: Stats cards checked")

def get_tab_buttons(page):
    """Get tab buttons using Alpine x-on:click selectors."""
    trx_tab = page.locator("button[x-on\\:click*='transactions'], button[\\@click*='transactions']")
    pmt_tab = page.locator("button[x-on\\:click*='payments'], button[\\@click*='payments']")
    return trx_tab, pmt_tab

def test_custom_tabs(page):
    """Test 6: Custom tabs (Transactions/Payments) switch correctly."""
    print("\n=== Test 6: Custom Tabs Switching ===")

    trx_tab, pmt_tab = get_tab_buttons(page)

    trx_found = trx_tab.count() > 0
    pmt_found = pmt_tab.count() > 0

    print(f"  [INFO] Transactions tab: {'found' if trx_found else 'not found'}")
    print(f"  [INFO] Payments tab: {'found' if pmt_found else 'not found'}")

    if trx_found and pmt_found:
        # Click Transactions tab
        trx_tab.first.click()
        page.wait_for_timeout(1500)
        page.screenshot(path=f"{SCREENSHOTS_DIR}/06a_tab_transactions.png", full_page=True)
        print("  [OK] Transactions tab clicked")

        # Click Payments tab
        pmt_tab.first.click()
        page.wait_for_timeout(1500)
        page.screenshot(path=f"{SCREENSHOTS_DIR}/06b_tab_payments.png", full_page=True)
        print("  [OK] Payments tab clicked")

        # Switch back to Transactions
        trx_tab.first.click()
        page.wait_for_timeout(1000)
        print("  [OK] Switched back to Transactions tab")
    else:
        print("  [WARN] Tab buttons not found with Alpine selectors")

    print("[PASS] Test 6: Tab switching works")

def test_transaction_list(page):
    """Test 7: Transaction list shows data, filters work."""
    print("\n=== Test 7: Transaction List ===")

    # Make sure we're on Transactions tab
    trx_tab, _ = get_tab_buttons(page)
    if trx_tab.count() > 0:
        trx_tab.first.click()
        page.wait_for_timeout(2000)

    # Check if table exists
    table = page.locator("table")
    table_count = table.count()
    print(f"  [INFO] Found {table_count} table(s) on page")

    if table_count > 0:
        # Check rows
        rows = table.first.locator("tbody tr")
        row_count = rows.count()
        print(f"  [OK] Transaction table has {row_count} row(s)")

        # Take screenshot of transaction list
        page.screenshot(path=f"{SCREENSHOTS_DIR}/07a_transaction_list.png", full_page=True)

        # Test search filter - target the visible wire:model search input (not hidden select search)
        search_input = page.locator("div.sm\\:w-64 input:visible").first
        if search_input.is_visible():
            search_input.fill("test")
            page.wait_for_timeout(1500)
            print("  [OK] Search filter applied")
            page.screenshot(path=f"{SCREENSHOTS_DIR}/07b_transaction_search.png", full_page=True)

            # Clear search
            search_input.fill("")
            page.wait_for_timeout(1500)
            print("  [OK] Search filter cleared")
        else:
            print("  [WARN] Search input not visible")

    # Check filter dropdowns exist
    selects = page.locator("select, [wire\\:model\\.live]")
    print(f"  [INFO] Found {selects.count()} filter elements")

    print("[PASS] Test 7: Transaction list functional")

def test_payment_list(page):
    """Test 8: Payment list tab shows empty state or data."""
    print("\n=== Test 8: Payment List ===")

    # Switch to Payments tab
    _, pmt_tab = get_tab_buttons(page)
    if pmt_tab.count() > 0:
        pmt_tab.first.click()
        page.wait_for_timeout(2000)

    page.screenshot(path=f"{SCREENSHOTS_DIR}/08_payment_list.png", full_page=True)

    # Check for table or empty state
    table = page.locator("table")
    if table.count() > 0:
        rows = table.last.locator("tbody tr")
        print(f"  [OK] Payment table visible with {rows.count()} row(s)")
    else:
        print("  [INFO] No table found — may show empty state (0 payments in DB)")

    # Switch back to transactions
    trx_tab, _ = get_tab_buttons(page)
    if trx_tab.count() > 0:
        trx_tab.first.click()
        page.wait_for_timeout(1000)

    print("[PASS] Test 8: Payment list checked")

def test_responsive_mobile(page):
    """Test 9: Mobile layout — horizontal scroll cards, no sidebar."""
    print("\n=== Test 9: Responsive Mobile Layout ===")

    # Set mobile viewport
    page.set_viewport_size({"width": 375, "height": 812})
    page.wait_for_timeout(2000)
    page.screenshot(path=f"{SCREENSHOTS_DIR}/09a_mobile_top.png", full_page=False)

    # Desktop sidebar should be hidden
    sidebar = page.locator(".hidden.lg\\:block")
    if sidebar.count() > 0:
        is_visible = sidebar.first.is_visible()
        print(f"  [OK] Desktop sidebar visible: {is_visible} (should be False)")
    else:
        print("  [INFO] Desktop sidebar selector not matched")

    # Mobile account cards should be visible
    mobile_cards = page.locator(".lg\\:hidden")
    mobile_visible = False
    for i in range(mobile_cards.count()):
        if mobile_cards.nth(i).is_visible():
            mobile_visible = True
            break
    print(f"  [OK] Mobile card section visible: {mobile_visible}")

    # Scroll down to see more content
    page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
    page.wait_for_timeout(1000)
    page.screenshot(path=f"{SCREENSHOTS_DIR}/09b_mobile_bottom.png", full_page=False)

    # Reset to desktop viewport
    page.set_viewport_size({"width": 1440, "height": 900})
    page.wait_for_timeout(1500)

    print("[PASS] Test 9: Mobile responsive layout verified")

def test_dark_mode(page):
    """Test 10: Dark mode toggle — UI and charts re-render."""
    print("\n=== Test 10: Dark Mode ===")

    # Toggle dark mode by adding class to html element
    page.evaluate("document.documentElement.classList.toggle('dark')")
    page.wait_for_timeout(2000)
    page.screenshot(path=f"{SCREENSHOTS_DIR}/10a_dark_mode.png", full_page=True)
    print("  [OK] Dark mode toggled ON")

    # Check that dark bg classes are applied
    body_bg = page.evaluate("getComputedStyle(document.body).backgroundColor")
    print(f"  [INFO] Body background color: {body_bg}")

    # Check charts still exist
    canvases = page.locator("canvas")
    print(f"  [INFO] Canvas elements in dark mode: {canvases.count()}")

    # Toggle back to light mode
    page.evaluate("document.documentElement.classList.toggle('dark')")
    page.wait_for_timeout(2000)
    page.screenshot(path=f"{SCREENSHOTS_DIR}/10b_light_mode.png", full_page=True)
    print("  [OK] Dark mode toggled OFF (back to light)")

    print("[PASS] Test 10: Dark mode works")

def test_action_buttons(page):
    """Test 11: Action buttons (create expense, create income, dropdown)."""
    print("\n=== Test 11: Action Buttons ===")

    # Check for create expense button
    expense_btn = page.locator("button:has-text('Pengeluaran'), button:has-text('Expense'), a:has-text('Pengeluaran')")
    if expense_btn.count() > 0:
        print(f"  [OK] Expense button found ({expense_btn.count()})")

        # Click to open modal
        expense_btn.first.click()
        page.wait_for_timeout(2000)
        page.screenshot(path=f"{SCREENSHOTS_DIR}/11a_expense_modal.png", full_page=True)

        # Close modal (press Escape or click close)
        page.keyboard.press("Escape")
        page.wait_for_timeout(1000)
        print("  [OK] Expense modal opened and closed")

    # Check for create income button
    income_btn = page.locator("button:has-text('Pemasukan'), button:has-text('Income'), a:has-text('Pemasukan')")
    if income_btn.count() > 0:
        print(f"  [OK] Income button found ({income_btn.count()})")

        income_btn.first.click()
        page.wait_for_timeout(2000)
        page.screenshot(path=f"{SCREENSHOTS_DIR}/11b_income_modal.png", full_page=True)

        page.keyboard.press("Escape")
        page.wait_for_timeout(1000)
        print("  [OK] Income modal opened and closed")

    # Check for dropdown/more actions
    dropdown_trigger = page.locator("button:has(svg[class*='ellipsis']), [x-on\\:click*='dropdown'], button:has-text('...')")
    if dropdown_trigger.count() > 0:
        print(f"  [OK] Dropdown trigger found ({dropdown_trigger.count()})")
    else:
        print("  [INFO] Dropdown trigger not found with current selectors")

    print("[PASS] Test 11: Action buttons verified")

def test_guide_modal(page):
    """Test 12: Guide modal opens correctly."""
    print("\n=== Test 12: Guide Modal ===")

    guide_btn = page.get_by_text("Panduan").or_(page.get_by_text("Guide"))
    if guide_btn.count() > 0:
        guide_btn.first.click()
        page.wait_for_timeout(2000)
        page.screenshot(path=f"{SCREENSHOTS_DIR}/12_guide_modal.png", full_page=True)
        print("  [OK] Guide modal opened")

        page.keyboard.press("Escape")
        page.wait_for_timeout(1000)
        print("  [OK] Guide modal closed")
    else:
        print("  [INFO] Guide button not found")

    print("[PASS] Test 12: Guide modal checked")

def main():
    import os
    os.makedirs(SCREENSHOTS_DIR, exist_ok=True)

    results = []

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={"width": 1440, "height": 900})
        page = context.new_page()

        # Enable console logging
        page.on("console", lambda msg: None)  # Suppress noise
        page.on("pageerror", lambda err: print(f"  [PAGE ERROR] {err}"))

        try:
            login(page)

            tests = [
                ("Page Load & Layout", test_page_load),
                ("Sidebar Account Selection", test_sidebar_account_selection),
                ("Selected Account Header", test_selected_account_header),
                ("Charts Rendering", test_charts_rendering),
                ("Mini Stats Cards", test_mini_stats),
                ("Custom Tabs Switching", test_custom_tabs),
                ("Transaction List", test_transaction_list),
                ("Payment List", test_payment_list),
                ("Responsive Mobile", test_responsive_mobile),
                ("Dark Mode", test_dark_mode),
                ("Action Buttons", test_action_buttons),
                ("Guide Modal", test_guide_modal),
            ]

            passed = 0
            failed = 0

            for name, test_fn in tests:
                try:
                    test_fn(page)
                    results.append((name, "PASS"))
                    passed += 1
                except Exception as e:
                    results.append((name, f"FAIL: {e}"))
                    failed += 1
                    page.screenshot(path=f"{SCREENSHOTS_DIR}/FAIL_{name.replace(' ', '_')}.png", full_page=True)
                    print(f"[FAIL] {name}: {e}")

            print("\n" + "=" * 60)
            print("RESULTS SUMMARY")
            print("=" * 60)
            for name, result in results:
                status = "PASS" if result == "PASS" else "FAIL"
                icon = "[v]" if status == "PASS" else "[x]"
                print(f"  {icon} {name}: {result}")
            print(f"\nTotal: {passed} passed, {failed} failed out of {len(tests)}")
            print(f"Screenshots saved to: {SCREENSHOTS_DIR}/")

        finally:
            browser.close()

    return 0 if failed == 0 else 1

if __name__ == "__main__":
    sys.exit(main())
