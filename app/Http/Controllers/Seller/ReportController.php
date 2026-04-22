<?php

namespace App\Http\Controllers\Seller;

use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->parseRange($request);

        $storeId = $request->user()->store?->id;
        if (! $storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $rows = $this->queryRows($storeId, $from, $to)->paginate(10)->withQueryString();

        $summary = $this->queryRows($storeId, $from, $to)->get();
        $totalQty = (int) $summary->sum('qty');
        $totalAmount = (float) $summary->sum('subtotal');

        return view('seller.reports.sales.index', compact('rows', 'from', 'to', 'totalQty', 'totalAmount'));
    }

    public function exportPdf(Request $request)
    {
        [$from, $to] = $this->parseRange($request);

        $storeId = $request->user()->store?->id;
        if (! $storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $rows = $this->queryRows($storeId, $from, $to)->get();
        $totalQty = (int) $rows->sum('qty');
        $totalAmount = (float) $rows->sum('subtotal');

        $pdf = Pdf::loadView('seller.reports.sales.pdf', compact('rows', 'from', 'to', 'totalQty', 'totalAmount'));

        return $pdf->download('laporan-penjualan.pdf');
    }

    public function exportExcel(Request $request)
    {
        [$from, $to] = $this->parseRange($request);

        $storeId = $request->user()->store?->id;
        if (! $storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $rows = $this->queryRows($storeId, $from, $to)->get();
        $fileName = 'laporan-penjualan-' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new SalesReportExport($rows, $from->format('d/m/Y'), $to->format('d/m/Y')), $fileName);
    }

    private function queryRows(int $storeId, Carbon $from, Carbon $to)
    {
        return OrderItem::query()
            ->with(['product', 'order'])
            ->where('store_id', $storeId)
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereIn('status', [
                    Order::STATUS_DIBAYAR,
                    Order::STATUS_DIPROSES,
                    Order::STATUS_DIKIRIM,
                    Order::STATUS_SELESAI,
                ])->whereBetween('created_at', [$from, $to]);
            })
            ->latest('id');
    }

    /**
     * @return array{0:Carbon,1:Carbon}
     */
    private function parseRange(Request $request): array
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay();
        $toDate = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        return [$fromDate, $toDate];
    }
}

