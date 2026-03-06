<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_code }}</title>
    @php
        $theme       = $receiptTheme ?? 'normal';
        $fontMap     = ['system' => '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif', 'serif' => 'Georgia,"Times New Roman",serif', 'mono' => '"Courier New",Courier,monospace'];
        $fontStack   = $fontMap[$receiptFontFamily ?? 'system'] ?? $fontMap['system'];
        $headerPx    = intval($receiptHeaderFontSize ?? 20);
        $bodyPx      = intval($receiptBodyFontSize ?? 13);
        $accentColor = $receiptAccentColor ?? '#1a1a1a';
        $totalColor  = $receiptTotalColor ?? '#1a1a1a';
        $borderStyle = $receiptBorderStyle ?? 'dashed';
        $borderColor = $receiptBorderColor ?? '#e5e5e5';
        $paperSize   = $receiptPaperSize ?? 'thermal80';
        $paperWidths = ['thermal80' => '420px', 'a5' => '559px', 'a4' => '794px'];
        $maxW        = $paperWidths[$paperSize] ?? '420px';
        $footerPx    = intval($receiptFooterFontSize ?? 14);

        // Theme-based overrides
        $bgBody      = '#f5f5f5';
        $bgReceipt   = '#fff';
        $textMain    = '#1a1a1a';
        $textSub     = '#888';
        $chipSuccessBg  = '#ecfdf5'; $chipSuccessText = '#065f46';
        $chipPaidBg     = '#eff6ff'; $chipPaidText    = '#1e40af';
        $chipMethodBg   = '#faf5ff'; $chipMethodText  = '#6b21a8';
        if ($theme === 'dark') {
            $bgBody = '#121212'; $bgReceipt = '#1e1e1e'; $textMain = '#f5f5f5'; $textSub = '#aaa';
            $chipSuccessBg  = '#1a3028'; $chipSuccessText = '#6ee7b7';
            $chipPaidBg     = '#1e2d40'; $chipPaidText    = '#93c5fd';
            $chipMethodBg   = '#2d1b40'; $chipMethodText  = '#d8b4fe';
        } elseif ($theme === 'blue') {
            $accentColor = $accentColor === '#1a1a1a' ? '#1e3a8a' : $accentColor;
            $totalColor  = $totalColor === '#1a1a1a' ? '#1e3a8a' : $totalColor;
        } elseif ($theme === 'bw') {
            $chipSuccessBg  = '#f5f5f5'; $chipSuccessText = '#333';
            $chipPaidBg     = '#f5f5f5'; $chipPaidText    = '#333';
            $chipMethodBg   = '#f5f5f5'; $chipMethodText  = '#333';
        }

        $borderCss = $borderStyle === 'none' ? 'none' : "2px {$borderStyle} {$borderColor}";
        $borderThinCss = $borderStyle === 'none' ? 'none' : "1px solid {$borderColor}";
    @endphp
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: {{ $fontStack }};
            background: {{ $bgBody }};
            color: {{ $textMain }};
            font-size: {{ $bodyPx }}px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Receipt Container ── */
        .receipt {
            max-width: {{ $maxW }};
            margin: 24px auto;
            background: {{ $bgReceipt }};
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,.08);
            overflow: hidden;
        }

        /* ── Header ── */
        .receipt-header {
            text-align: center;
            padding: 28px 24px 20px;
            border-bottom: {{ $borderCss }};
        }
        .receipt-logo {
            object-fit: contain;
            margin: 0 auto;
            display: block;
            border-radius: 8px;
        }
        .receipt-header .cafe-name {
            font-size: {{ $headerPx }}px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: {{ $accentColor }};
        }
        .receipt-header .cafe-tagline { font-size: 12px; color: {{ $textSub }}; margin-top: 2px; }
        .receipt-header .order-code   { font-size: 16px; font-weight: 700; margin-top: 14px; color: {{ $textMain }}; letter-spacing: 0.5px; }
        .receipt-header .order-date   { font-size: 12px; color: {{ $textSub }}; margin-top: 4px; }

        /* ── Info Grid ── */
        .receipt-info {
            padding: 16px 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            border-bottom: {{ $borderThinCss }};
        }
        .receipt-info .info-item  { font-size: {{ $bodyPx - 1 }}px; }
        .receipt-info .info-label { color: {{ $textSub }}; font-size: {{ $bodyPx - 2 }}px; }
        .receipt-info .info-value { font-weight: 600; color: {{ $textMain }}; }

        /* ── Status Chips ── */
        .receipt-status { padding: 14px 24px; display: flex; flex-wrap: wrap; gap: 8px; border-bottom: {{ $borderCss }}; }
        .status-chip { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .chip-success { background: {{ $chipSuccessBg }}; color: {{ $chipSuccessText }}; @if($theme === 'bw') border: 1px solid #ccc; @endif }
        .chip-paid    { background: {{ $chipPaidBg }};    color: {{ $chipPaidText }};    @if($theme === 'bw') border: 1px solid #ccc; @endif }
        .chip-method  { background: {{ $chipMethodBg }};  color: {{ $chipMethodText }};  @if($theme === 'bw') border: 1px solid #ccc; @endif }

        /* ── Items ── */
        .receipt-items { padding: 0 24px; }
        .receipt-items .items-title { font-size: 12px; font-weight: 700; color: {{ $textSub }}; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 0 10px; }
        .item-row { padding: 10px 0; border-bottom: {{ $borderThinCss }}; }
        .item-row:last-child { border-bottom: none; }
        .item-main { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .item-name    { font-size: {{ $bodyPx }}px; font-weight: 600; color: {{ $textMain }}; }
        .item-mods    { font-size: 11px; color: {{ $textSub }}; margin-top: 2px; }
        .item-note    { font-size: 11px; color: #b45309; margin-top: 2px; }
        .item-price-col { text-align: right; flex-shrink: 0; }
        .item-qty     { font-size: 11px; color: {{ $textSub }}; }
        .item-subtotal{ font-size: {{ $bodyPx }}px; font-weight: 600; color: {{ $textMain }}; }

        /* ── Total ── */
        .receipt-total { margin: 0 24px; padding: 16px 0; border-top: {{ $borderCss }}; display: flex; justify-content: space-between; align-items: center; }
        .total-label  { font-size: 14px; font-weight: 700; color: {{ $textMain }}; }
        .total-amount { font-size: 20px; font-weight: 800; color: {{ $totalColor }}; }

        /* ── Footer ── */
        .receipt-footer { text-align: center; padding: 20px 24px 28px; border-top: {{ $borderThinCss }}; }
        .receipt-footer .thankyou     { font-size: {{ $footerPx }}px; font-weight: 600; color: {{ $textMain }}; }
        .receipt-footer .footer-info  { font-size: 11px; color: {{ $textSub }}; margin-top: 6px; }
        .receipt-footer .footer-secondary { font-size: 11px; color: {{ $textSub }}; margin-top: 4px; }

        /* ── Buttons ── */
        .receipt-actions { max-width: {{ $maxW }}; margin: 0 auto 32px; padding: 0 16px; display: flex; gap: 10px; }
        .btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 12px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; transition: all .15s; }
        .btn-primary { background: {{ $accentColor }}; color: #fff; }
        .btn-outline  { background: {{ $bgReceipt }}; color: {{ $textSub }}; border: 1px solid {{ $borderColor }}; }
        .btn-outline:hover { opacity: .85; }

        /* ── Print ── */
        @media print {
            body { background: #fff; margin: 0; padding: 0; }
            .receipt { max-width: 100%; margin: 0; border-radius: 0; box-shadow: none; background: #fff !important; color: #000 !important; }
            .receipt-actions { display: none !important; }
            @page { margin: {{ $paperSize === 'thermal80' ? '3mm' : '8mm' }}; }
        }
        @if($paperSize === 'thermal80')
        @media print and (max-width: 80mm) {
            .receipt-header .cafe-name { font-size: {{ max(14, $headerPx - 4) }}px; }
            .receipt-info { grid-template-columns: 1fr; gap: 6px; }
            .total-amount { font-size: 16px; }
        }
        @endif
    </style>
</head>
<body>

<div class="receipt">
    <!-- Header -->
    <div class="receipt-header">
        @if(($receiptShowLogo ?? '0') === '1' && !empty($receiptLogoUrl))
            @php $logoSize = intval($receiptLogoSize ?? 64); $logoSpacing = intval($receiptLogoSpacing ?? 10); @endphp
            <img src="{{ $receiptLogoUrl }}" alt="Logo" class="receipt-logo"
                 style="width:{{ $logoSize }}px;height:{{ $logoSize }}px;margin-bottom:{{ $logoSpacing }}px;{{ $theme === 'bw' ? 'filter:grayscale(1);' : '' }}">
        @endif
        <div class="cafe-name">{{ $cafeName }}</div>
        @if(($receiptShowTagline ?? '1') === '1')
        <div class="cafe-tagline">{{ $cafeTagline }}</div>
        @endif
        <div class="order-code">{{ $order->order_code }}</div>
        <div class="order-date">{{ $order->created_at->format('d M Y, H:i') }}</div>
    </div>

    <!-- Info Grid -->
    @php
        $showCustomer   = ($receiptShowCustomer ?? '1') === '1';
        $showOrderType  = ($receiptShowOrderType ?? '1') === '1';
        $showTableNo    = ($receiptShowTableNo ?? '1') === '1';
        $infoItems = [];
        if ($showCustomer)  $infoItems[] = ['Customer', $order->customer_name];
        if ($showTableNo)   $infoItems[] = ['Meja', $order->table->table_no ?? '-'];
        if ($showOrderType) $infoItems[] = ['Tipe', $order->fulfillment_type === 'DINE_IN' ? 'Dine In' : 'Takeaway'];
        $infoItems[] = ['Tanggal', $order->created_at->format('d/m/Y')];
    @endphp
    @if($showCustomer || $showOrderType || $showTableNo)
    <div class="receipt-info">
        @foreach($infoItems as [$label, $value])
        <div class="info-item">
            <div class="info-label">{{ $label }}</div>
            <div class="info-value">{{ $value }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Items -->
    <div class="receipt-items">
        <div class="items-title">Rincian Pesanan</div>
        @foreach($order->items as $item)
        <div class="item-row">
            <div class="item-main">
                <div>
                    <div class="item-name">{{ $item->product_name }}</div>
                    @if($item->mods->isNotEmpty())
                        <div class="item-mods">{{ $item->mods_summary }}</div>
                    @endif
                    @if($item->note && ($receiptShowItemNotes ?? '1') === '1')
                        <div class="item-note">📝 {{ $item->note }}</div>
                    @endif
                </div>
                <div class="item-price-col">
                    <div class="item-qty">{{ $item->qty }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                    <div class="item-subtotal">Rp {{ number_format($item->line_total, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Payment Method -->
    @if(($receiptShowPayment ?? '1') === '1')
    <div style="margin:0 24px;padding:10px 0;border-top:{{ $borderThinCss }};display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:12px;color:{{ $textSub }};">Metode Pembayaran</span>
        <span style="font-size:{{ $bodyPx }}px;font-weight:600;color:{{ $textMain }};">💳 {{ $paymentMethod }}</span>
    </div>
    @endif

    <!-- Total -->
    <div class="receipt-total">
        <span class="total-label">Grand Total</span>
        <span class="total-amount">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
    </div>

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="thankyou">{{ $receiptFooterText ?? 'Terima kasih! 🙏' }}</div>
        @php
            $location = $receiptLocation ?? $cafeAddress ?? null;
            $wa = $receiptWhatsapp ?? $cafeWhatsapp ?? null;
        @endphp
        @if($location || $wa)
        <div class="footer-info">
            @if($location){{ $location }}@endif
            @if($location && $wa) &bull; @endif
            @if($wa)WA: {{ $wa }}@endif
        </div>
        @endif
        @if(!empty($receiptFooterSecondary))
        <div class="footer-secondary">{{ $receiptFooterSecondary }}</div>
        @endif
    </div>
</div>

<!-- Actions (hidden on print) -->
<div class="receipt-actions">
    <button class="btn btn-primary" onclick="window.print()">🖨️ Cetak / Save PDF</button>
    <a href="{{ $backUrl }}" class="btn btn-outline">← Kembali</a>
</div>

</body>
</html>
