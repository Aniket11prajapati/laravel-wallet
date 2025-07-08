<?php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained(config('wallet.users_table'));
    $table->decimal('amount', 15, 2);
    $table->enum('type', ['credit', 'debit']);
    $table->string('description')->nullable();
    $table->json('meta')->nullable();
    $table->timestamps();
});

Schema::create('transaction_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained(config('wallet.users_table'));
    $table->decimal('amount', 15, 2);
    $table->enum('type', ['credit', 'debit', 'refund']);
    $table->string('description')->nullable();
    $table->json('meta')->nullable();
    $table->timestamps();
});
