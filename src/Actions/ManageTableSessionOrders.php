<?php

namespace Restotech\Standard\Actions;

use DomainException;
use Restotech\Standard\Models\MenuItem;
use Restotech\Standard\Models\OrderItem;
use Restotech\Standard\Models\TableSession;

class ManageTableSessionOrders
{
    public function addItem(TableSession $tableSession, MenuItem $menuItem, float $quantity = 1, ?string $notes = null, ?OrderItem $parentItem = null): OrderItem
    {
        $this->ensureMutable($tableSession);

        $orderItem = new OrderItem([
            'table_session_id' => $tableSession->id,
            'parent_id' => $parentItem?->id,
            'menu_item_id' => $menuItem->id,
            'notes' => $notes,
            'quantity' => $quantity,
            'unit_price_amount' => (int) $menuItem->sale_price_amount,
            'discount_type' => 'Percent',
            'discount_value' => 0,
            'is_free' => false,
            'is_void' => false,
        ]);

        $this->applyCalculatedAmounts($orderItem);
        $orderItem->save();

        return $orderItem->refresh();
    }

    public function changeQuantity(OrderItem $orderItem, float $quantity): OrderItem
    {
        $tableSession = $this->resolveTableSession($orderItem);
        $this->ensureMutable($tableSession);

        $orderItem->quantity = $quantity;
        $this->applyCalculatedAmounts($orderItem);
        $orderItem->save();

        return $orderItem->refresh();
    }

    public function addNotes(OrderItem $orderItem, ?string $notes): OrderItem
    {
        $tableSession = $this->resolveTableSession($orderItem);
        $this->ensureMutable($tableSession);

        $orderItem->notes = $notes;
        $orderItem->save();

        return $orderItem->refresh();
    }

    public function voidItem(OrderItem $orderItem): OrderItem
    {
        $tableSession = $this->resolveTableSession($orderItem);
        $this->ensureMutable($tableSession);

        $orderItem->is_void = true;
        $orderItem->is_free = false;
        $orderItem->free_at = null;
        $orderItem->void_at = now();
        $orderItem->discount_type = 'Value';
        $orderItem->discount_value = 0;
        $orderItem->discounted_at = now();
        $this->applyCalculatedAmounts($orderItem);
        $orderItem->save();

        return $orderItem->refresh();
    }

    public function freeItem(OrderItem $orderItem): OrderItem
    {
        $tableSession = $this->resolveTableSession($orderItem);
        $this->ensureMutable($tableSession);

        $subTotalAmount = $this->calculateSubTotalAmount($orderItem);

        $orderItem->is_free = true;
        $orderItem->is_void = false;
        $orderItem->void_at = null;
        $orderItem->free_at = now();
        $orderItem->discount_type = 'Value';
        $orderItem->discount_value = $subTotalAmount;
        $orderItem->discounted_at = now();
        $this->applyCalculatedAmounts($orderItem);
        $orderItem->save();

        return $orderItem->refresh();
    }

    public function applyDiscount(OrderItem $orderItem, string $discountType, int|float $discountValue): OrderItem
    {
        $tableSession = $this->resolveTableSession($orderItem);
        $this->ensureMutable($tableSession);

        $orderItem->discount_type = $discountType;
        $orderItem->discount_value = max(0, (int) round($discountValue));
        $orderItem->discounted_at = now();
        $this->applyCalculatedAmounts($orderItem);
        $orderItem->save();

        return $orderItem->refresh();
    }

    public function generateBill(TableSession $tableSession): TableSession
    {
        $tableSession->loadMissing('orderItems');

        $billableOrderItems = $tableSession->orderItems->filter(
            fn (OrderItem $orderItem): bool => ! $orderItem->is_void,
        );

        $subtotalAmount = (int) $billableOrderItems->sum('line_subtotal_amount');
        $discountAmount = (int) $billableOrderItems->sum('discount_amount');
        $netAmount = max(0, $subtotalAmount - $discountAmount);
        $taxAmount = (int) round($netAmount * ((float) $tableSession->tax_rate / 100));
        $serviceChargeAmount = (int) round($netAmount * ((float) $tableSession->service_charge_rate / 100));
        $grandTotalAmount = $netAmount + $taxAmount + $serviceChargeAmount;

        $tableSession->markBillPrinted([
            'subtotal_amount' => $subtotalAmount,
            'discount_amount' => $discountAmount,
            'net_amount' => $netAmount,
            'tax_amount' => $taxAmount,
            'service_charge_amount' => $serviceChargeAmount,
            'grand_total_amount' => $grandTotalAmount,
        ]);
        $tableSession->save();

        return $tableSession->refresh();
    }

    public function unlockBill(TableSession $tableSession): TableSession
    {
        $tableSession->unlockBill();
        $tableSession->save();

        return $tableSession->refresh();
    }

    private function ensureMutable(TableSession $tableSession): void
    {
        if ($tableSession->isBillLocked()) {
            throw new DomainException('TABLE_SESSION_BILL_LOCKED');
        }
    }

    private function resolveTableSession(OrderItem $orderItem): TableSession
    {
        $tableSession = $orderItem->tableSession;

        if (! $tableSession) {
            throw new DomainException('TABLE_SESSION_NOT_FOUND');
        }

        return $tableSession;
    }

    private function applyCalculatedAmounts(OrderItem $orderItem): void
    {
        $subTotalAmount = $this->calculateSubTotalAmount($orderItem);

        if ($orderItem->is_void) {
            $discountAmount = 0;
            $lineTotalAmount = 0;
        } elseif ($orderItem->is_free) {
            $discountAmount = $subTotalAmount;
            $lineTotalAmount = 0;
        } else {
            $discountAmount = $this->calculateDiscountAmount($orderItem, $subTotalAmount);
            $lineTotalAmount = max(0, $subTotalAmount - $discountAmount);
        }

        $orderItem->line_subtotal_amount = $subTotalAmount;
        $orderItem->discount_amount = $discountAmount;
        $orderItem->line_total_amount = $lineTotalAmount;
    }

    private function calculateSubTotalAmount(OrderItem $orderItem): int
    {
        return (int) round(((float) $orderItem->quantity) * (int) $orderItem->unit_price_amount);
    }

    private function calculateDiscountAmount(OrderItem $orderItem, int $subTotalAmount): int
    {
        $discountValue = max(0, (int) round((float) $orderItem->discount_value));

        if ($subTotalAmount === 0) {
            return 0;
        }

        if ($orderItem->discount_type === 'Value') {
            return min($subTotalAmount, $discountValue);
        }

        $percent = min(100, $discountValue);

        return (int) round($subTotalAmount * ($percent / 100));
    }
}
