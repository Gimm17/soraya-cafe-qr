<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;

class AdminPosController extends Controller
{
    public function __construct(private OrderService $orders) {}

    /**
     * POS main page.
     */
    public function index()
    {
        $categories = Category::orderBy('sort_order')->get();
        $products = Product::with('activeModGroups.options')
            ->active()
            ->where('is_sold_out', false)
            ->orderBy('name')
            ->get();
        $tables = CafeTable::orderBy('table_no')->get();

        return view('admin.pos', [
            'categories' => $categories,
            'products' => $products,
            'tables' => $tables,
            'posQrisMode' => Setting::getValue('pos_qris_mode', 'external'),
            'posQrisImage' => Setting::getValue('pos_qris_image'),
            'posCashChangeEnabled' => Setting::getValue('pos_cash_change_enabled', '1'),
            'posAudioEnabled' => Setting::getValue('pos_audio_enabled', '1'),
        ]);
    }

    /**
     * JSON endpoint for product data (used by JS).
     */
    public function products()
    {
        $products = Product::with('activeModGroups.options')
            ->active()
            ->where('is_sold_out', false)
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'base_price' => $p->base_price,
                'image_url' => $p->image_url ? asset($p->image_url) : null,
                'category_id' => $p->category_id,
                'modGroups' => $p->activeModGroups->map(fn($g) => [
                    'id' => $g->id,
                    'name' => $g->name,
                    'is_required' => $g->is_required,
                    'max_select' => $g->max_select,
                    'options' => $g->options->map(fn($o) => [
                        'id' => $o->id,
                        'name' => $o->name,
                        'price_modifier' => $o->price_modifier,
                    ]),
                ]),
            ]);

        return response()->json($products);
    }

    /**
     * Create POS order.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:60'],
            'table_id' => ['nullable', 'integer', 'exists:cafe_tables,id'],
            'fulfillment_type' => ['required', 'in:DINE_IN,PICKUP'],
            'payment_method' => ['required', 'in:CASH,QRIS'],
            'cash_received' => ['nullable', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.name' => ['required', 'string'],
            'items.*.unit_price' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:200'],
            'items.*.modifiers' => ['nullable', 'array'],
            'items.*.modifiers.*.id' => ['required', 'integer'],
            'items.*.modifiers.*.group_name' => ['required', 'string'],
            'items.*.modifiers.*.option_name' => ['required', 'string'],
            'items.*.modifiers.*.price_modifier' => ['required', 'integer'],
        ]);

        // Calculate totals
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $lineTotal = $item['unit_price'] * $item['qty'];
            $subtotal += $lineTotal;
        }

        $totals = [
            'subtotal' => $subtotal,
            'tax' => 0,
            'service' => 0,
            'discount' => 0,
            'grand_total' => $subtotal,
        ];

        $order = $this->orders->createPosOrder(
            $data['table_id'] ?? null,
            $data['customer_name'],
            $data['fulfillment_type'],
            $data['items'],
            $totals,
            $data['payment_method'],
            $data['cash_received'] ?? null
        );

        $order->load('table');

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'grand_total' => $order->grand_total,
                'payment_status' => $order->payment_status,
                'payment_method' => $data['payment_method'],
                'table' => $order->table?->table_no,
                'customer_name' => $order->customer_name,
                'receipt_url' => route('admin.orders.receipt', $order),
            ],
        ]);
    }

    /**
     * Manually confirm QRIS payment (for external QRIS mode).
     */
    public function confirmQris(Order $order)
    {
        if ($order->payment_status === 'PAID') {
            return response()->json(['success' => false, 'message' => 'Sudah dibayar.']);
        }

        $order->update(['payment_status' => 'PAID']);

        if ($order->payment) {
            $order->payment->update(['status' => 'settlement']);
        }

        return response()->json([
            'success' => true,
            'grand_total' => $order->grand_total,
        ]);
    }

    /**
     * Check payment status (for QRIS polling).
     */
    public function checkPayment(Order $order)
    {
        return response()->json([
            'payment_status' => $order->payment_status,
            'grand_total' => $order->grand_total,
        ]);
    }
}
