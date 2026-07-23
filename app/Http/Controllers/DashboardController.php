<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductUnit;
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
            'availableUnits' => ProductUnit::where('status', 'available')->count(),
            'inventoryValue' => ProductUnit::where('status', 'available')->sum('cost_price'),
            'totalOrders' => Order::count(),
            'products' => Product::withCount('availableUnits')->latest()->take(8)->get(),
            'purchases' => Purchase::with(['customer', 'product'])->latest()->take(8)->get(),
            'sales' => Sale::with('product')->latest()->take(8)->get(),
        ]);
    }
}
