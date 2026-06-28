<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restotech_transaction_days', function (Blueprint $table): void {
            $table->id();
            $table->date('business_date')->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_shifts', function (Blueprint $table): void {
            $table->id();
            $table->string('shift_code')->unique();
            $table->string('name');
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('restotech_cashier_balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transaction_day_id')->nullable()->constrained('restotech_transaction_days')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('restotech_shifts')->nullOnDelete();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('opening_balance_amount')->default(0);
            $table->unsignedBigInteger('closing_balance_amount')->default(0);
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restotech_cashier_balances');
        Schema::dropIfExists('restotech_shifts');
        Schema::dropIfExists('restotech_transaction_days');
    }
};
