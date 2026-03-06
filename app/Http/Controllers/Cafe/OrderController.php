<?php

namespace App\Http\Controllers\Cafe;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        // Access control: order_code is a random unguessable string (e.g. A-20260210-EDUFDI)
        // that acts as the access token. No session check needed for viewing.
        $order->load('items','feedback','table');
        return view('cafe.order', [
            'order' => $order,
            'tableNo' => $order->table?->table_no ?? session('cafe_table_no'),
        ]);
    }

    /**
     * Render receipt page for buyer (secured with HMAC token).
     */
    public function receipt(Order $order, Request $request)
    {
        // Validate HMAC token
        $expectedToken = hash_hmac('sha256', $order->order_code, config('app.key'));
        if (!hash_equals($expectedToken, $request->query('t', ''))) {
            abort(403, 'Token tidak valid.');
        }

        // Only allow receipt for completed & paid orders
        if ($order->order_status !== 'SELESAI' || $order->payment_status !== 'PAID') {
            abort(403, 'Struk hanya tersedia untuk pesanan yang sudah selesai dan dibayar.');
        }

        $order->load(['items.mods', 'table', 'payment.events']);

        return view('receipt', [
            'order'         => $order,
            'paymentMethod' => $order->payment?->payment_method_label ?? 'Midtrans',
            'cafeName'      => Setting::getValue('cafe_name', 'Soraya'),
            'cafeTagline'   => Setting::getValue('cafe_tagline', 'Coffee & Friends'),
            'cafeAddress'   => Setting::getValue('cafe_address'),
            'cafeWhatsapp'  => Setting::getValue('cafe_whatsapp'),
            'backUrl'       => route('cafe.order.show', $order->order_code),
            // ── Receipt customization ─────────────────────────────────────
            'receiptLogoUrl'        => ($logo = Setting::getValue('receipt_logo')) ? asset($logo) : null,
            'receiptShowLogo'       => Setting::getValue('receipt_show_logo', '0'),
            'receiptLogoSize'       => Setting::getValue('receipt_logo_size', '64'),
            'receiptLogoSpacing'    => Setting::getValue('receipt_logo_spacing', '10'),
            'receiptLocation'       => Setting::getValue('receipt_cafe_location'),
            'receiptCafeName'       => Setting::getValue('receipt_cafe_name') ?: Setting::getValue('cafe_name', 'Soraya Cafe'),
            'receiptCafeTagline'    => Setting::getValue('receipt_cafe_tagline') ?: Setting::getValue('cafe_tagline', ''),
            'receiptWhatsapp'       => Setting::getValue('receipt_whatsapp'),
            'receiptShowTagline'    => Setting::getValue('receipt_show_tagline', '1'),
            'receiptFontFamily'     => Setting::getValue('receipt_font_family', 'system'),
            'receiptHeaderFontSize' => Setting::getValue('receipt_header_font_size', '20'),
            'receiptBodyFontSize'   => Setting::getValue('receipt_body_font_size', '13'),
            'receiptTheme'          => Setting::getValue('receipt_theme', 'normal'),
            'receiptAccentColor'    => Setting::getValue('receipt_accent_color', '#1a1a1a'),
            'receiptTotalColor'     => Setting::getValue('receipt_total_color', '#1a1a1a'),
            'receiptBorderStyle'    => Setting::getValue('receipt_border_style', 'dashed'),
            'receiptBorderColor'    => Setting::getValue('receipt_border_color', '#e5e5e5'),
            'receiptPaperSize'      => Setting::getValue('receipt_paper_size', 'thermal80'),
            'receiptShowCustomer'   => Setting::getValue('receipt_show_customer_info', '1'),
            'receiptShowPayment'    => Setting::getValue('receipt_show_payment_method', '1'),
            'receiptShowItemNotes'  => Setting::getValue('receipt_show_item_notes', '1'),
            'receiptShowOrderType'  => Setting::getValue('receipt_show_order_type', '1'),
            'receiptShowTableNo'    => Setting::getValue('receipt_show_table_no', '1'),
            'receiptFooterText'     => Setting::getValue('receipt_footer_text', 'Terima kasih! 🙏'),
            'receiptFooterSecondary'=> Setting::getValue('receipt_footer_secondary'),
            'receiptFooterFontSize' => Setting::getValue('receipt_footer_font_size', '14'),
        ]);
    }

    public function statusJson(Order $order)
    {
        // Access control: same as show() — order_code is the auth token

        // Generate ETag based on order's last update and status
        $etag = 'W/"' . $order->updated_at->timestamp . '-' . $order->order_status . '-' . $order->payment_status . '"';
        
        // Check if client's cached version is still valid
        $clientEtag = request()->headers->get('If-None-Match');
        if ($clientEtag === $etag) {
            return response('', 304)->setEtag($etag, true);
        }

        return response()->json([
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'updated_at' => $order->updated_at?->toISOString(),
        ])->setEtag($etag, true)
           ->header('Cache-Control', 'private, no-cache');
    }

    /**
     * Show order history for current table session
     */
    public function history()
    {
        $tableId = session('cafe_table_id');
        
        if (!$tableId) {
            return redirect()->route('cafe.menu')->with('error', 'Silakan scan QR meja terlebih dahulu.');
        }

        // Get all orders for this table in the last 24 hours
        $orders = Order::where('table_id', $tableId)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->with(['items', 'table'])
            ->get();

        return view('cafe.history', [
            'orders' => $orders,
            'tableNo' => session('cafe_table_no'),
        ]);
    }

    /**
     * Cancel an unpaid order
     */
    public function cancel(Order $order)
    {
        $tableId = session('cafe_table_id');

        // Verify order belongs to this table
        if ($order->table_id !== $tableId) {
            return back()->with('error', 'Anda tidak dapat membatalkan pesanan ini.');
        }

        // Only allow cancel for unpaid orders
        if ($order->payment_status === 'PAID') {
            return back()->with('error', 'Pesanan yang sudah dibayar tidak dapat dibatalkan.');
        }

        // Delete the order
        $order->items()->delete();
        $order->payment()->delete();
        $order->delete();

        return redirect()->route('cafe.history')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Generate HMAC token for receipt URL.
     */
    public static function receiptToken(string $orderCode): string
    {
        return hash_hmac('sha256', $orderCode, config('app.key'));
    }
}

