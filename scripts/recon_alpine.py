"""Recon Alpine.js data structure on create invoice page."""
from playwright.sync_api import sync_playwright

SCRIPT = """
() => {
    const el = document.querySelector('[x-data]');
    if (!el) return {error: 'no x-data element'};
    if (!window.Alpine) return {error: 'Alpine not on window'};
    try {
        const data = Alpine.$data(el);
        return {
            keys: Object.keys(data).join(','),
            hasClients: !!(data.clients),
            clientCount: data.clients ? data.clients.length : 0,
            firstClient: data.clients && data.clients[0] ? {id: data.clients[0].id, name: data.clients[0].name} : null,
            hasItems: !!(data.items),
            itemCount: data.items ? data.items.length : 0,
            hasBulkCount: 'bulkCount' in data,
            hasBulkAddItems: typeof data.bulkAddItems === 'function',
            hasSyncAndSave: typeof data.syncAndSave === 'function',
            hasSelectClient: typeof data.selectClient === 'function'
        };
    } catch(e) {
        return {error: e.message};
    }
}
"""

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1280, "height": 900})

    page.goto("http://localhost:8000/login")
    page.wait_for_load_state("networkidle")
    page.fill("#email", "admin@gmail.com")
    page.fill("#password", "password")
    page.click("button[type=submit]")
    page.wait_for_url("http://localhost:8000/dashboard", timeout=10000)

    page.goto("http://localhost:8000/invoices/create")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(4000)

    result = page.evaluate(SCRIPT)
    print("Alpine data:", result)

    # Also get all _x_ keys on the element
    xkeys = page.evaluate("""
        () => {
            const el = document.querySelector('[x-data]');
            if (!el) return [];
            return Object.getOwnPropertyNames(el).filter(k => k.startsWith('_x'));
        }
    """)
    print("_x keys on element:", xkeys)

    browser.close()
