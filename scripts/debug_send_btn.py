"""Debug send button behavior on invoices listing."""
from playwright.sync_api import sync_playwright

GET_SEND_BTNS = """
() => {
    const btns = Array.from(document.querySelectorAll('button'));
    return btns
        .filter(b => {
            const wc = b.getAttribute('wire:click') || '';
            return wc.includes('prepareSendInvoice');
        })
        .map(b => ({
            wireClick: b.getAttribute('wire:click'),
            title: b.getAttribute('title') || '',
            visible: b.offsetParent !== null,
            disabled: b.disabled,
            outerHTML: b.outerHTML.slice(0, 200)
        }));
}
"""

GET_ALL_TITLED_BTNS = """
() => {
    return Array.from(document.querySelectorAll('button[title]')).map(b => ({
        title: b.getAttribute('title'),
        wireClick: b.getAttribute('wire:click') || '',
        visible: b.offsetParent !== null,
        disabled: b.disabled
    }));
}
"""

GET_LIVEWIRE_ERRORS = """
() => {
    if (window.Livewire) {
        return 'Livewire present, version: ' + (window.Livewire.version || 'unknown');
    }
    return 'Livewire not found';
}
"""

with sync_playwright() as p:
    browser = p.chromium.launch(headless=False, slow_mo=300)
    page = browser.new_page(viewport={"width": 1280, "height": 900})

    # Capture all console messages
    logs = []
    page.on('console', lambda m: logs.append(m.type + ': ' + m.text[:100]))
    page.on('pageerror', lambda e: logs.append('PAGEERROR: ' + str(e)[:100]))

    page.goto('http://localhost:8000/login')
    page.wait_for_load_state('networkidle')
    page.fill('#email', 'admin@gmail.com')
    page.fill('#password', 'password')
    page.click('button[type=submit]')
    page.wait_for_url('http://localhost:8000/dashboard', timeout=10000)

    page.goto('http://localhost:8000/invoices')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(3000)
    page.screenshot(path='C:/tmp/debug_listing.png', full_page=False)

    # Check Livewire
    lw = page.evaluate(GET_LIVEWIRE_ERRORS)
    print('Livewire:', lw)

    # Get send buttons
    send_btns = page.evaluate(GET_SEND_BTNS)
    print('\nSend buttons (prepareSendInvoice):', len(send_btns))
    for b in send_btns:
        print('  title=' + b['title'] + ' visible=' + str(b['visible']) + ' disabled=' + str(b['disabled']))
        print('  wire:click=' + str(b['wireClick']))
        print('  html=' + b['outerHTML'][:150])

    # Get all titled buttons
    all_btns = page.evaluate(GET_ALL_TITLED_BTNS)
    print('\nAll titled buttons:')
    for b in all_btns:
        print('  title=' + b['title'] + ' wire=' + b['wireClick'] + ' visible=' + str(b['visible']))

    # Try clicking the send button via JS and watch what happens
    if send_btns:
        print('\nClicking first send button via JS...')
        page.evaluate("""
            () => {
                const btns = Array.from(document.querySelectorAll('button'));
                const btn = btns.find(b => (b.getAttribute('wire:click') || '').includes('prepareSendInvoice'));
                if (btn) {
                    console.log('Clicking button:', btn.getAttribute('wire:click'));
                    btn.click();
                } else {
                    console.log('Button not found!');
                }
            }
        """)
        page.wait_for_timeout(3000)
        page.screenshot(path='C:/tmp/debug_after_click.png', full_page=False)

        # Check if modal appeared
        modal_visible = page.evaluate("""
            () => {
                const modals = document.querySelectorAll('[x-show]');
                const visible = Array.from(modals).filter(m => m.style.display !== 'none' && m.offsetParent !== null);
                return visible.map(m => ({tag: m.tagName, class: m.className.slice(0, 80)}));
            }
        """)
        print('Visible x-show elements after click:', modal_visible)

        # Check sendModal wire property
        send_modal_val = page.evaluate("""
            () => {
                if (!window.Livewire) return 'no Livewire';
                try {
                    const components = window.Livewire.all();
                    for (const comp of components) {
                        if (comp.get && comp.get('sendModal') !== undefined) {
                            return 'sendModal=' + comp.get('sendModal');
                        }
                        const data = comp.$wire ? comp.$wire.__instance : null;
                        if (data) return JSON.stringify(Object.keys(data)).slice(0, 200);
                    }
                    return 'components: ' + components.length;
                } catch(e) {
                    return 'error: ' + e.message;
                }
            }
        """)
        print('Livewire sendModal state:', send_modal_val)

    print('\nConsole logs:')
    for log in logs[-20:]:
        print(' ', log)

    browser.close()
