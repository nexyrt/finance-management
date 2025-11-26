<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('receivable_number')->unique();
            $table->enum('type', ['employee_loan', 'company_loan']);

            $table->morphs('debtor');

            $table->bigInteger('principal_amount');
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->integer('installment_months')->nullable();
            $table->bigInteger('installment_amount')->nullable();

            $table->date('loan_date');
            $table->date('due_date')->nullable();

            $table->enum('status', ['draft', 'pending_approval', 'active', 'paid_off', 'rejected'])->default('draft');
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->string('contract_attachment')->nullable();
            $table->timestamps();

            $table->index(['debtor_type', 'debtor_id']);
            $table->index('status');
            $table->index('loan_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};