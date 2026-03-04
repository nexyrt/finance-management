"""Debug mengapa modal tidak muncul saat klik tombol Kirim."""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=False, slow_mo=500)
    page = browser.new_page(viewport={"width": 1280, "height": 900})

    logs = []
    page.on("console", lambda m: logs.append(m.type + ": " + m.text[:200]))
    page.on("pageerror", lambda e: logs.append("PAGEERROR: " + str(e)[:200]))

    page.goto("http://localhost:8000/login")
    page.wait_for_load_state("networkidle")
    page.fill("#email", "admin@gmail.com")
    page.fill("#password", "password")
    page.click("button[type=submit]")
    page.wait_for_url("http://localhost:8000/dashboard", timeout=10000)

    page.goto("http://localhost:8000/invoices")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(3000)

    # Check TallStackUI modal element before click
    modal_info = page.evaluate("""
        () => {
            const modals = document.querySelectorAll('[x-data*="tallstackui_modal"]');
            return Array.from(modals).map(m => {
                const stack = m._x_dataStack;
                const data = stack && stack[0];
                return {
                    xDataAttr: (m.getAttribute('x-data') || '').slice(0, 80),
                    showValue: data ? data.show : 'no stack',
                    wireModel: m.getAttribute('wire:model') || m.getAttribute('wire:model.live') || '',
                    id: data ? data.id : 'no id'
                };
            });
        }
    """)
    print("TallStackUI modals before click:")
    for m in modal_info:
        print("  xData=" + m["xDataAttr"])
        print("  show=" + str(m["showValue"]) + " wireModel=" + m["wireModel"] + " id=" + str(m["id"]))

    # Click send button for invoice 10
    page.evaluate("""
        () => {
            const btns = Array.from(document.querySelectorAll("button"));
            const btn = btns.find(b => (b.getAttribute("wire:click") || "").includes("prepareSendInvoice(10)"));
            if (btn) btn.click();
        }
    """)
    print("\nClicked prepareSendInvoice(10)")
    page.wait_for_timeout(3000)

    # Check modal state after click
    modal_info_after = page.evaluate("""
        () => {
            const modals = document.querySelectorAll('[x-data*="tallstackui_modal"]');
            return Array.from(modals).map(m => {
                const stack = m._x_dataStack;
                const data = stack && stack[0];
                return {
                    showValue: data ? data.show : 'no stack',
                    wireModel: m.getAttribute('wire:model') || m.getAttribute('wire:model.live') || '',
                    display: m.style.display,
                    visible: m.offsetParent !== null
                };
            });
        }
    """)
    print("\nTallStackUI modals AFTER click:")
    for m in modal_info_after:
        print("  show=" + str(m["showValue"]) + " wireModel=" + m["wireModel"] +
              " display=" + m["display"] + " visible=" + str(m["visible"]))

    # Check Livewire sendModal property via wire
    lw_state = page.evaluate("""
        () => {
            if (!window.Livewire) return "no Livewire";
            const comps = Livewire.all();
            for (const comp of comps) {
                try {
                    const val = comp.$wire.get("sendModal");
                    if (val !== undefined) return "sendModal=" + val + " component=" + comp.name;
                } catch(e) {}
            }
            return "sendModal not found in " + comps.length + " components";
        }
    """)
    print("\nLivewire sendModal state:", lw_state)

    page.screenshot(path="C:/tmp/debug_modal_state.png")

    print("\nConsole logs:")
    for log in logs[-30:]:
        print("  " + log)

    browser.close()
