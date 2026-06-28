<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restotech_sales_invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('table_session_id')->constrained('restotech_table_sessions')->cascadeOnDelete()->unique();
            $table->foreignId('transaction_day_id')->constrained('restotech_transaction_days')->cascadeOnDelete();
            $table->foreignId('cashier_balance_id')->constrained('restotech_cashier_balances')->cascadeOnDelete();
            $table->unsignedBigInteger('operator_user_id')->nullable()->index();
            $table->timestamp('issued_at')->useCurrent();
            $table->string('status')->default('paid');
            $table->unsignedBigInteger('subtotal_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('net_amount')->default(0);
            $table->decimal('tax_rate', 8, 3)->default(0);
            $table->decimal('service_charge_rate', 8, 3)->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('service_charge_amount')->default(0);
            $table->unsignedBigInteger('grand_total_amount')->default(0);
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->unsignedBigInteger('change_amount')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_sales_invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('restotech_sales_invoices')->cascadeOnDelete();
            $table->foreignId('source_order_item_id')->nullable()->constrained('restotech_order_items')->nullOnDelete()->unique();
            $table->foreignId('menu_item_id')->nullable()->constrained('restotech_menu_items')->nullOnDelete();
            $table->string('menu_item_code');
            $table->string('menu_item_name');
            $table->text('notes')->nullable();
            $table->decimal('quantity', 12, 3)->default(1);
            $table->unsignedBigInteger('unit_price_amount')->default(0);
            $table->unsignedBigInteger('line_subtotal_amount')->default(0);
            $table->string('discount_type')->default('Percent');
            $table->unsignedBigInteger('discount_value')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('line_total_amount')->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_void')->default(false);
            $table->timestamps();
        });

        Schema::create('restotech_sales_invoice_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('restotech_sales_invoices')->cascadeOnDelete();
            $table->string('payment_method_code');
            $table->string('payment_method_name');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('change_amount')->default(0);
            $table->timestamp('paid_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restotech_sales_invoice_payments');
        Schema::dropIfExists('restotech_sales_invoice_items');
        Schema::dropIfExists('restotech_sales_invoices');
    }
};
