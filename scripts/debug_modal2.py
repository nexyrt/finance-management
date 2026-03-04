"""Debug modal - check console after click."""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=False, slow_mo=400)
    page = browser.new_page(viewport={"width": 1280, "height": 900})

    logs = []
    page.on("console", lambda m: logs.append(m.type + ": " + m.text[:200]))
    page.on("pageerror", lambda e: logs.append("PAGEERROR: " + str(e)[:200]))

    page.goto("http://localhost:8000/login")
    page.wait_for_load_state("networkidle")
    page.fill("#email", "admin@gmail.com")
    page.fill("#password", "password")
    page.click("button[type=submit]")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(2000)
    print("After login:", page.url)

    page.goto("http://localhost:8000/invoices")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(3000)

    # Clear logs before click
    logs.clear()

    # Click send button
    page.evaluate("""
        () => {
            const btns = Array.from(document.querySelectorAll("button"));
            const btn = btns.find(b => (b.getAttribute("wire:click") || "").includes("prepareSendInvoice(10)"));
            if (btn) { console.log("Clicking: " + btn.getAttribute("wire:click")); btn.click(); }
            else console.error("Button not found!");
        }
    """)
    page.wait_for_timeout(4000)
    page.screenshot(path="C:/tmp/debug_modal2.png")

    # Check sendModal in Livewire via wire data
    check = page.evaluate("""
        () => {
            const result = {};
            if (!window.Livewire) { result.error = "no Livewire"; return result; }

            const comps = Livewire.all();
            result.component_count = comps.length;

            for (const comp of comps) {
                try {
                    if (comp.name && comp.name.includes("listing")) {
                        result.found = comp.name;
                        result.sendModal = comp.$wire.get("sendModal");
                        result.pendingInvoiceNumber = comp.$wire.get("pendingInvoiceNumber");
                    }
                } catch(e) { result.compError = e.message; }
            }

            // Check all modals
            const modals = Array.from(document.querySelectorAll("[role=dialog]"));
            result.modal_count = modals.length;
            result.modals = modals.map(m => ({
                visible: m.offsetParent !== null,
                display: m.style.display
            }));

            return result;
        }
    """)
    print("State after click:", check)

    print("\nConsole logs after click:")
    for log in logs:
        print("  " + log)

    browser.close()
