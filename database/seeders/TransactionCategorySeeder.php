<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // EXPENSE - Operational
            ['type' => 'expense', 'code' => 'OPEX', 'label' => 'Operational Expenses', 'parent_code' => null],
            ['type' => 'expense', 'code' => 'OPX_KSN', 'label' => 'MAKAN MINUM', 'parent_code' => 'OPEX'],
            ['type' => 'expense', 'code' => 'KSBN_KSN', 'label' => 'KASBON', 'parent_code' => 'OPEX'],
            ['type' => 'expense', 'code' => 'PJK_KSN', 'label' => 'PAJAK PERUSAHAAN', 'parent_code' => 'OPEX'],
            ['type' => 'expense', 'code' => 'OPX_OPS1', 'label' => 'OPERASIONAL LUAR KOTA', 'parent_code' => 'OPEX'],
            ['type' => 'expense', 'code' => 'OPX_OPS', 'label' => 'OPERASIONAL', 'parent_code' => 'OPEX'],
            ['type' => 'expense', 'code' => 'OPX_KSN1', 'label' => 'OPEX BULANAN', 'parent_code' => 'OPEX'],

            // EXPENSE - Other
            ['type' => 'expense', 'code' => 'OTHER', 'label' => 'PENGELUARAN LAIN-LAIN', 'parent_code' => null],
            ['type' => 'expense', 'code' => 'ADM', 'label' => 'ADMIN BANK', 'parent_code' => 'OTHER'],
            ['type' => 'expense', 'code' => 'PIUTANG_PTKSN', 'label' => 'PEMBAYARAN PIUTANG', 'parent_code' => 'OTHER'],

            // EXPENSE - HPP
            ['type' => 'expense', 'code' => 'HPP', 'label' => 'HPP', 'parent_code' => null],
            ['type' => 'expense', 'code' => 'HPP_SISTM', 'label' => 'HPP SISTEM DIGITAL', 'parent_code' => 'HPP'],
            ['type' => 'expense', 'code' => 'HPP_LGL', 'label' => 'HPP LEGAL', 'parent_code' => 'HPP'],
            ['type' => 'expense', 'code' => 'HPP_DM', 'label' => 'HPP DIGITAL MARKETING', 'parent_code' => 'HPP'],
            ['type' => 'expense', 'code' => 'HPP_PERPAJAKAN', 'label' => 'HPP PERPAJAKAN', 'parent_code' => 'HPP'],

            // EXPENSE - CAPEX
            ['type' => 'expense', 'code' => 'CAPEX', 'label' => 'CAPEX', 'parent_code' => null],
            ['type' => 'expense', 'code' => 'CAPEX_ASET', 'label' => 'ASET PERUSAHAAN', 'parent_code' => 'CAPEX'],

            // INCOME
            ['type' => 'income', 'code' => 'INCOME', 'label' => 'Penghasilan', 'parent_code' => null],
            ['type' => 'income', 'code' => 'RETUR', 'label' => 'Kembali Dana', 'parent_code' => 'INCOME'],

            // TRANSFER
            ['type' => 'transfer', 'code' => 'TRF_INTERNAL', 'label' => 'Transfer Internal', 'parent_code' => null],

            // FINANCING
            ['type' => 'financing', 'code' => 'FIN-LOAN-IN', 'label' => 'Penerimaan Pinjaman', 'parent_code' => null],
            ['type' => 'financing', 'code' => 'FIN-LOAN-OUT', 'label' => 'Pembayaran Pokok Pinjaman', 'parent_code' => null],
            ['type' => 'financing', 'code' => 'FIN-EQUITY-IN', 'label' => 'Setoran Modal', 'parent_code' => null],
            ['type' => 'financing', 'code' => 'FIN-EQUITY-OUT', 'label' => 'Penarikan Modal', 'parent_code' => null],
            ['type' => 'financing', 'code' => 'FIN-RCV-OUT', 'label' => 'Piutang Diberikan', 'parent_code' => null],
            ['type' => 'financing', 'code' => 'FIN-RCV-IN', 'label' => 'Pembayaran Piutang Diterima', 'parent_code' => null],

            // FINANCING - Interest
            ['type' => 'expense', 'code' => 'EXP-INTEREST', 'label' => 'Beban Bunga Pinjaman', 'parent_code' => null],
            ['type' => 'income', 'code' => 'REV-INTEREST', 'label' => 'Pendapatan Bunga', 'parent_code' => null],
        ];

        foreach ($categories as $category) {
            TransactionCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}