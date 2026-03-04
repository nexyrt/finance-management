"""Debug - find which modal should be sendModal."""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=False, slow_mo=400)
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

    # Find all x-data elements with tallstackui_modal + their entangle
    modals = page.evaluate("""
        () => {
            const els = document.querySelectorAll('[x-data*="tallstackui_modal"]');
            return Array.from(els).map((el, i) => {
                const attr = el.getAttribute('x-data') || '';
                const stack = el._x_dataStack;
                const data = stack && stack[0];
                return {
                    index: i,
                    xData: attr.slice(0, 120),
                    show: data ? data.show : 'no data',
                    id: el.id || 'no-id',
                    display: el.style.display
                };
            });
        }
    """)
    print("All TallStackUI modals:")
    for m in modals:
        print(f"  [{m['index']}] id={m['id']} show={m['show']} display={m['display']}")
        print(f"       xData={m['xData']}")

    # Find Listing Livewire component ID
    lw_comps = page.evaluate("""
        () => {
            if (!window.Livewire) return [];
            return Livewire.all().map(c => ({
                name: c.name || '?',
                id: c.id || '?'
            }));
        }
    """)
    print("\nLivewire components:")
    for c in lw_comps:
        print(f"  {c['name']} id={c['id']}")

    # Click send and check which modal's show changes
    page.evaluate("""
        () => {
            const btns = Array.from(document.querySelectorAll("button"));
            const btn = btns.find(b => (b.getAttribute("wire:click") || "").includes("prepareSendInvoice(10)"));
            if (btn) btn.click();
        }
    """)
    page.wait_for_timeout(3000)

    modals_after = page.evaluate("""
        () => {
            const els = document.querySelectorAll('[x-data*="tallstackui_modal"]');
            return Array.from(els).map((el, i) => {
                const stack = el._x_dataStack;
                const data = stack && stack[0];
                return {
                    index: i,
                    show: data ? data.show : 'no data',
                    display: el.style.display,
                    xData: (el.getAttribute('x-data') || '').slice(0, 80)
                };
            });
        }
    """)
    print("\nModals AFTER click:")
    for m in modals_after:
        changed = "CHANGED!" if str(m['show']) != str(modals[m['index']]['show']) else ""
        print(f"  [{m['index']}] show={m['show']} display={m['display']} {changed}")
        if changed:
            print(f"    xData={m['xData']}")

    browser.close()
