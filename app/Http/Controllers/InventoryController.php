<?php

namespace App\Http\Controllers;

use App\Models\InventoryUnitIdentifier;
use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __invoke(): View
    {
        $search = trim((string) request('search'));
        $normalizedSearch = InventoryUnitIdentifier::normalize($search);
        $status = request('status', 'available');

        $units = ProductUnit::query()
            ->with(['product', 'identifiers', 'purchase.customer', 'purchase.batch'])
            ->when($search, function (Builder $query) use ($search, $normalizedSearch) {
                $query->where(function (Builder $query) use ($search, $normalizedSearch) {
                    $query->whereHas('identifiers', function (Builder $query) use ($search, $normalizedSearch) {
                        $query->where('value', 'like', "%{$search}%")
                            ->orWhere('normalized_value', 'like', "%{$normalizedSearch}%");
                    })->orWhereHas('product', function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('color', 'like', "%{$search}%")
                            ->orWhere('storage_capacity', 'like', "%{$search}%");
                    });
                });
            })
            ->when(in_array($status, ['available', 'sold'], true), fn (Builder $query) => $query->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('inventory.index', [
            'units' => $units,
            'search' => $search,
            'status' => $status,
            'availableCount' => ProductUnit::where('status', 'available')->count(),
            'soldCount' => ProductUnit::where('status', 'sold')->count(),
            'inventoryValue' => ProductUnit::where('status', 'available')->sum('cost_price'),
        ]);
    }
}
