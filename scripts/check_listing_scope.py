"""Check if sendModal modal is inside invoices.listing Livewire scope."""
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

    result = page.evaluate("""
        () => {
            // Find invoices.listing Livewire root
            const snapshots = document.querySelectorAll('[wire\\\\:snapshot]');
            let listingEl = null;
            for (const el of snapshots) {
                try {
                    const snap = JSON.parse(el.getAttribute('wire:snapshot'));
                    if (snap.memo && snap.memo.name === 'invoices.listing') {
                        listingEl = el;
                        break;
                    }
                } catch(e) {}
            }

            if (!listingEl) return {error: 'listing not found'};

            // Find all tallstackui_modal elements
            const modals = document.querySelectorAll('[x-data*="tallstackui_modal"]');

            return Array.from(modals).map((modal, i) => {
                const isInsideListing = listingEl.contains(modal);
                return {
                    index: i,
                    xData: modal.getAttribute('x-data').slice(0, 80),
                    isInsideListing: isInsideListing
                };
            });
        }
    """)
    print("Modal placement relative to invoices.listing:")
    for m in result:
        print(f"  [{m['index']}] inside={m['isInsideListing']} xData={m['xData']}")

    browser.close()
