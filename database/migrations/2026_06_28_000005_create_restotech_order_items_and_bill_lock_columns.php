<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restotech_table_sessions', function (Blueprint $table): void {
            $table->unsignedBigInteger('subtotal_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('net_amount')->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('service_charge_amount')->default(0);
            $table->unsignedBigInteger('grand_total_amount')->default(0);
            $table->boolean('bill_printed')->default(false);
            $table->timestamp('bill_printed_at')->nullable();
        });

        Schema::create('restotech_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('table_session_id')->constrained('restotech_table_sessions')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('restotech_order_items')->nullOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained('restotech_menu_items')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->decimal('quantity', 12, 3)->default(1);
            $table->unsignedBigInteger('unit_price_amount')->default(0);
            $table->unsignedBigInteger('line_subtotal_amount')->default(0);
            $table->string('discount_type')->default('Percent');
            $table->unsignedBigInteger('discount_value')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('line_total_amount')->default(0);
            $table->boolean('is_free')->default(false);
            $table->timestamp('free_at')->nullable();
            $table->boolean('is_void')->default(false);
            $table->timestamp('void_at')->nullable();
            $table->timestamp('discounted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restotech_order_items');

        Schema::table('restotech_table_sessions', function (Blueprint $table): void {
            $table->dropColumn([
                'subtotal_amount',
                'discount_amount',
                'net_amount',
                'tax_amount',
                'service_charge_amount',
                'grand_total_amount',
                'bill_printed',
                'bill_printed_at',
            ]);
        });
    }
};
