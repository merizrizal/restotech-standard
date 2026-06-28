<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restotech_table_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dining_table_id')->constrained('restotech_dining_tables')->cascadeOnDelete();
            $table->foreignId('transaction_day_id')->constrained('restotech_transaction_days')->cascadeOnDelete();
            $table->foreignId('cashier_balance_id')->constrained('restotech_cashier_balances')->cascadeOnDelete();
            $table->unsignedBigInteger('opened_by_user_id')->nullable()->index();
            $table->string('status')->default('open');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->string('guest_name')->nullable();
            $table->decimal('tax_rate', 8, 3)->default(0);
            $table->decimal('service_charge_rate', 8, 3)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['dining_table_id', 'status'], 'restotech_table_sessions_table_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restotech_table_sessions');
    }
};
