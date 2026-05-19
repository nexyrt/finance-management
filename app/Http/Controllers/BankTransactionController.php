<?php

namespace App\Http\Controllers;

use App\Models\BankTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankTransactionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_type' => 'required|in:credit,debit',
            'amount' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:transaction_categories,id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('bank-transactions', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $transaction = BankTransaction::create([
            'bank_account_id' => $validated['bank_account_id'],
            'transaction_type' => $validated['transaction_type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $transaction->load('category.parent');

        return response()->json([
            'message' => $validated['transaction_type'] === 'credit'
                ? 'Pemasukan berhasil disimpan.'
                : 'Pengeluaran berhasil disimpan.',
            'transaction' => $this->formatTransaction($transaction),
        ]);
    }

    public function update(Request $request, BankTransaction $bankTransaction): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:transaction_categories,id',
            'transaction_date' => 'nullable|date',
            'amount' => 'nullable|integer|min:1',
        ]);

        $bankTransaction->update(array_filter($validated, fn ($v) => $v !== null));
        $bankTransaction->load('category.parent');

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui.',
            'transaction' => $this->formatTransaction($bankTransaction),
        ]);
    }

    public function destroy(BankTransaction $bankTransaction): JsonResponse
    {
        $deleted = 1;

        // Detect & delete paired TRF transactions
        if ($bankTransaction->reference_number && str_starts_with($bankTransaction->reference_number, 'TRF')) {
            $paired = BankTransaction::where('reference_number', $bankTransaction->reference_number)
                ->where('id', '!=', $bankTransaction->id)
                ->get();

            foreach ($paired as $pair) {
                $pair->delete();
                $deleted++;
            }
        }

        $bankTransaction->delete();

        return response()->json([
            'message' => $deleted > 1
                ? "Transfer dihapus ({$deleted} transaksi terkait)."
                : 'Transaksi berhasil dihapus.',
            'deleted_count' => $deleted,
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_ids' => 'required|array|min:1',
            'transaction_ids.*' => 'integer|exists:bank_transactions,id',
        ]);

        $ids = collect($validated['transaction_ids']);

        // Include paired TRF transactions
        $trfRefs = BankTransaction::whereIn('id', $ids)
            ->whereNotNull('reference_number')
            ->where('reference_number', 'like', 'TRF%')
            ->pluck('reference_number')
            ->unique();

        if ($trfRefs->isNotEmpty()) {
            $pairedIds = BankTransaction::whereIn('reference_number', $trfRefs)->pluck('id');
            $ids = $ids->merge($pairedIds)->unique();
        }

        $deleted = BankTransaction::whereIn('id', $ids)->count();
        BankTransaction::whereIn('id', $ids)->each(fn ($t) => $t->delete());

        return response()->json([
            'message' => "{$deleted} transaksi berhasil dihapus.",
            'deleted_count' => $deleted,
        ]);
    }

    public function categorize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_ids' => 'required|array|min:1',
            'transaction_ids.*' => 'integer|exists:bank_transactions,id',
            'category_id' => 'required|exists:transaction_categories,id',
        ]);

        $updated = BankTransaction::whereIn('id', $validated['transaction_ids'])
            ->update(['category_id' => $validated['category_id']]);

        return response()->json([
            'message' => "{$updated} transaksi berhasil dikategorikan.",
            'updated_count' => $updated,
        ]);
    }

    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:bank_accounts,id',
            'to_account_id' => 'required|exists:bank_accounts,id|different:from_account_id',
            'amount' => 'required|integer|min:1',
            'admin_fee' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:transaction_categories,id',
            'transfer_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $adminFee = $validated['admin_fee'] ?? 0;
        $reference = 'TRF'.time();

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('bank-transactions', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $desc = ! empty($validated['description']) ? ' - '.$validated['description'] : '';

        DB::transaction(function () use ($validated, $adminFee, $reference, $attachmentPath, $attachmentName, $desc) {
            BankTransaction::create([
                'bank_account_id' => $validated['from_account_id'],
                'transaction_type' => 'debit',
                'amount' => $validated['amount'] + $adminFee,
                'transaction_date' => $validated['transfer_date'],
                'description' => 'Transfer keluar'.($adminFee > 0 ? ' + Biaya Admin' : '').$desc,
                'reference_number' => $reference,
                'category_id' => $validated['category_id'] ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            BankTransaction::create([
                'bank_account_id' => $validated['to_account_id'],
                'transaction_type' => 'credit',
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transfer_date'],
                'description' => 'Transfer masuk'.$desc,
                'reference_number' => $reference,
                'category_id' => $validated['category_id'] ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);
        });

        return response()->json(['message' => 'Transfer berhasil dilakukan.']);
    }

    private function formatTransaction(BankTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'bank_account_id' => $transaction->bank_account_id,
            'transaction_type' => $transaction->transaction_type,
            'amount' => $transaction->amount,
            'transaction_date' => $transaction->transaction_date?->format('Y-m-d'),
            'description' => $transaction->description,
            'reference_number' => $transaction->reference_number,
            'category_id' => $transaction->category_id,
            'category' => $transaction->category ? [
                'id' => $transaction->category->id,
                'label' => $transaction->category->label,
                'parent' => $transaction->category->parent ? [
                    'id' => $transaction->category->parent->id,
                    'label' => $transaction->category->parent->label,
                ] : null,
            ] : null,
            'attachment_url' => $transaction->attachment_path
                ? Storage::url($transaction->attachment_path)
                : null,
            'attachment_name' => $transaction->attachment_name,
            'created_at' => $transaction->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
