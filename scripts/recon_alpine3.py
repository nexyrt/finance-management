"""Recon all x-data elements on create invoice page."""
from playwright.sync_api import sync_playwright

SCRIPT = """
() => {
    const results = [];
    const els = document.querySelectorAll('[x-data]');
    for (const el of els) {
        const xDataAttr = el.getAttribute('x-data') || '';
        const stack = el._x_dataStack || [];
        const stackInfo = stack.map((d, i) => {
            const keys = Object.keys(d).join(',').slice(0, 150);
            return {
                index: i,
                keys: keys,
                hasClients: 'clients' in d,
                clientCount: d.clients ? d.clients.length : 0,
                hasBulkAdd: typeof d.bulkAddItems === 'function'
            };
        });
        results.push({
            tag: el.tagName,
            xDataAttr: xDataAttr.slice(0, 60),
            stackLen: stack.length,
            stackInfo: stackInfo
        });
    }
    return results;
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
    page.wait_for_timeout(5000)

    results = page.evaluate(SCRIPT)
    print(f"Found {len(results)} x-data elements:")
    for r in results:
        print(f"\n  [{r['tag']}] x-data='{r['xDataAttr']}'")
        print(f"    stackLen={r['stackLen']}")
        for s in r['stackInfo']:
            print(f"    stack[{s['index']}]: keys={s['keys']}")
            print(f"      hasClients={s['hasClients']} clientCount={s['clientCount']} hasBulkAdd={s['hasBulkAdd']}")

    browser.close()
