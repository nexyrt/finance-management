<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // INCOME
            [
                'type' => 'income',
                'code' => 'income',
                'label' => 'Pemasukan',
                'parent_code' => null,
                'children' => [
                    ['code' => 'invoice_payment', 'label' => 'Pembayaran Invoice'],
                    ['code' => 'direct_fee', 'label' => 'Fee Langsung'],
                    ['code' => 'other_income', 'label' => 'Pemasukan Lain'],
                ]
            ],

            // EXPENSE
            [
                'type' => 'expense',
                'code' => 'expense',
                'label' => 'Pengeluaran',
                'parent_code' => null,
                'children' => [
                    ['code' => 'opex', 'label' => 'Operational Expense'],
                    ['code' => 'capex', 'label' => 'Capital Expense'],
                    ['code' => 'cogs', 'label' => 'Cost of Goods Sold'],
                    ['code' => 'marketing', 'label' => 'Marketing Expense'],
                    ['code' => 'administrative', 'label' => 'Administrative Cost'],
                ]
            ],

            // TRANSFER
            [
                'type' => 'transfer',
                'code' => 'transfer',
                'label' => 'Transfer Internal',
                'parent_code' => null,
            ],

            // ADJUSTMENT
            [
                'type' => 'adjustment',
                'code' => 'adjustment',
                'label' => 'Penyesuaian',
                'parent_code' => null,
                'children' => [
                    ['code' => 'exchange', 'label' => 'Tukar Uang'],
                    ['code' => 'correction', 'label' => 'Koreksi Saldo'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = TransactionCategory::create($categoryData);

            foreach ($children as $childData) {
                TransactionCategory::create([
                    'type' => $parent->type,
                    'code' => $childData['code'],
                    'label' => $childData['label'],
                    'parent_code' => $parent->code,
                ]);
            }
        }
    }
}