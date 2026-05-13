<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController 
{
    public function index()
    {
        $invoices = Invoice::with('order')->latest()->paginate(20);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = Invoice::with('order')->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function export(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $invoiceCode = 'INV' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_code' => $invoiceCode,
            'total' => $order->total_price,
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);
        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Xuất hóa đơn thành công!');
    }

    public function exportPdf($id)
    {
        $invoice = \App\Models\Invoice::with('order.items.product', 'order.items.variant')->findOrFail($id);
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->download('hoa-don-'.$invoice->invoice_code.'.pdf');
    }
} 