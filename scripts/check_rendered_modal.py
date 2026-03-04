"""Get rendered HTML of invoices page and check sendModal entangle."""
from playwright.sync_api import sync_playwright
import re

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1280, "height": 900})
    page.goto("http://localhost:8000/login")
    page.wait_for_load_state("networkidle")
    page.fill("#email", "admin@gmail.com")
    page.fill("#password", "password")
    page.click("button[type=submit]")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(2000)
    page.goto("http://localhost:8000/invoices")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(3000)

    # Get full page HTML and search for sendModal
    html = page.content()

    # Find sendModal references
    lines = html.split("\n")
    for i, line in enumerate(lines):
        if "sendModal" in line or "entangle" in line.lower():
            ctx = lines[max(0, i-1):i+2]
            for l in ctx:
                if l.strip():
                    print(l.strip()[:120])
            print("---")

    browser.close()
