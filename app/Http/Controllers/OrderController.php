<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Sale::query()
            ->with('product')
            ->when(request('search'), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('salesman_name', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%")
                                ->orWhere('imei1', 'like', "%{$search}%")
                                ->orWhere('imei2', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('sold_at')
            ->paginate(12)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Sale $order): View
    {
        $order->load('product');

        return view('orders.show', compact('order'));
    }

    public function print(Sale $order): View
    {
        $order->load('product');

        return view('orders.print', compact('order'));
    }
}
