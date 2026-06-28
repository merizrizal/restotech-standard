<?php

use Illuminate\Support\Facades\Artisan;
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

it('can seed dining, menu, and stock relationships', function () {
    Artisan::call('migrate:fresh', [
        '--force' => true,
    ]);

    $diningArea = DiningArea::factory()->create();
    $diningTable = DiningTable::factory()->create([
        'dining_area_id' => $diningArea->id,
    ]);

    $menuCategory = MenuCategory::factory()->create();
    $menuUnit = MenuUnit::factory()->create();
    $menuItem = MenuItem::factory()->create([
        'menu_category_id' => $menuCategory->id,
        'menu_unit_id' => $menuUnit->id,
    ]);

    $inventoryCategory = InventoryCategory::factory()->create();
    $inventoryItem = InventoryItem::factory()->create([
        'inventory_category_id' => $inventoryCategory->id,
    ]);
    $inventorySku = InventorySku::factory()->create([
        'inventory_item_id' => $inventoryItem->id,
    ]);

    $recipeItem = MenuRecipeItem::factory()->create([
        'menu_item_id' => $menuItem->id,
        'inventory_sku_id' => $inventorySku->id,
        'quantity' => 2.500,
    ]);

    $storageLocation = StorageLocation::factory()->create();
    $stockBalance = StockBalance::factory()->create([
        'inventory_sku_id' => $inventorySku->id,
        'storage_location_id' => $storageLocation->id,
        'storage_rack_id' => null,
        'on_hand_quantity' => 12.500,
    ]);

    expect($diningTable->diningArea->is($diningArea))->toBeTrue();
    expect($diningArea->diningTables)->toHaveCount(1);
    expect($menuItem->menuCategory->is($menuCategory))->toBeTrue();
    expect($menuItem->menuUnit->is($menuUnit))->toBeTrue();
    expect($menuItem->recipeItems)->toHaveCount(1);
    expect($menuItem->recipeItems->first()->is($recipeItem))->toBeTrue();
    expect($recipeItem->inventorySku->is($inventorySku))->toBeTrue();
    expect($inventorySku->stockBalances)->toHaveCount(1);
    expect($inventorySku->stockBalances->first()->is($stockBalance))->toBeTrue();
    expect($storageLocation->stockBalances)->toHaveCount(1);
});
