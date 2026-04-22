<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f5f5f5; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>Laporan Penjualan</h2>
    <p class="muted">Periode: {{ $from->toDateString() }} s/d {{ $to->toDateString() }}</p>
    <p><strong>Total Qty:</strong> {{ $totalQty }}<br><strong>Total Penjualan:</strong> Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}</p>

    <table>
        <thead>
            <tr>
                <th>Order</th>
                <th>Produk</th>
                <th class="right">Qty</th>
                <th class="right">Subtotal</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->order->order_code }}</td>
                    <td>{{ $row->product->name }}</td>
                    <td class="right">{{ $row->qty }}</td>
                    <td class="right">Rp {{ number_format((float) $row->subtotal, 0, ',', '.') }}</td>
                    <td>{{ $row->order->created_at?->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

