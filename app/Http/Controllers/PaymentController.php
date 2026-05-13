<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if (in_array($invoice->status, ['draft', 'paid'])) {
            return response()->json(['message' => 'Invoice tidak dapat menerima pembayaran.'], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('payments', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $invoice->updateStatus();

        return response()->json($this->formatPayment($payment->fresh(['bankAccount'])));
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'remove_attachment' => ['nullable', 'boolean'],
        ]);

        $attachmentPath = $payment->attachment_path;
        $attachmentName = $payment->attachment_name;

        if ($request->boolean('remove_attachment') || $request->hasFile('attachment')) {
            if ($payment->attachment_path && Storage::disk('public')->exists($payment->attachment_path)) {
                Storage::disk('public')->delete($payment->attachment_path);
            }
            $attachmentPath = null;
            $attachmentName = null;
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('payments', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $payment->update([
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $payment->invoice->updateStatus();

        return response()->json($this->formatPayment($payment->fresh(['bankAccount'])));
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $invoice = $payment->invoice;
        $payment->delete();
        $invoice->updateStatus();

        return response()->json(['message' => 'Pembayaran berhasil dihapus.']);
    }

    private function formatPayment(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'amount' => $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => $payment->payment_method,
            'bank_account_id' => $payment->bank_account_id,
            'bank_account_name' => $payment->bankAccount
                ? $payment->bankAccount->account_name.' ('.$payment->bankAccount->bank_name.')'
                : null,
            'reference_number' => $payment->reference_number,
            'attachment_name' => $payment->attachment_name,
            'attachment_url' => $payment->attachment_url,
        ];
    }
}
