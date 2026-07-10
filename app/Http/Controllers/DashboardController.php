<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalProducts' => Product::count(),
            'totalCustomers' => Customer::count(),
            'totalPurchases' => Purchase::count(),
            'totalSales' => Sale::count(),
            'inventoryValue' => Product::query()->selectRaw('COALESCE(SUM(stock_quantity * purchase_price), 0) as value')->value('value'),
            'products' => Product::latest()->take(8)->get(),
            'purchases' => Purchase::with(['customer', 'product'])->latest()->take(8)->get(),
            'sales' => Sale::with('product')->latest()->take(8)->get(),
        ]);
    }
}
