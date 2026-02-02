<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Change quantity from integer to decimal to support decimal values
            $table->decimal('quantity', 12, 3)->default(1)->change();

            // Add unit column after quantity
            if (!Schema::hasColumn('invoice_items', 'unit')) {
                $table->string('unit', 20)->default('pcs')->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Revert quantity back to integer
            $table->integer('quantity')->default(1)->change();

            // Drop unit column if exists
            if (Schema::hasColumn('invoice_items', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
