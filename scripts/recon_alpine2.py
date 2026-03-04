"""Recon Alpine.js data via _x_dataStack."""
from playwright.sync_api import sync_playwright

SCRIPT = """
() => {
    const el = document.querySelector('[x-data]');
    if (!el || !el._x_dataStack) return {error: 'no dataStack'};
    const stack = el._x_dataStack;
    const info = {stackLen: stack.length};
    for (let i = 0; i < stack.length; i++) {
        const d = stack[i];
        info['stack' + i + '_keys'] = Object.keys(d).join(',').slice(0, 200);
        if (d.clients !== undefined) {
            info['clients_count'] = d.clients ? d.clients.length : 0;
            info['first_client'] = d.clients && d.clients[0] ? {id: d.clients[0].id, name: d.clients[0].name} : null;
        }
        if (d.items !== undefined) {
            info['items_count'] = d.items.length;
        }
        if (typeof d.bulkAddItems === 'function') {
            info['has_bulkAddItems'] = true;
            info['has_syncAndSave'] = typeof d.syncAndSave === 'function';
            info['has_selectClient'] = typeof d.selectClient === 'function';
        }
    }
    return info;
}
"""

SCRIPT_GET_CLIENTS = """
() => {
    const el = document.querySelector('[x-data]');
    if (!el || !el._x_dataStack) return [];
    for (const data of el._x_dataStack) {
        if (data.clients && data.clients.length > 0) {
            return data.clients.slice(0, 5).map(c => ({id: c.id, name: c.name}));
        }
    }
    return [];
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
    print("Stack info:")
    for k, v in result.items():
        print(f"  {k}: {v}")

    clients = page.evaluate(SCRIPT_GET_CLIENTS)
    print("Clients:", clients)

    browser.close()
