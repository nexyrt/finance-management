<?php

namespace App\Http\Requests;

use App\Models\BankTransaction;
use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyBankTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $ids = $this->input('ids');
        if (! is_array($ids) || empty($ids)) {
            return true; // defer to validation (422)
        }

        // Require the matching delete permission for every feature present in the selection.
        $features = BankTransaction::whereIn('id', $ids)->get()
            ->map->permissionFeature()
            ->unique();

        foreach ($features as $feature) {
            if (! $user->can("delete {$feature}")) {
                return false;
            }
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:bank_transactions,id'],
        ];
    }
}
