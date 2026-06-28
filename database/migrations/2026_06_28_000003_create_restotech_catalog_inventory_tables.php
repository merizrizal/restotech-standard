<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restotech_dining_areas', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_dining_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dining_area_id')->nullable()->constrained('restotech_dining_areas')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_tax_exemption')->default(false);
            $table->boolean('allow_service_charge_exemption')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_menu_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_discount')->default(true);
            $table->boolean('supports_queue')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_menu_units', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('restotech_inventory_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_category_id')->nullable()->constrained('restotech_inventory_categories')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('base_unit')->nullable();
            $table->decimal('minimum_stock_quantity', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_storage_locations', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_storage_racks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('storage_location_id')->nullable()->constrained('restotech_storage_locations')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_inventory_skus', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_item_id')->nullable()->constrained('restotech_inventory_items')->nullOnDelete();
            $table->string('sku_code')->unique();
            $table->string('sku_name');
            $table->string('barcode')->nullable();
            $table->decimal('minimum_stock_quantity', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_category_id')->nullable()->constrained('restotech_menu_categories')->nullOnDelete();
            $table->foreignId('menu_unit_id')->nullable()->constrained('restotech_menu_units')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('sale_price_amount')->default(0);
            $table->unsignedBigInteger('cost_amount')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_discount')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_menu_condiments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_item_id')->nullable()->constrained('restotech_menu_items')->nullOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('additional_price_amount')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_menu_recipe_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_item_id')->nullable()->constrained('restotech_menu_items')->nullOnDelete();
            $table->foreignId('inventory_sku_id')->nullable()->constrained('restotech_inventory_skus')->nullOnDelete();
            $table->decimal('quantity', 12, 3)->default(0);
            $table->boolean('is_optional')->default(false);
            $table->text('notes')->nullable();
            $table->unique(['menu_item_id', 'inventory_sku_id'], 'restotech_menu_recipe_uq');
            $table->timestamps();
        });

        Schema::create('restotech_stock_balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_sku_id')->nullable()->constrained('restotech_inventory_skus')->nullOnDelete();
            $table->foreignId('storage_location_id')->nullable()->constrained('restotech_storage_locations')->nullOnDelete();
            $table->foreignId('storage_rack_id')->nullable()->constrained('restotech_storage_racks')->nullOnDelete();
            $table->decimal('on_hand_quantity', 12, 3)->default(0);
            $table->decimal('reserved_quantity', 12, 3)->default(0);
            $table->decimal('minimum_stock_quantity', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unique(['inventory_sku_id', 'storage_location_id', 'storage_rack_id'], 'restotech_stock_balance_uq');
            $table->timestamps();
        });

        Schema::create('restotech_stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_sku_id')->nullable()->constrained('restotech_inventory_skus')->nullOnDelete();
            $table->foreignId('storage_location_id')->nullable()->constrained('restotech_storage_locations')->nullOnDelete();
            $table->foreignId('storage_rack_id')->nullable()->constrained('restotech_storage_racks')->nullOnDelete();
            $table->string('movement_type');
            $table->decimal('quantity', 12, 3);
            $table->timestamp('occurred_at')->useCurrent();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('reference_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restotech_stock_movements');
        Schema::dropIfExists('restotech_stock_balances');
        Schema::dropIfExists('restotech_menu_recipe_items');
        Schema::dropIfExists('restotech_menu_condiments');
        Schema::dropIfExists('restotech_menu_items');
        Schema::dropIfExists('restotech_inventory_skus');
        Schema::dropIfExists('restotech_storage_racks');
        Schema::dropIfExists('restotech_storage_locations');
        Schema::dropIfExists('restotech_inventory_items');
        Schema::dropIfExists('restotech_inventory_categories');
        Schema::dropIfExists('restotech_menu_units');
        Schema::dropIfExists('restotech_menu_categories');
        Schema::dropIfExists('restotech_dining_tables');
        Schema::dropIfExists('restotech_dining_areas');
    }
};
