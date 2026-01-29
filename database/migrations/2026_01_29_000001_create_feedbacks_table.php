<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();

            // Reporter info
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Feedback details
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['bug', 'feature', 'feedback'])->default('feedback');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');

            // Context
            $table->string('page_url')->nullable();

            // Supporting documents
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();

            // Admin response
            $table->text('admin_response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('type');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
