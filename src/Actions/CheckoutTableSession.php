<?php

namespace Restotech\Standard\Actions;

use DomainException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Restotech\Standard\Models\Employee;
use Restotech\Standard\Models\InventorySku;
use Restotech\Standard\Models\OrderItem;
use Restotech\Standard\Models\SalesInvoice;
use Restotech\Standard\Models\SalesInvoiceItem;
use Restotech\Standard\Models\SalesInvoicePayment;
use Restotech\Standard\Models\StockBalance;
use Restotech\Standard\Models\StockMovement;
use Restotech\Standard\Models\TableSession;
use Restotech\Standard\Models\Voucher;

class CheckoutTableSession
{
    public function handle(TableSession $tableSession, int|array $paymentInput, ?int $processedByUserId = null): SalesInvoice
    {
        return DB::transaction(function () use ($tableSession, $paymentInput, $processedByUserId): SalesInvoice {
            $lockedSession = TableSession::query()
                ->whereKey($tableSession->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingInvoice = SalesInvoice::query()
                ->where('table_session_id', $lockedSession->id)
                ->lockForUpdate()
                ->first();

            if ($existingInvoice) {
                throw new DomainException('TABLE_SESSION_ALREADY_CHECKED_OUT');
            }

            if (! $lockedSession->isBillLocked()) {
                throw new DomainException('BILL_REQUIRED_BEFORE_CHECKOUT');
            }

            $lockedSession->loadMissing(['orderItems.menuItem.recipeItems.inventorySku']);
            $billableOrderItems = $lockedSession->orderItems->filter(
                fn (OrderItem $orderItem): bool => ! $orderItem->is_void,
            );

            $grandTotalAmount = (int) $lockedSession->grand_total_amount;
            $paymentRecords = [];
            $deferredVoucherUpdates = [];

            if (is_int($paymentInput)) {
                if ($paymentInput < $grandTotalAmount) {
                    throw new DomainException('PAYMENT_TOTAL_MISMATCH');
                }

                $paymentRecords[] = [
                    'payment_method_code' => 'cash',
                    'payment_method_name' => 'Cash',
                    'amount' => $paymentInput,
                    'change_amount' => $paymentInput - $grandTotalAmount,
                    'notes' => 'Cash checkout',
                ];
            } else {
                $structuredResult = $this->processStructuredPayments($paymentInput, $grandTotalAmount, $processedByUserId, $deferredVoucherUpdates);
                $paymentRecords = $structuredResult['payment_records'];
            }

            $paidAmount = array_sum(array_map(static fn (array $paymentRecord): int => (int) $paymentRecord['amount'], $paymentRecords));
            $changeAmount = array_sum(array_map(static fn (array $paymentRecord): int => (int) $paymentRecord['change_amount'], $paymentRecords));

            if (is_array($paymentInput) && $paidAmount !== $grandTotalAmount) {
                throw new DomainException('PAYMENT_TOTAL_MISMATCH');
            }

            $salesInvoice = SalesInvoice::create([
                'invoice_number' => sprintf('SI-%d', $lockedSession->id),
                'table_session_id' => $lockedSession->id,
                'transaction_day_id' => $lockedSession->transaction_day_id,
                'cashier_balance_id' => $lockedSession->cashier_balance_id,
                'operator_user_id' => $processedByUserId,
                'issued_at' => now(),
                'status' => 'paid',
                'subtotal_amount' => (int) $lockedSession->subtotal_amount,
                'discount_amount' => (int) $lockedSession->discount_amount,
                'net_amount' => (int) $lockedSession->net_amount,
                'tax_rate' => (float) $lockedSession->tax_rate,
                'service_charge_rate' => (float) $lockedSession->service_charge_rate,
                'tax_amount' => (int) $lockedSession->tax_amount,
                'service_charge_amount' => (int) $lockedSession->service_charge_amount,
                'grand_total_amount' => $grandTotalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'paid_at' => now(),
                'closed_at' => now(),
            ]);

            foreach ($billableOrderItems as $orderItem) {
                SalesInvoiceItem::create([
                    'sales_invoice_id' => $salesInvoice->id,
                    'source_order_item_id' => $orderItem->id,
                    'menu_item_id' => $orderItem->menu_item_id,
                    'menu_item_code' => $orderItem->menuItem?->code ?? (string) $orderItem->menu_item_id,
                    'menu_item_name' => $orderItem->menuItem?->name ?? (string) $orderItem->menu_item_id,
                    'notes' => $orderItem->notes,
                    'quantity' => $orderItem->quantity,
                    'unit_price_amount' => $orderItem->unit_price_amount,
                    'line_subtotal_amount' => $orderItem->line_subtotal_amount,
                    'discount_type' => $orderItem->discount_type,
                    'discount_value' => $orderItem->discount_value,
                    'discount_amount' => $orderItem->discount_amount,
                    'line_total_amount' => $orderItem->line_total_amount,
                    'is_free' => $orderItem->is_free,
                    'is_void' => $orderItem->is_void,
                ]);
            }

            foreach ($paymentRecords as $paymentRecord) {
                SalesInvoicePayment::create([
                    'sales_invoice_id' => $salesInvoice->id,
                    'payment_method_code' => $paymentRecord['payment_method_code'],
                    'payment_method_name' => $paymentRecord['payment_method_name'],
                    'amount' => $paymentRecord['amount'],
                    'change_amount' => $paymentRecord['change_amount'],
                    'paid_at' => now(),
                    'notes' => $paymentRecord['notes'],
                ]);
            }

            foreach ($deferredVoucherUpdates as $voucher) {
                $voucher->used_sales_invoice_id = $salesInvoice->id;
                $voucher->save();
            }

            $this->consumeRecipeStock($salesInvoice, $billableOrderItems);

            $lockedSession->status = 'closed';
            $lockedSession->closed_at = now();
            $lockedSession->save();

            return $salesInvoice->refresh()->load(['invoiceItems', 'payments', 'tableSession']);
        });
    }

    private function processStructuredPayments(array $paymentInput, int $grandTotalAmount, ?int $processedByUserId, array &$deferredVoucherUpdates): array
    {
        $paymentRecords = [];
        $runningTotal = 0;

        foreach ($paymentInput as $paymentLine) {
            $method = (string) ($paymentLine['method'] ?? $paymentLine['payment_method'] ?? $paymentLine['type'] ?? '');
            $amount = (int) round((float) ($paymentLine['amount'] ?? 0));
            $notes = (string) ($paymentLine['notes'] ?? '');

            if ($amount <= 0) {
                throw new DomainException('PAYMENT_TOTAL_MISMATCH');
            }

            $record = match ($method) {
                'cash' => [
                    'payment_method_code' => 'cash',
                    'payment_method_name' => 'Cash',
                    'amount' => $amount,
                    'change_amount' => 0,
                    'notes' => $notes !== '' ? $notes : 'Cash checkout',
                ],
                'employee_credit' => $this->processEmployeeCreditPayment($paymentLine, $amount, $notes, $processedByUserId),
                'voucher' => $this->processVoucherPayment($paymentLine, $amount, $notes, $grandTotalAmount, $processedByUserId, $deferredVoucherUpdates),
                'accounts_receivable', 'account_receivable' => [
                    'payment_method_code' => 'account_receivable',
                    'payment_method_name' => 'Accounts Receivable',
                    'amount' => $amount,
                    'change_amount' => 0,
                    'notes' => $notes !== '' ? $notes : 'Accounts receivable checkout',
                ],
                default => throw new DomainException('PAYMENT_TOTAL_MISMATCH'),
            };

            $paymentRecords[] = $record;
            $runningTotal += $amount;
        }

        if ($runningTotal !== $grandTotalAmount) {
            throw new DomainException('PAYMENT_TOTAL_MISMATCH');
        }

        return [
            'payment_records' => $paymentRecords,
        ];
    }

    private function processEmployeeCreditPayment(array $paymentLine, int $amount, string $notes, ?int $processedByUserId): array
    {
        $employeeId = $paymentLine['employee_id'] ?? $paymentLine['employee_code'] ?? null;

        if ($employeeId === null) {
            throw new DomainException('EMPLOYEE_CREDIT_INSUFFICIENT');
        }

        $employeeQuery = Employee::query()->lockForUpdate();
        $employee = is_numeric($employeeId)
            ? $employeeQuery->whereKey($employeeId)->first()
            : $employeeQuery->where('employee_code', (string) $employeeId)->first();

        if (! $employee || ! $employee->is_active || (int) $employee->remaining_credit_amount < $amount) {
            throw new DomainException('EMPLOYEE_CREDIT_INSUFFICIENT');
        }

        $employee->remaining_credit_amount = max(0, (int) $employee->remaining_credit_amount - $amount);
        $employee->save();

        return [
            'payment_method_code' => 'employee_credit',
            'payment_method_name' => 'Employee Credit',
            'amount' => $amount,
            'change_amount' => 0,
            'notes' => $notes !== '' ? $notes : sprintf('Employee %s credit debit', $employee->employee_code),
        ];
    }

    private function processVoucherPayment(array $paymentLine, int $amount, string $notes, int $grandTotalAmount, ?int $processedByUserId, array &$deferredVoucherUpdates): array
    {
        $voucherId = $paymentLine['voucher_id'] ?? $paymentLine['voucher_code'] ?? null;

        if ($voucherId === null) {
            throw new DomainException('VOUCHER_INVALID');
        }

        $voucherQuery = Voucher::query()->lockForUpdate();
        $voucher = is_numeric($voucherId)
            ? $voucherQuery->whereKey($voucherId)->first()
            : $voucherQuery->where('voucher_code', (string) $voucherId)->first();

        if (! $voucher || ! $voucher->is_active || $voucher->used_at !== null || ! $this->voucherIsValidForToday($voucher)) {
            throw new DomainException('VOUCHER_INVALID');
        }

        $voucherRedeemAmount = $this->resolveVoucherRedeemAmount($voucher, $grandTotalAmount);

        if ($amount !== $voucherRedeemAmount) {
            throw new DomainException('VOUCHER_INVALID');
        }

        $voucher->used_at = now();
        $voucher->used_by_user_id = $processedByUserId;
        $deferredVoucherUpdates[] = $voucher;

        return [
            'payment_method_code' => 'voucher',
            'payment_method_name' => 'Voucher',
            'amount' => $amount,
            'change_amount' => 0,
            'notes' => $notes !== '' ? $notes : sprintf('Voucher %s redemption', $voucher->voucher_code),
        ];
    }

    private function resolveVoucherRedeemAmount(Voucher $voucher, int $grandTotalAmount): int
    {
        $valueAmount = (int) $voucher->voucher_value_amount;

        return match ($voucher->voucher_type) {
            'Percent' => (int) round($grandTotalAmount * ($valueAmount / 100)),
            default => min($valueAmount, $grandTotalAmount),
        };
    }

    private function voucherIsValidForToday(Voucher $voucher): bool
    {
        $today = now()->startOfDay();

        if ($voucher->start_date !== null && $today->lt($voucher->start_date->copy()->startOfDay())) {
            return false;
        }

        if ($voucher->end_date !== null && $today->gt($voucher->end_date->copy()->endOfDay())) {
            return false;
        }

        return true;
    }

    private function consumeRecipeStock(SalesInvoice $salesInvoice, EloquentCollection $billableOrderItems): void
    {
        $requirements = [];

        foreach ($billableOrderItems as $orderItem) {
            foreach ($orderItem->menuItem?->recipeItems ?? [] as $recipeItem) {
                $inventorySkuId = $recipeItem->inventory_sku_id;
                $requiredQuantity = round(((float) $orderItem->quantity) * ((float) $recipeItem->quantity), 3);

                if ($requiredQuantity <= 0 || $inventorySkuId === null) {
                    continue;
                }

                $requirements[$inventorySkuId] = round(($requirements[$inventorySkuId] ?? 0) + $requiredQuantity, 3);
            }
        }

        foreach ($requirements as $inventorySkuId => $requiredQuantity) {
            $stockBalance = StockBalance::query()
                ->where('inventory_sku_id', $inventorySkuId)
                ->lockForUpdate()
                ->orderBy('id')
                ->first();

            if (! $stockBalance) {
                throw new DomainException('STOCK_INSUFFICIENT');
            }

            $availableQuantity = (float) $stockBalance->on_hand_quantity;

            if (! config('restotech-standard.stock.allow_negative_stock', false) && $availableQuantity < $requiredQuantity) {
                throw new DomainException('STOCK_INSUFFICIENT');
            }

            $stockBalance->on_hand_quantity = round($availableQuantity - $requiredQuantity, 3);
            $stockBalance->save();

            StockMovement::create([
                'inventory_sku_id' => $stockBalance->inventory_sku_id,
                'storage_location_id' => $stockBalance->storage_location_id,
                'storage_rack_id' => $stockBalance->storage_rack_id,
                'movement_type' => 'Outflow-Menu',
                'quantity' => $requiredQuantity,
                'occurred_at' => now(),
                'source_type' => SalesInvoice::class,
                'source_id' => $salesInvoice->id,
                'reference_code' => $salesInvoice->invoice_number,
                'notes' => 'Checkout stock consumption',
            ]);
        }
    }
}
