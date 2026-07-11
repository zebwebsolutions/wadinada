<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $order->id }} - Wadi Nada Phone</title>
    <style>
        body { margin: 0; background: #f4f4f5; color: #18181b; font-family: Arial, sans-serif; }
        .page { max-width: 760px; margin: 32px auto; padding: 0 16px; }
        .receipt { background: #fff; border: 1px solid #d4d4d8; border-radius: 8px; overflow: hidden; }
        .brand { background: #18181b; color: #fff; padding: 24px; text-align: center; }
        .brand h1 { margin: 0; font-size: 28px; letter-spacing: .04em; }
        .brand p { margin: 8px 0 0; font-size: 14px; color: #e4e4e7; }
        .content { padding: 24px; }
        .meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
        .box { border: 1px solid #e4e4e7; border-radius: 6px; padding: 12px; }
        .label { display: block; color: #71717a; font-size: 12px; text-transform: uppercase; }
        .value { display: block; margin-top: 4px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border-bottom: 1px solid #e4e4e7; padding: 12px 8px; text-align: left; }
        th { color: #71717a; font-size: 12px; text-transform: uppercase; }
        .total { text-align: right; font-size: 22px; font-weight: 700; margin-top: 18px; }
        .actions { margin-top: 18px; text-align: center; }
        button { border: 0; border-radius: 6px; background: #18181b; color: #fff; cursor: pointer; font-weight: 700; padding: 10px 16px; }
        @media print {
            body { background: #fff; }
            .page { margin: 0; max-width: none; padding: 0; }
            .receipt { border: 0; border-radius: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="receipt">
            <header class="brand">
                <h1>Wadi Nada Phone</h1>
                <p>Shop 15, Khalid Bin Waleed Street, Sharq</p>
            </header>

            <div class="content">
                <div class="meta">
                    <div class="box">
                        <span class="label">Receipt</span>
                        <span class="value">#{{ $order->id }}</span>
                    </div>
                    <div class="box">
                        <span class="label">Date</span>
                        <span class="value">{{ $order->sold_at->format('d M Y') }}</span>
                    </div>
                    <div class="box">
                        <span class="label">Customer</span>
                        <span class="value">{{ $order->customer_name ?: 'Walk-in customer' }}</span>
                    </div>
                    <div class="box">
                        <span class="label">Phone</span>
                        <span class="value">{{ $order->customer_phone ?: '-' }}</span>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>{{ $order->product->name }}</strong><br>
                                <span>{{ $order->product->brand ?: '' }}</span>
                                @if ($order->product->sku || $order->product->imei1 || $order->product->imei2)
                                    <br><span>
                                        {{ $order->product->sku ? 'SKU: '.$order->product->sku : '' }}
                                        {{ $order->product->imei1 ? ' IMEI1: '.$order->product->imei1 : '' }}
                                        {{ $order->product->imei2 ? ' IMEI2: '.$order->product->imei2 : '' }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format($order->unit_price, 3) }} KD</td>
                            <td>{{ number_format($order->total_amount, 3) }} KD</td>
                        </tr>
                    </tbody>
                </table>

                <p class="total">Total: {{ number_format($order->total_amount, 3) }} KD</p>
                <p>Payment: {{ $order->payment_method ?: '-' }}</p>
                <p>Thank you for shopping with Wadi Nada Phone.</p>
            </div>
        </section>

        <div class="actions">
            <button onclick="window.print()">Print Receipt</button>
        </div>
    </main>
</body>
</html>
