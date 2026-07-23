<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['items.product', 'items.unit.identifiers'])
            ->when(request('search'), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('customer_id_number', 'like', "%{$search}%")
                        ->orWhere('salesman_name', 'like', "%{$search}%")
                        ->orWhereHas('items.unit', function ($query) use ($search) {
                            $query->where('imei', 'like', "%{$search}%")
                                ->orWhereHas('identifiers', function ($query) use ($search) {
                                    $query->where('value', 'like', "%{$search}%")
                                        ->orWhere('normalized_value', 'like', "%{$search}%");
                                });
                        })
                        ->orWhereHas('items.product', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%")
                                ->orWhere('imei1', 'like', "%{$search}%")
                                ->orWhere('imei2', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('ordered_at')
            ->paginate(12)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.unit.identifiers']);

        return view('orders.show', compact('order'));
    }

    public function print(Order $order): View
    {
        $order->load(['items.product', 'items.unit.identifiers']);

        return view('orders.print', compact('order'));
    }
}
