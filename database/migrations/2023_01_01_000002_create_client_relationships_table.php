<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('clients');
            $table->foreignId('company_id')->constrained('clients');
            $table->timestamps();
            
            // Ensure unique relationships
            $table->unique(['owner_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_relationships');
    }
};
