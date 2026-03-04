"""
Focused test: Console errors when switching between bank accounts.
Monitors Alpine/Livewire errors during account card clicks.
"""
import sys
import os

os.environ["PYTHONIOENCODING"] = "utf-8"
sys.stdout.reconfigure(encoding='utf-8', errors='replace')
sys.stderr.reconfigure(encoding='utf-8', errors='replace')

from playwright.sync_api import sync_playwright

BASE_URL = "http://127.0.0.1:8000"

# Known/expected errors to ignore (TallStackUI theme-switch)
IGNORED = ['darkTheme is not defined']

def main():
    page_errors = []
    console_errors = []

    def on_page_error(err):
        s = str(err)
        if not any(ign in s for ign in IGNORED):
            page_errors.append(s)

    def on_console(msg):
        if msg.type == 'error':
            t = msg.text
            if not any(ign in t for ign in IGNORED):
                console_errors.append(t)

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_context(viewport={"width": 1440, "height": 900}).new_page()
        page.on("pageerror", on_page_error)
        page.on("console", on_console)

        # Login
        page.goto(f"{BASE_URL}/login", wait_until="domcontentloaded", timeout=60000)
        page.wait_for_load_state("networkidle", timeout=30000)
        page.fill('input[type="email"]', "admin@gmail.com")
        page.fill('input[type="password"]', "password")
        page.click('button[type="submit"]')
        page.wait_for_url("**/dashboard**", timeout=30000)
        print("[OK] Logged in")

        # Go to bank accounts
        page.goto(f"{BASE_URL}/bank-accounts", wait_until="domcontentloaded", timeout=60000)
        page.wait_for_load_state("networkidle", timeout=30000)
        page.wait_for_timeout(4000)
        print("[OK] Bank accounts page loaded")

        # Clear errors from initial load
        page_errors.clear()
        console_errors.clear()

        # Find all selectAccount buttons (both mobile horizontal scroll + desktop sidebar)
        account_cards = page.locator("button[wire\\:click*='selectAccount']")
        card_count = account_cards.count()
        print(f"[INFO] selectAccount buttons total: {card_count}")

        # Filter to only visible ones
        visible_cards = []
        for i in range(card_count):
            if account_cards.nth(i).is_visible():
                visible_cards.append(i)
        card_count = len(visible_cards)
        print(f"[INFO] Visible selectAccount buttons: {card_count}")

        print(f"[INFO] Found {card_count} clickable account element(s)")

        # Click each account card multiple times to trigger morphing
        for round_num in range(3):
            print(f"\n--- Round {round_num + 1}: Clicking account cards ---")
            errors_before = len(page_errors) + len(console_errors)

            for idx in visible_cards[:3]:
                card = account_cards.nth(idx)
                card.click()
                page.wait_for_timeout(3000)  # Wait for Livewire morph
                print(f"  Clicked card {idx}, errors so far: page={len(page_errors)} console={len(console_errors)}")

            errors_after = len(page_errors) + len(console_errors)
            new_errors = errors_after - errors_before
            if new_errors > 0:
                print(f"  [WARN] {new_errors} new error(s) in round {round_num + 1}")
            else:
                print(f"  [OK] No new errors in round {round_num + 1}")

        # Summary
        print("\n" + "=" * 60)
        print("ACCOUNT SWITCH ERROR SUMMARY")
        print("=" * 60)

        if page_errors:
            print(f"\nPage Errors ({len(page_errors)}):")
            seen = set()
            for e in page_errors:
                short = e[:120]
                if short not in seen:
                    seen.add(short)
                    print(f"  [x] {short}")
        else:
            print("\n[OK] No page errors")

        if console_errors:
            print(f"\nConsole Errors ({len(console_errors)}):")
            seen = set()
            for e in console_errors:
                short = e[:120]
                if short not in seen:
                    seen.add(short)
                    print(f"  [x] {short}")
        else:
            print("[OK] No console errors")

        total = len(page_errors) + len(console_errors)
        print(f"\nTotal errors: {total}")

        browser.close()

    return 0 if total == 0 else 1

if __name__ == "__main__":
    sys.exit(main())
