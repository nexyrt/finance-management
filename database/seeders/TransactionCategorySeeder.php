<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Parent categories
        $parents = [
            ['type' => 'expense', 'label' => 'Operational Expenses'],
            ['type' => 'expense', 'label' => 'PENGELUARAN LAIN-LAIN'],
            ['type' => 'expense', 'label' => 'HPP'],
            ['type' => 'expense', 'label' => 'CAPEX'],
            ['type' => 'income', 'label' => 'Penghasilan'],
            ['type' => 'transfer', 'label' => 'Transfer Internal'],
            ['type' => 'financing', 'label' => 'Penerimaan Pinjaman'],
            ['type' => 'financing', 'label' => 'Pembayaran Pokok Pinjaman'],
            ['type' => 'financing', 'label' => 'Setoran Modal'],
            ['type' => 'financing', 'label' => 'Penarikan Modal'],
            ['type' => 'financing', 'label' => 'Piutang Diberikan'],
            ['type' => 'financing', 'label' => 'Pembayaran Piutang Diterima'],
            ['type' => 'expense', 'label' => 'Beban Bunga Pinjaman'],
            ['type' => 'income', 'label' => 'Pendapatan Bunga'],
        ];

        foreach ($parents as $parent) {
            TransactionCategory::firstOrCreate(
                ['type' => $parent['type'], 'label' => $parent['label'], 'parent_id' => null]
            );
        }

        // Child categories: 'parent_label' => children
        $children = [
            'Operational Expenses' => [
                ['type' => 'expense', 'label' => 'MAKAN MINUM'],
                ['type' => 'expense', 'label' => 'KASBON'],
                ['type' => 'expense', 'label' => 'PAJAK PERUSAHAAN'],
                ['type' => 'expense', 'label' => 'OPERASIONAL LUAR KOTA'],
                ['type' => 'expense', 'label' => 'OPERASIONAL'],
                ['type' => 'expense', 'label' => 'OPEX BULANAN'],
            ],
            'PENGELUARAN LAIN-LAIN' => [
                ['type' => 'expense', 'label' => 'ADMIN BANK'],
                ['type' => 'expense', 'label' => 'PEMBAYARAN PIUTANG'],
            ],
            'HPP' => [
                ['type' => 'expense', 'label' => 'HPP SISTEM DIGITAL'],
                ['type' => 'expense', 'label' => 'HPP LEGAL'],
                ['type' => 'expense', 'label' => 'HPP DIGITAL MARKETING'],
                ['type' => 'expense', 'label' => 'HPP PERPAJAKAN'],
            ],
            'CAPEX' => [
                ['type' => 'expense', 'label' => 'ASET PERUSAHAAN'],
            ],
            'Penghasilan' => [
                ['type' => 'income', 'label' => 'Kembali Dana'],
            ],
        ];

        foreach ($children as $parentLabel => $childCategories) {
            $parent = TransactionCategory::where('label', $parentLabel)->whereNull('parent_id')->first();

            if ($parent) {
                foreach ($childCategories as $child) {
                    TransactionCategory::firstOrCreate(
                        ['type' => $child['type'], 'label' => $child['label'], 'parent_id' => $parent->id]
                    );
                }
            }
        }
    }
}
