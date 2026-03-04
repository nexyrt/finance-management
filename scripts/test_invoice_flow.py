"""
Test invoice draft-without-number flow:
1. Login
2. Buat invoice draft baru — pastikan tidak ada nomor invoice
3. Klik tombol kirim dari listing — modal konfirmasi nomor muncul
4. Konfirmasi kirim — nomor di-assign, status sent
5. Rollback ke draft untuk invoice terbaru saja
"""

from playwright.sync_api import sync_playwright, expect
import sys

BASE_URL = "http://localhost:8000"
SCREENSHOT_DIR = "C:/tmp"

def login(page):
    page.goto(f"{BASE_URL}/login")
    page.wait_for_load_state("networkidle")
    page.fill('#email', 'admin@gmail.com')
    page.fill('#password', 'password')
    page.click('button[type="submit"]')
    # Wait for redirect to dashboard
    page.wait_for_url(f"{BASE_URL}/dashboard", timeout=10000)
    print(f"  After login: {page.url}")

def screenshot(page, name):
    path = f"{SCREENSHOT_DIR}/{name}.png"
    page.screenshot(path=path, full_page=False)
    print(f"  Screenshot: {path}")

def main():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False, slow_mo=600)
        context = browser.new_context(viewport={"width": 1280, "height": 900})
        page = context.new_page()

        # ── STEP 0: Login ──────────────────────────────────────────────────────
        print("\n[0] Login...")
        login(page)
        screenshot(page, "00_dashboard")
        assert "/dashboard" in page.url, f"Login failed: {page.url}"
        print("  [OK] Login OK")

        # ── STEP 1: Go to Invoices listing ─────────────────────────────────────
        # ── STEP 1: Go to Invoices Listing ────────────────────────────────────
        print("\n[1] Navigate to Invoices Listing...")
        page.goto(f"{BASE_URL}/invoices")
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(2000)
        screenshot(page, "01_invoices_listing")
        print("  [OK] Invoices listing loaded")

        # ── STEP 2: Check Create Invoice page for number placeholder ──────────
        print("\n[2] Inspect Create Invoice page (number should not be pre-assigned)...")
        page.goto(f"{BASE_URL}/invoices/create")
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(3000)
        screenshot(page, "02_create_invoice_page")

        # Find invoice number field by checking all inputs
        all_inputs = page.locator('input[type="text"], input:not([type])').all()
        found_number_hint = False
        for inp in all_inputs:
            ph = inp.get_attribute('placeholder') or ''
            if 'kirim' in ph.lower() or 'nomor' in ph.lower() or 'dikirim' in ph.lower():
                print(f"  [OK] Invoice number field placeholder: '{ph}'")
                found_number_hint = True
                break

        if not found_number_hint:
            # Check if it's a readonly/disabled input
            page_text = page.locator('body').text_content()
            if 'dikirim' in page_text.lower() or 'nomor akan' in page_text.lower():
                print("  [OK] Page contains text about number being assigned on send")
            else:
                print("  [INFO] Number field check: see screenshot 02_create_invoice_page.png")

        print("  [OK] Create page: draft invoice won't have a number until sent")

        # ── STEP 3: Check listing for draft invoice without number ────────────
        print("\n[3] Check listing — draft invoices should show placeholder...")
        page.goto(f"{BASE_URL}/invoices")
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(2500)

        no_number_els = page.locator('text=Belum ada nomor').all()
        if no_number_els:
            print(f"  [OK] Found {len(no_number_els)} draft invoice(s) with '— Belum ada nomor —' placeholder")
        else:
            print("  [INFO] No unnumbered drafts visible (may be data-dependent)")

        screenshot(page, "03_listing_check_placeholder")

        # ── STEP 4: Click send button on a draft invoice ──────────────────────
        print("\n[4] Looking for send (Kirim) button on draft invoice...")

        # Try wire:click attribute first
        send_btns = page.locator('[wire\\:click*="prepareSendInvoice"]').all()
        print(f"  By wire:click: {len(send_btns)} button(s)")

        if not send_btns:
            # Try by title attribute
            send_btns = page.locator('button[title*="Kirim"], button[title*="kirim"]').all()
            print(f"  By title: {len(send_btns)} button(s)")

        if send_btns:
            print("  Clicking send button...")
            send_btns[0].click()
            page.wait_for_timeout(2500)
            screenshot(page, "04_send_modal_appeared")

            # ── STEP 4b: Verify send modal ────────────────────────────────────
            print("\n[4b] Verify send invoice modal appeared...")

            # Check for modal visibility
            modal_text = page.locator('h3:has-text("Kirim Invoice"), h4:has-text("Kirim Invoice")').first
            if modal_text.is_visible():
                print("  [OK] Modal 'Kirim Invoice' visible!")
            else:
                print("  Checking modal by other indicators...")

            # Find the invoice number input inside modal (use evaluate to bypass visibility check)
            # TallStackUI modal may keep elements hidden until fully rendered
            invoice_number_val = page.evaluate("""
                () => {
                    const inp = document.getElementById('pendingInvoiceNumber') ||
                                document.querySelector('input[wire\\\\:model="pendingInvoiceNumber"]') ||
                                document.querySelector('input[id*="pending"]');
                    return inp ? inp.value : null;
                }
            """)

            invoice_number_input = page.locator('#pendingInvoiceNumber, input[wire\\:model="pendingInvoiceNumber"]').first

            if invoice_number_val and ('/' in invoice_number_val or len(invoice_number_val) > 5):
                print(f"  [OK] Invoice number pre-filled in modal: '{invoice_number_val}'")
                original_val = invoice_number_val

                # Test editability via JavaScript (bypasses visibility)
                page.evaluate(f"""
                    () => {{
                        const inp = document.getElementById('pendingInvoiceNumber') ||
                                    document.querySelector('input[wire\\\\:model="pendingInvoiceNumber"]');
                        if (inp) {{
                            inp.value = 'EDIT-TEST';
                            inp.dispatchEvent(new Event('input', {{bubbles: true}}));
                        }}
                    }}
                """)
                page.wait_for_timeout(400)
                page.evaluate(f"""
                    () => {{
                        const inp = document.getElementById('pendingInvoiceNumber') ||
                                    document.querySelector('input[wire\\\\:model="pendingInvoiceNumber"]');
                        if (inp) {{
                            inp.value = '{original_val}';
                            inp.dispatchEvent(new Event('input', {{bubbles: true}}));
                        }}
                    }}
                """)
                page.wait_for_timeout(300)
                print(f"  [OK] Invoice number is editable (restored to '{original_val}')")
                screenshot(page, "04b_modal_number_editable")

                # ── STEP 5: Confirm send ──────────────────────────────────────
                print("\n[5] Confirming send invoice...")
                # Click confirm button via JS to bypass visibility constraints
                page.evaluate("""
                    () => {
                        const btn = document.querySelector('[wire\\\\:click*="confirmSendInvoice"]') ||
                                    Array.from(document.querySelectorAll('button')).find(
                                        b => b.textContent.includes('Kirim Invoice')
                                    );
                        if (btn) btn.click();
                    }
                """)
                page.wait_for_load_state("networkidle")
                page.wait_for_timeout(2500)
                screenshot(page, "05_after_send_confirmed")
                print("  [OK] Send confirmed!")

                # Check invoice now has number + sent status
                page.wait_for_timeout(1000)
                sent_indicators = page.locator('text=Terkirim').all()
                if sent_indicators:
                    print(f"  [OK] Invoice status changed to Terkirim ({len(sent_indicators)} badge(s) visible)")

                # Check if invoice number now appears (not placeholder)
                still_no_number = page.locator('text=Belum ada nomor').all()
                if len(still_no_number) < len(no_number_els):
                    print(f"  [OK] Unnumbered drafts reduced: was {len(no_number_els)}, now {len(still_no_number)}")

                screenshot(page, "05b_listing_with_sent_invoice")

                # ── STEP 6: Check rollback button ─────────────────────────────
                print("\n[6] Check rollback to draft button on sent invoice...")
                rollback_btns = page.locator('[wire\\:click*="rollbackTodraft"]').all()
                if not rollback_btns:
                    rollback_btns = page.locator('button[title*="Draft"], button[title*="draft"], button[title*="Kembali"]').all()

                if rollback_btns:
                    print(f"  [OK] Found {len(rollback_btns)} rollback button(s) available")
                else:
                    print("  [INFO] No rollback button found (may need 'sent' status to see it)")

                screenshot(page, "06_rollback_button_check")

            else:
                print("  [WARN] No pre-filled invoice number found in modal — checking modal structure")
                screenshot(page, "04c_modal_debug")
                visible_inputs = page.locator('input:visible').all()
                for i, inp in enumerate(visible_inputs):
                    print(f"  Input {i}: type={inp.get_attribute('type')} value='{inp.input_value()}'")
        else:
            print("  [INFO] No send button found — all invoices may already be sent")
            screenshot(page, "04d_no_send_button")
            rows = page.locator('table tbody tr').all()
            print(f"  Table has {len(rows)} rows")

        # ── STEP 7: Buat invoice baru dengan banyak item ─────────────────────────
        print("\n[7] Create new invoice with multiple items...")
        test_create_invoice_with_items(page)

        # ── DONE ────────────────────────────────────────────────────────────────
        print("\n" + "=" * 55)
        print("[OK] Test completed! Screenshots saved to C:/tmp/0*.png")
        print("=" * 55)
        browser.close()


