<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing bank accounts where initial_balance is 0 or null
        // Set initial_balance = current_balance for consistency
        DB::table('bank_accounts')
            ->where(function ($query) {
                $query->where('initial_balance', 0)
                      ->orWhereNull('initial_balance');
            })
            ->update([
                'initial_balance' => DB::raw('current_balance'),
                'updated_at' => now()
            ]);

        // Log hasil update
        $updatedCount = DB::table('bank_accounts')
            ->where('initial_balance', '!=', 0)
            ->whereNotNull('initial_balance')
            ->count();

        \Log::info("Updated {$updatedCount} bank accounts with initial_balance = current_balance");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback tidak diperlukan karena kita hanya mengupdate data yang kosong
        // Tidak ada data yang rusak/hilang
        
        // Jika ingin rollback paksa (tidak direkomendasikan):
        // DB::table('bank_accounts')->update(['initial_balance' => 0]);
    }
};