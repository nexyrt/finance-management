<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE recurring_templates MODIFY COLUMN status ENUM('active', 'inactive', 'archived') DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE recurring_templates MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }
};
