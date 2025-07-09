<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->enum('status', ['pending', 'processing', 'failed', 'success'])->default('pending');
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });


        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // ✅ Use actual table name
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit', 'refund']);
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_histories');
        Schema::dropIfExists('transactions');
    }
};
