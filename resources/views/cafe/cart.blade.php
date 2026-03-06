<x-cafe-layout :tableNo="$tableNo" title="Keranjang - Soraya Coffee">
    <x-slot:head>
    <style>
        .cart-item-card {
            background: #fff;
            border-radius: 1.1rem;
            border: 1.5px solid #F3F4F6;
            padding: 14px;
            box-shadow: 0 1px 6px rgba(30,58,138,0.06);
        }
        .cart-qty-btn {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: 1.5px solid #E5E7EB;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px; color: #1E3A8A; font-weight: 700;
            cursor: pointer; transition: background 0.15s;
            flex-shrink: 0;
        }
        .cart-qty-btn:hover { background: #EFF6FF; }
        .checkout-btn {
            width: 100%;
            padding: 15px 0;
            background: #1E3A8A;
            color: #fff;
            border-radius: 999px;
            font-weight: 700;
            font-size: 16px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: background 0.18s;
            border: none; cursor: pointer;
            text-decoration: none;
        }
        .checkout-btn:hover { background: #1E40AF; }
        .summary-card {
            background: #fff;
            border-radius: 1.1rem;
            border: 1.5px solid #F3F4F6;
            padding: 16px;
            box-shadow: 0 1px 6px rgba(30,58,138,0.06);
        }
    </style>
    </x-slot:head>

    <div class="max-w-lg mx-auto px-4">

        {{-- Header --}}
        <div class="flex items-end justify-between mb-5">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">Keranjang</h1>
                <p class="text-sm text-gray-500 mt-0.5">Cek pesananmu sebelum checkout.</p>
            </div>
            <a href="{{ route('cafe.menu') }}" class="text-sm font-bold text-[#1E3A8A] hover:underline">Tambah item</a>
        </div>

        @if(empty($items))
            {{-- Empty Cart --}}
            <div class="py-16 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-[#EFF6FF] rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#1E3A8A] opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Keranjang kosong</h2>
                <p class="text-sm text-gray-400 mt-1 mb-6">Belum ada item. Yuk pilih menu favoritmu.</p>
                <a href="{{ route('cafe.menu') }}" class="checkout-btn" style="display:inline-flex;width:auto;padding:12px 28px;">
                    Lihat Menu
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        @else
            {{-- Cart Items --}}
            <div class="space-y-3 mb-5">
                @foreach($items as $item)
                <div class="cart-item-card">
                    <div class="flex gap-3">
                        {{-- Thumbnail --}}
                        <div class="w-[72px] h-[72px] rounded-xl overflow-hidden flex-shrink-0 bg-[#F0F4FF]">
                            @if($item['image_url'] ?? false)
                                <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-[#1E3A8A] opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 text-[15px] truncate">{{ $item['name'] }}</h3>
                                    @if(!empty($item['modifiers_summary']))
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $item['modifiers_summary'] }}</p>
                                    @endif
                                    @if(!empty($item['note']))
                                        <p class="text-xs text-amber-700 mt-0.5 italic">📝 {{ $item['note'] }}</p>
                                    @endif
                                </div>

                                {{-- Delete --}}
                                <form id="remove-item-{{ $loop->index }}" action="{{ route('cafe.cart.remove') }}" method="POST" class="hidden">
                                    @csrf
                                    <input type="hidden" name="cart_key" value="{{ $item['cart_key'] }}">
                                </form>
                                <button type="button"
                                    onclick="confirmRemoveItem('remove-item-{{ $loop->index }}', '{{ $item['name'] }}')"
                                    class="p-1.5 rounded-lg hover:bg-red-50 transition-colors flex-shrink-0"
                                    aria-label="Hapus">
                                    <svg class="w-5 h-5 text-red-400 hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Price + Qty --}}
                            <div class="mt-3 flex items-center justify-between">
                                <p class="font-bold text-[#1E3A8A] text-sm">{{ $item['formatted_unit_price'] }}</p>

                                <div class="flex items-center gap-2">
                                    <form action="{{ route('cafe.cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="cart_key" value="{{ $item['cart_key'] }}">
                                        <input type="hidden" name="qty" value="{{ $item['qty'] - 1 }}">
                                        <button type="submit" class="cart-qty-btn" aria-label="Kurangi">−</button>
                                    </form>
                                    <span class="w-7 text-center font-bold text-gray-900 text-sm">{{ $item['qty'] }}</span>
                                    <form action="{{ route('cafe.cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="cart_key" value="{{ $item['cart_key'] }}">
                                        <input type="hidden" name="qty" value="{{ $item['qty'] + 1 }}">
                                        <button type="submit" class="cart-qty-btn" aria-label="Tambah">+</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Subtotal --}}
                    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-sm text-gray-400">Subtotal item</span>
                        <span class="font-bold text-gray-900">{{ $item['formatted_line_total'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="summary-card mb-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-gray-900">Ringkasan</h3>
                    <span class="text-xs font-bold bg-[#EFF6FF] text-[#1E3A8A] px-3 py-1 rounded-full">{{ $totals['count'] }} item</span>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-900 font-medium">Rp {{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @if($totals['tax'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pajak</span>
                        <span class="text-gray-900 font-medium">Rp {{ number_format($totals['tax'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($totals['service'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Biaya Layanan</span>
                        <span class="text-gray-900 font-medium">Rp {{ number_format($totals['service'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="text-2xl font-extrabold text-[#1E3A8A]">Rp {{ number_format($totals['grand_total'], 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Kirim Pesanan / Checkout --}}
            <a href="{{ route('cafe.checkout') }}" class="checkout-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
                Kirim Pesanan
            </a>

            <div class="h-6"></div>
        @endif
    </div>
</x-cafe-layout>
