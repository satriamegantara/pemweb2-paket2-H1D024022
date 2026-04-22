<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Collection $rows,
        private readonly string $from,
        private readonly string $to
    ) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Pesanan',
            'Tanggal',
            'Produk',
            'Qty',
            'Harga',
            'Subtotal',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $num = 1;

        return [
            $num++,
            $row->order->order_code,
            $row->order->created_at->format('d/m/Y H:i'),
            $row->product->name,
            $row->qty,
            'Rp ' . number_format($row->price, 0, ',', '.'),
            'Rp ' . number_format($row->subtotal, 0, ',', '.'),
            $this->statusLabel($row->order->status),
        ];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            Order::STATUS_DIBAYAR => 'Dibayar',
            Order::STATUS_DIPROSES => 'Diproses',
            Order::STATUS_DIKIRIM => 'Dikirim',
            Order::STATUS_SELESAI => 'Selesai',
            default => $status,
        };
    }
}
