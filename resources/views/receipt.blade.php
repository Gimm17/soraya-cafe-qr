<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_code }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4ff;
            color: #1a1a1a;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Receipt wrapper ── */
        .receipt {
            max-width: 400px;
            margin: 20px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(30,58,138,.12);
            overflow: hidden;
        }

        /* ── Header ── */
        .rh {
            text-align: center;
            padding: 24px 24px 18px;
        }
        .rh-logo {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: #1E3A8A;
            color: #fff;
            font-size: 22px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .rh-logo img {
            width: 52px; height: 52px;
            border-radius: 50%;
            object-fit: cover;
        }
        .rh-name {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: .5px;
            color: #1E3A8A;
            text-transform: uppercase;
        }
        .rh-tagline {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ── Meta rows ── */
        .rmeta {
            padding: 12px 22px;
            border-top: 1px solid #f1f5f9;
        }
        .rmeta-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 4px 0;
        }
        .rmeta-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .rmeta-val {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            text-align: right;
        }

        /* ── Items section ── */
        .ri {
            padding: 0 22px;
            border-top: 1px solid #f1f5f9;
        }
        .ri-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 12px 0 8px;
        }
        .ri-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f8fafc;
        }
        .ri-row:last-child { border-bottom: none; }
        .ri-qty {
            min-width: 24px;
            height: 22px;
            background: #1E3A8A;
            color: #fff;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
            flex-shrink: 0;
        }
        .ri-info { flex: 1; }
        .ri-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }
        .ri-mods {
            font-size: 10px;
            color: #94a3b8;
            font-style: italic;
            margin-top: 1px;
        }
        .ri-note {
            font-size: 10px;
            color: #b45309;
            margin-top: 1px;
        }
        .ri-price {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            text-align: right;
            white-space: nowrap;
        }

        /* ── Dashed separator ── */
        .dashed {
            border: none;
            border-top: 2px dashed #cbd5e1;
            margin: 4px 22px;
        }

        /* ── Totals ── */
        .rtotal {
            padding: 10px 22px;
        }
        .rtotal-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #64748b;
            padding: 3px 0;
        }
        .rtotal-grand {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 4px;
            border-top: 1px solid #f1f5f9;
            margin-top: 4px;
        }
        .rtotal-grand-label {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }
        .rtotal-grand-amount {
            font-size: 18px;
            font-weight: 800;
            color: #1E3A8A;
        }

        /* ── Payment pill ── */
        .rpay {
            text-align: center;
            padding: 6px 22px 14px;
        }
        .rpay-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: #475569;
        }
        .rpay-dot {
            width: 16px; height: 16px;
            border-radius: 50%;
            border: 2px solid #22c55e;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .rpay-dot::after {
            content: '';
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #22c55e;
        }

        /* ── Blue footer ── */
        .rf {
            background: #1E3A8A;
            text-align: center;
            padding: 18px 22px;
            margin-top: 2px;
        }
        .rf-icon {
            font-size: 22px;
            margin-bottom: 6px;
        }
        .rf-title {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }
        .rf-sub {
            font-size: 11px;
            color: #93c5fd;
            margin-top: 3px;
        }
        .rf-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,.15);
            margin: 12px 0 10px;
        }
        .rf-foot {
            font-size: 10px;
            letter-spacing: .5px;
            color: #60a5fa;
            text-transform: uppercase;
        }

        /* ── Action buttons (screen only) ── */
        .receipt-actions {
            max-width: 400px;
            margin: 0 auto 24px;
            padding: 0 16px;
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 11px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-primary { background: #1E3A8A; color: #fff; }
        .btn-outline { background: #fff; color: #555; border: 1px solid #ddd; }

        @media print {
            body { background: #fff; }
            .receipt { max-width: 100%; margin: 0; border-radius: 0; box-shadow: none; }
            .receipt-actions { display: none !important; }
            @page { margin: 6mm; }
        }
        @media print and (max-width: 80mm) {
            .rh-name { font-size: 15px; }
            .rtotal-grand-amount { font-size: 15px; }
            .rmeta-row { flex-direction: column; }
            @page { margin: 3mm; }
        }
    </style>
</head>
<body>

<div class="receipt">

    {{-- ── Header ── --}}
    <div class="rh">
        @if(($receiptShowLogo ?? '0') === '1' && !empty($receiptLogoUrl))
            <div class="rh-logo"><img src="{{ $receiptLogoUrl }}" alt="Logo"></div>
        @else
            <div class="rh-logo">{{ strtoupper(substr($cafeName, 0, 1)) }}</div>
        @endif
        <div class="rh-name">{{ $cafeName }}</div>
        <div class="rh-tagline">{{ $cafeTagline }}</div>
    </div>

    {{-- ── Meta ── --}}
    <div class="rmeta">
        <div class="rmeta-row">
            <span class="rmeta-label">Order ID</span>
            <span class="rmeta-val">{{ $order->order_code }}</span>
        </div>
        <div class="rmeta-row">
            <span class="rmeta-label">Date</span>
            <span class="rmeta-val">{{ $order->created_at->format('d M Y, H:i') }}</span>
        </div>
        <div class="rmeta-row">
            <span class="rmeta-label">Service</span>
            <span class="rmeta-val">
                @if($order->table)Meja {{ $order->table->table_no }} • @endif{{ $order->fulfillment_type === 'DINE_IN' ? 'Dine In' : 'Takeaway' }}
            </span>
        </div>
        @if(($receiptShowCustomer ?? '1') === '1')
        <div class="rmeta-row">
            <span class="rmeta-label">Customer</span>
            <span class="rmeta-val">{{ $order->customer_name }}</span>
        </div>
        @endif
    </div>

    {{-- ── Items ── --}}
    <div class="ri">
        <div class="ri-title">Order Summary</div>
        @foreach($order->items as $item)
        <div class="ri-row">
            <div class="ri-qty">{{ $item->qty }}x</div>
            <div class="ri-info">
                <div class="ri-name">{{ $item->product_name }}</div>
                @if($item->mods->isNotEmpty())
                    <div class="ri-mods">{{ $item->mods_summary }}</div>
                @endif
                @if($item->note)
                    <div class="ri-note">📝 {{ $item->note }}</div>
                @endif
            </div>
            <div class="ri-price">Rp {{ number_format($item->line_total, 0, ',', '.') }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Separator ── --}}
    <hr class="dashed">

    {{-- ── Totals ── --}}
    <div class="rtotal">
        <div class="rtotal-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
        <div class="rtotal-row">
            <span>Tax (0%)</span>
            <span>Rp 0</span>
        </div>
        <div class="rtotal-grand">
            <span class="rtotal-grand-label">Grand Total</span>
            <span class="rtotal-grand-amount">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- ── Payment pill ── --}}
    @if(($receiptShowPayment ?? '1') === '1')
    <div class="rpay">
        <span class="rpay-pill">
            <span class="rpay-dot"></span>
            Paid via {{ $paymentMethod }}
        </span>
    </div>
    @endif

    {{-- ── Blue footer ── --}}
    <div class="rf">
        <div class="rf-icon">☕</div>
        <div class="rf-title">{{ $receiptFooterText ?? 'Terima kasih sudah berkunjung!' }}</div>
        <div class="rf-sub">Sampai jumpa lagi di {{ $cafeName }}.</div>
        @php $location = $receiptLocation ?? $cafeAddress ?? null; @endphp
        @if($location || $cafeWhatsapp)
        <hr class="rf-divider">
        <div class="rf-foot">
            @if($location){{ $location }}@endif
            @if($location && $cafeWhatsapp) • @endif
            @if($cafeWhatsapp)WA: {{ $cafeWhatsapp }}@endif
        </div>
        @endif
    </div>

</div>

{{-- Actions (hidden on print) --}}
<div class="receipt-actions">
    <button class="btn btn-primary" onclick="window.print()">🖨️ Cetak / Save PDF</button>
    <a href="{{ $backUrl }}" class="btn btn-outline">← Kembali</a>
</div>

</body>
</html>
