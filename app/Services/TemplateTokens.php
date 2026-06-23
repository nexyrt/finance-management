<?php

namespace App\Services;

use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Invoice;
use Carbon\Carbon;

/**
 * Single source of truth for template token catalog.
 *
 * Each entry defines:
 *   - path:    the {{dot.path}} used in templates
 *   - label:   Indonesian label shown in the field picker
 *   - resolve: callable(Invoice) → string
 *
 * Used server-side (token resolution) AND exposed to the editor (field picker + sampleData).
 */
class TemplateTokens
{
    /**
     * Return the full catalog as an array of ['path', 'label', 'resolve'].
     *
     * @return array<int, array{path: string, label: string, resolve: callable(Invoice): string}>
     */
    public static function catalog(): array
    {
        return [
            // ── Invoice ──────────────────────────────────────────────────────
            [
                'path' => 'invoice.number',
                'label' => 'No. Invoice',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->invoice_number ?? ''),
            ],
            [
                'path' => 'invoice.issue_date',
                'label' => 'Tanggal Terbit',
                'resolve' => fn (Invoice $inv): string => self::formatDate($inv->issue_date),
            ],
            [
                'path' => 'invoice.due_date',
                'label' => 'Jatuh Tempo',
                'resolve' => fn (Invoice $inv): string => self::formatDate($inv->due_date),
            ],
            [
                'path' => 'invoice.status',
                'label' => 'Status',
                'resolve' => fn (Invoice $inv): string => self::translateStatus((string) ($inv->status ?? '')),
            ],
            [
                'path' => 'invoice.subtotal',
                'label' => 'Subtotal',
                'resolve' => fn (Invoice $inv): string => self::formatRupiah((int) ($inv->subtotal ?? 0)),
            ],
            [
                'path' => 'invoice.discount_amount',
                'label' => 'Diskon',
                'resolve' => fn (Invoice $inv): string => self::formatRupiah((int) ($inv->discount_amount ?? 0)),
            ],
            [
                'path' => 'invoice.total_amount',
                'label' => 'Total',
                'resolve' => fn (Invoice $inv): string => self::formatRupiah((int) ($inv->total_amount ?? 0)),
            ],
            [
                'path' => 'invoice.amount_paid',
                'label' => 'Sudah Dibayar',
                'resolve' => fn (Invoice $inv): string => self::formatRupiah((int) $inv->amount_paid),
            ],
            [
                'path' => 'invoice.amount_remaining',
                'label' => 'Sisa Tagihan',
                'resolve' => fn (Invoice $inv): string => self::formatRupiah((int) $inv->amount_remaining),
            ],
            [
                'path' => 'invoice.notes',
                'label' => 'Catatan',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->notes ?? ''),
            ],
            [
                'path' => 'invoice.faktur',
                'label' => 'No. Faktur Pajak',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->faktur ?? ''),
            ],

            // ── Client ───────────────────────────────────────────────────────
            [
                'path' => 'client.name',
                'label' => 'Nama Klien',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->client?->name ?? ''),
            ],
            [
                'path' => 'client.npwp',
                'label' => 'NPWP Klien',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->client?->NPWP ?? ''),
            ],
            [
                'path' => 'client.address',
                'label' => 'Alamat Klien',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->client?->address ?? ''),
            ],
            [
                'path' => 'client.email',
                'label' => 'Email Klien',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->client?->email ?? ''),
            ],
            [
                'path' => 'client.phone',
                'label' => 'Telepon Klien',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->client?->ar_phone_number ?? ''),
            ],
            [
                'path' => 'client.person_in_charge',
                'label' => 'Penanggung Jawab',
                'resolve' => fn (Invoice $inv): string => (string) ($inv->client?->person_in_charge ?? ''),
            ],

            // ── Company ──────────────────────────────────────────────────────
            [
                'path' => 'company.name',
                'label' => 'Nama Perusahaan',
                'resolve' => fn (Invoice $inv): string => (string) (CompanyProfile::current()?->name ?? ''),
            ],
            [
                'path' => 'company.npwp',
                'label' => 'NPWP Perusahaan',
                'resolve' => fn (Invoice $inv): string => (string) (CompanyProfile::current()?->npwp ?? ''),
            ],
            [
                'path' => 'company.address',
                'label' => 'Alamat Perusahaan',
                'resolve' => fn (Invoice $inv): string => (string) (CompanyProfile::current()?->address ?? ''),
            ],
            [
                'path' => 'company.phone',
                'label' => 'Telepon Perusahaan',
                'resolve' => fn (Invoice $inv): string => (string) (CompanyProfile::current()?->phone ?? ''),
            ],
            [
                'path' => 'company.email',
                'label' => 'Email Perusahaan',
                'resolve' => fn (Invoice $inv): string => (string) (CompanyProfile::current()?->email ?? ''),
            ],
            [
                'path' => 'company.finance_manager',
                'label' => 'Manajer Keuangan',
                'resolve' => fn (Invoice $inv): string => (string) (CompanyProfile::current()?->finance_manager_name ?? ''),
            ],
        ];
    }

    /**
     * Resolve all tokens in a text string against the given Invoice.
     * Unknown {{tokens}} are left as-is.
     */
    public static function resolveText(string $text, Invoice $invoice): string
    {
        $map = self::buildMap($invoice);

        return preg_replace_callback('/\{\{([\w.]+)\}\}/', function (array $m) use ($map): string {
            return array_key_exists($m[1], $map) ? $map[$m[1]] : $m[0];
        }, $text);
    }

    /**
     * Return a flat path→value map for a given Invoice (used for sampleData in the editor).
     *
     * @return array<string, string>
     */
    public static function buildMap(Invoice $invoice): array
    {
        $map = [];
        foreach (self::catalog() as $entry) {
            $map[$entry['path']] = ($entry['resolve'])($invoice);
        }

        return $map;
    }

    /**
     * Return just [path, label] pairs — safe to serialize to JSON for the frontend.
     *
     * @return array<int, array{path: string, label: string}>
     */
    public static function catalogForFrontend(): array
    {
        return array_map(
            fn (array $entry): array => ['path' => $entry['path'], 'label' => $entry['label']],
            self::catalog()
        );
    }

    /**
     * Build an in-memory sample Invoice so preview/PDF never crashes when DB is empty.
     */
    public static function sampleInvoice(): Invoice
    {
        $inv = new Invoice([
            'invoice_number' => 'INV/001/KSN/VI.2026',
            'subtotal' => 5000000,
            'discount_amount' => 0,
            'total_amount' => 5000000,
            'issue_date' => '2026-06-08',
            'due_date' => '2026-06-22',
            'status' => 'draft',
            'faktur' => '',
        ]);

        // Attach a synthetic client (not persisted).
        $client = new Client([
            'name' => 'PT Maju Jaya',
            'NPWP' => '01.234.567.8-901.000',
            'address' => 'Jl. Contoh No. 1, Jakarta',
            'email' => 'info@majujaya.co.id',
            'ar_phone_number' => '021-1234567',
            'person_in_charge' => 'Budi Santoso',
        ]);
        $inv->setRelation('client', $client);

        // Payments relation = empty collection so accessors work without DB.
        $inv->setRelation('payments', collect());
        $inv->setRelation('items', collect());

        return $inv;
    }

    // ── Formatting helpers ────────────────────────────────────────────────────

    /**
     * Format an integer (stored in cents-of-Rupiah, i.e. whole Rupiah) to "Rp 1.500.000".
     * Matches the convention used throughout the app: integer → display.
     */
    public static function formatRupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * Format a date value (Carbon, string, or null) to Indonesian long format.
     * Example: "08 Juni 2026"  — matches kisantra-invoice.blade.php convention.
     */
    public static function formatDate(mixed $date): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        $carbon = $date instanceof \DateTimeInterface
            ? Carbon::instance($date)
            : Carbon::parse($date);

        $months = [
            1 => 'Januari',  2 => 'Februari', 3 => 'Maret',
            4 => 'April',    5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',     8 => 'Agustus',   9 => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];

        return sprintf('%02d %s %d', $carbon->day, $months[$carbon->month], $carbon->year);
    }

    /**
     * Translate invoice status to Indonesian.
     */
    private static function translateStatus(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'paid' => 'Lunas',
            'partially_paid' => 'Sebagian Dibayar',
            'sent' => 'Terkirim',
            'overdue' => 'Jatuh Tempo',
            default => $status,
        };
    }
}
