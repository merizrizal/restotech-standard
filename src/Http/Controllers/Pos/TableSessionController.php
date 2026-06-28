<?php

namespace Restotech\Standard\Http\Controllers\Pos;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Restotech\Standard\Actions\OpenTableSession;
use Restotech\Standard\Models\DiningTable;

class TableSessionController extends Controller
{
    public function store(Request $request, OpenTableSession $openTableSession): JsonResponse
    {
        $validated = $request->validate([
            'dining_table_id' => ['required', 'integer', 'exists:restotech_dining_tables,id'],
        ]);

        $diningTable = DiningTable::query()->findOrFail($validated['dining_table_id']);
        $tableSession = $openTableSession->handle($diningTable, $request->user()?->getAuthIdentifier());

        return response()->json([
            'data' => [
                'id' => $tableSession->id,
                'dining_table_id' => $tableSession->dining_table_id,
                'transaction_day_id' => $tableSession->transaction_day_id,
                'cashier_balance_id' => $tableSession->cashier_balance_id,
                'status' => $tableSession->status,
                'opened_at' => $tableSession->opened_at?->toIso8601String(),
                'tax_rate' => (string) $tableSession->tax_rate,
                'service_charge_rate' => (string) $tableSession->service_charge_rate,
            ],
        ], 201);
    }
}
