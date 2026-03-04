"""Get exact xData of the sendModal modal element."""
from playwright.sync_api import sync_playwright

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

    # Find all modal elements and their full xData
    result = page.evaluate("""
        () => {
            const modals = document.querySelectorAll('[x-data*="tallstackui_modal"]');
            return Array.from(modals).map((el, i) => ({
                index: i,
                fullXData: el.getAttribute('x-data'),
                id: el.id,
                wireSnapshot: el.previousElementSibling
                    ? (el.previousElementSibling.getAttribute('wire:snapshot') || '').slice(0, 100)
                    : ''
            }));
        }
    """)
    print("All modal xData:")
    for m in result:
        print(f"  [{m['index']}] id={m['id']}")
        print(f"    xData={m['fullXData']}")
        print()

    browser.close()
