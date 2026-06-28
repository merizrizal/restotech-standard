<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Restotech\Standard\Models\CashierBalance;
use Restotech\Standard\Models\DiningArea;
use Restotech\Standard\Models\DiningTable;
use Restotech\Standard\Models\InventoryCategory;
use Restotech\Standard\Models\InventoryItem;
use Restotech\Standard\Models\InventorySku;
use Restotech\Standard\Models\MenuCategory;
use Restotech\Standard\Models\MenuItem;
use Restotech\Standard\Models\MenuRecipeItem;
use Restotech\Standard\Models\MenuUnit;
use Restotech\Standard\Models\StockBalance;
use Restotech\Standard\Models\StorageLocation;
use Restotech\Standard\Models\TransactionDay;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $demoEmail = config('restotech-standard.demo.user_email', 'demo@restotech.test');
        $demoPassword = config('restotech-standard.demo.user_password', 'password');

        $user = User::query()->updateOrCreate(
            ['email' => $demoEmail],
            [
                'name' => 'Restotech Demo',
                'password' => Hash::make($demoPassword),
                'email_verified_at' => now(),
            ]
        );

        $transactionDay = TransactionDay::query()->updateOrCreate(
            ['business_date' => now()->toDateString()],
            [
                'started_at' => now(),
                'ended_at' => null,
                'status' => 'open',
                'notes' => 'Demo transaction day',
            ]
        );

        CashierBalance::query()->updateOrCreate(
            [
                'transaction_day_id' => $transactionDay->id,
                'status' => 'open',
            ],
            [
                'user_id' => $user->id,
                'opened_at' => now(),
                'closed_at' => null,
                'opening_balance_amount' => 500000,
                'closing_balance_amount' => 0,
                'notes' => 'Demo cashier balance',
            ]
        );

        $diningArea = DiningArea::query()->updateOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Main Hall',
                'is_active' => true,
                'sort_order' => 1,
                'notes' => 'Demo dining area',
            ]
        );

        DiningTable::query()->updateOrCreate(
            ['code' => 'T-01'],
            [
                'dining_area_id' => $diningArea->id,
                'name' => 'Table 01',
                'capacity' => 4,
                'is_active' => true,
                'allow_tax_exemption' => false,
                'allow_service_charge_exemption' => false,
                'notes' => 'Demo dining table',
            ]
        );

        $menuCategory = MenuCategory::query()->updateOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Main Courses',
                'is_active' => true,
                'allow_discount' => true,
                'supports_queue' => false,
                'notes' => 'Demo menu category',
            ]
        );

        $menuUnit = MenuUnit::query()->updateOrCreate(
            ['code' => 'PCS'],
            [
                'name' => 'Piece',
                'symbol' => 'pcs',
                'is_active' => true,
            ]
        );

        $inventoryCategory = InventoryCategory::query()->updateOrCreate(
            ['code' => 'ING'],
            [
                'name' => 'Ingredients',
                'is_active' => true,
                'notes' => 'Demo inventory category',
            ]
        );

        $inventoryItem = InventoryItem::query()->updateOrCreate(
            ['code' => 'RICE'],
            [
                'inventory_category_id' => $inventoryCategory->id,
                'name' => 'Rice',
                'base_unit' => 'gram',
                'minimum_stock_quantity' => 5000,
                'is_active' => true,
                'notes' => 'Demo inventory item',
            ]
        );

        $storageLocation = StorageLocation::query()->updateOrCreate(
            ['code' => 'MAIN-STORE'],
            [
                'name' => 'Main Store',
                'is_active' => true,
                'notes' => 'Demo storage location',
            ]
        );

        $inventorySku = InventorySku::query()->updateOrCreate(
            ['sku_code' => 'RICE-001'],
            [
                'inventory_item_id' => $inventoryItem->id,
                'sku_name' => 'Rice SKU',
                'barcode' => 'RICE001',
                'minimum_stock_quantity' => 5000,
                'is_active' => true,
                'notes' => 'Demo inventory SKU',
            ]
        );

        $menuItem = MenuItem::query()->updateOrCreate(
            ['code' => 'RICE-BOWL'],
            [
                'menu_category_id' => $menuCategory->id,
                'menu_unit_id' => $menuUnit->id,
                'name' => 'Rice Bowl',
                'sale_price_amount' => 25000,
                'cost_amount' => 12000,
                'image_path' => null,
                'is_active' => true,
                'allow_discount' => true,
                'notes' => 'Demo menu item',
            ]
        );

        MenuRecipeItem::query()->updateOrCreate(
            [
                'menu_item_id' => $menuItem->id,
                'inventory_sku_id' => $inventorySku->id,
            ],
            [
                'quantity' => 250,
                'is_optional' => false,
                'notes' => 'Demo recipe item',
            ]
        );

        StockBalance::query()->updateOrCreate(
            [
                'inventory_sku_id' => $inventorySku->id,
                'storage_location_id' => $storageLocation->id,
                'storage_rack_id' => null,
            ],
            [
                'on_hand_quantity' => 50000,
                'reserved_quantity' => 0,
                'minimum_stock_quantity' => 5000,
                'is_active' => true,
            ]
        );
    }
}