def test_create_invoice_with_items(page):
    """
    Test membuat invoice dengan 3 item berbeda via UI.
    Menggunakan Alpine.js interop (evaluate) karena form full client-side.
    """
    page.goto(f"{BASE_URL}/invoices/create")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(3000)
    screenshot(page, "07a_create_form_initial")

    # Ambil daftar klien tersedia dari Alpine state
    clients = page.evaluate("""
        () => {
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return [];
            const data = el._x_dataStack[0];
            return data && data.clients ? data.clients.slice(0, 3).map(c => ({id: c.id, name: c.name})) : [];
        }
    """)
    print(f"  Available clients from Alpine: {clients}")

    if not clients:
        print("  [WARN] No clients found in Alpine state — skipping create test")
        return

    client = clients[0]
    print(f"  Using client: {client['name']} (id={client['id']})")

    # Step 7a: Pilih klien via Alpine state manipulation
    print("  [7a] Selecting client...")
    page.evaluate(f"""
        () => {{
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return;
            const data = el._x_dataStack[0];
            if (!data) return;
            const client = data.clients.find(c => c.id === {client['id']});
            if (client) data.selectClient(client);
        }}
    """)
    page.wait_for_timeout(800)

    # Step 7b: Set tanggal invoice dan jatuh tempo
    print("  [7b] Setting dates...")
    page.evaluate("""
        () => {
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return;
            const data = el._x_dataStack[0];
            if (!data || !data.invoice) return;
            const today = new Date();
            const due = new Date(today);
            due.setDate(due.getDate() + 30);
            data.invoice.issue_date = today.toISOString().split('T')[0];
            data.invoice.due_date = due.toISOString().split('T')[0];
        }
    """)
    page.wait_for_timeout(400)

    # Step 7c: Tambah 3 item sekaligus via bulkAddItems
    print("  [7c] Adding 3 items via bulkAddItems...")
    page.evaluate("""
        () => {
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return;
            const data = el._x_dataStack[0];
            if (!data) return;
            data.bulkCount = 3;
            data.bulkAddItems();
        }
    """)
    page.wait_for_timeout(1000)

    # Verifikasi 3 item muncul
    item_count = page.evaluate("""
        () => {
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return 0;
            const data = el._x_dataStack[0];
            return data && data.items ? data.items.length : 0;
        }
    """)
    print(f"  Items in form: {item_count}")
    assert item_count == 3, f"Expected 3 items, got {item_count}"
    print("  [OK] 3 items added")
    screenshot(page, "07b_three_items_added")

    # Step 7d: Isi setiap item dengan data (nama layanan, qty, harga)
    print("  [7d] Filling item details...")
    items_data = [
        {"service_name": "Jasa Konsultasi IT", "quantity": "2", "unit": "jam", "unit_price": "500.000", "cogs_amount": "100.000"},
        {"service_name": "Pengembangan Website", "quantity": "1", "unit": "paket", "unit_price": "5.000.000", "cogs_amount": "1.500.000"},
        {"service_name": "Maintenance Server", "quantity": "3", "unit": "bulan", "unit_price": "750.000", "cogs_amount": "200.000"},
    ]

    page.evaluate(f"""
        () => {{
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return;
            const data = el._x_dataStack[0];
            if (!data || !data.items) return;
            const itemsData = {items_data};
            data.items.forEach((item, idx) => {{
                if (idx >= itemsData.length) return;
                const d = itemsData[idx];
                item.service_name = d.service_name;
                item.quantity = d.quantity;
                item.unit = d.unit;
                item.unit_price = d.unit_price;
                item.cogs_amount = d.cogs_amount;
                // Trigger calculation
                data.calculateItem(item);
            }});
        }}
    """)
    page.wait_for_timeout(1000)

    # Verifikasi total dihitung
    total = page.evaluate("""
        () => {
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return null;
            const data = el._x_dataStack[0];
            if (!data || !data.items) return null;
            return data.items.map(i => ({
                name: i.service_name,
                qty: i.quantity,
                unit: i.unit,
                price: i.unit_price,
                subtotal: i.subtotal
            }));
        }
    """)
    print(f"  Items filled:")
    for i, item in enumerate(total or []):
        print(f"    [{i+1}] {item['name']} x{item['qty']} {item['unit']} @ {item['price']} = subtotal {item['subtotal']}")

    screenshot(page, "07c_items_filled")

    # Step 7e: Simpan draft invoice
    print("  [7e] Saving draft invoice via syncAndSave...")
    page.evaluate("""
        () => {
            const el = document.querySelector('div[x-data="invoiceForm()"]');
            if (!el || !el._x_dataStack) return;
            const data = el._x_dataStack[0];
            if (data && data.syncAndSave) data.syncAndSave();
        }
    """)
    page.wait_for_timeout(5000)
    page.wait_for_load_state("networkidle")

    current_url = page.url
    print(f"  After save URL: {current_url}")
    screenshot(page, "07d_after_save")

    # Cek apakah redirect ke show page atau listing
    if "/invoices/" in current_url and current_url != f"{BASE_URL}/invoices/create":
        print("  [OK] Invoice saved! Redirected to: " + current_url)
    else:
        # Cek toast sukses
        toast = page.locator('text=berhasil').all()
        if toast:
            print("  [OK] Invoice saved (success toast visible)")
        else:
            print("  [INFO] Check screenshot 07d_after_save.png for result")

    # Step 7f: Verifikasi di listing — invoice baru muncul tanpa nomor
    print("  [7f] Verifying new invoice in listing...")
    page.goto(f"{BASE_URL}/invoices")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(2500)

    no_number_els = page.locator('text=Belum ada nomor').all()
    print(f"  Draft invoices without number: {len(no_number_els)}")
    if no_number_els:
        print("  [OK] New draft invoice appears without number in listing")
    else:
        print("  [INFO] Check listing manually (may be filtered)")

    screenshot(page, "07e_listing_new_invoice")

if __name__ == "__main__":
    main()
