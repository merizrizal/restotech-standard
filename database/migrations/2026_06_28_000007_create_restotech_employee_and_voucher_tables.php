<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restotech_employees', function (Blueprint $table): void {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('credit_limit_amount')->default(0);
            $table->unsignedBigInteger('remaining_credit_amount')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_vouchers', function (Blueprint $table): void {
            $table->id();
            $table->string('voucher_code')->unique();
            $table->string('voucher_type');
            $table->unsignedBigInteger('voucher_value_amount')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('used_by_user_id')->nullable()->index();
            $table->foreignId('used_sales_invoice_id')->nullable()->constrained('restotech_sales_invoices')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restotech_vouchers');
        Schema::dropIfExists('restotech_employees');
    }
};
