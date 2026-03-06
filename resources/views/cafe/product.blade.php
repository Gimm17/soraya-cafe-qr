<x-cafe-layout :tableNo="$tableNo" title="{{ $product->name }} - Soraya Coffee">
    <x-slot:head>
    <style>
        .modifier-option input:checked + label {
            border-color: #1E3A8A;
            background-color: rgba(30,58,138,0.06);
        }
        .modifier-option input:checked + label .check-icon {
            display: flex;
            background: #1E3A8A;
            border-color: #1E3A8A;
        }
        .modifier-option input:checked + label .check-icon svg {
            color: #fff;
        }
        .modifier-label {
            border: 1.5px solid #E5E7EB;
            border-radius: 0.875rem;
            padding: 12px 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color 0.15s, background 0.15s;
            background: #fff;
        }
        .modifier-label:hover {
            border-color: #1E3A8A;
            background: #EFF6FF;
        }
        .qty-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 1.5px solid #E5E7EB;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 700; color: #1E3A8A;
            cursor: pointer; transition: background 0.15s;
        }
        .qty-btn:hover { background: #EFF6FF; }
        .bottom-bar {
            position: sticky;
            bottom: 72px;
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 -2px 16px rgba(30,58,138,0.10);
            padding: 14px 16px 16px;
            margin-top: 12px;
        }
        .btn-add-cart {
            width: 100%; padding: 14px 0;
            background: #1E3A8A; color: #fff;
            border-radius: 999px; font-weight: 700;
            font-size: 16px; letter-spacing: 0.01em;
            border: none; cursor: pointer;
            transition: background 0.18s;
        }
        .btn-add-cart:hover { background: #1E40AF; }
        .btn-add-cart:disabled {
            background: #E5E7EB; color: #9CA3AF; cursor: not-allowed;
        }
        .category-pill {
            display: inline-block;
            border: 1.5px solid #E5E7EB;
            border-radius: 999px;
            padding: 3px 14px;
            font-size: 13px;
            color: #374151;
            font-weight: 500;
        }
        .back-btn {
            display: inline-flex; align-items: center; gap: 6px;
            border: 1.5px solid #E5E7EB; border-radius: 999px;
            padding: 6px 16px 6px 10px;
            font-size: 14px; font-weight: 600; color: #374151;
            background: #fff; margin-bottom: 14px;
            transition: background 0.15s;
        }
        .back-btn:hover { background: #F3F4F6; }
    </style>
    </x-slot:head>

    <div class="max-w-lg mx-auto px-4">

        {{-- Back Button --}}
        <a href="{{ route('cafe.menu') }}" class="back-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>

        {{-- Hero Image with Name Overlay --}}
        <div class="relative rounded-2xl overflow-hidden mb-5 {{ $isCloseOrder ? 'grayscale' : '' }}" style="aspect-ratio:4/3;">
            @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center bg-[#F0F4FF]">
                <svg class="w-20 h-20 text-[#1E3A8A] opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            @endif

            {{-- Gradient overlay with name --}}
            <div class="absolute bottom-0 left-0 right-0 px-4 py-4" style="background:linear-gradient(to top, rgba(0,0,0,0.68) 0%, transparent 100%);">
                <p class="text-white font-extrabold text-2xl tracking-tight">{{ $product->name }}</p>
                @if($product->is_best_seller && !$isCloseOrder)
                <span class="inline-flex items-center gap-1 mt-1 text-amber-300 font-bold text-xs">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Best
                </span>
                @endif
            </div>
        </div>

        {{-- Close Order Alert --}}
        @if($isCloseOrder)
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm flex items-center gap-2">
            @if(!$cafeIsOpen)
                🔴 <strong>Cafe sedang tutup.</strong> Tidak bisa memesan saat ini.
            @else
                🚫 <strong>Menu ini sudah Close Order.</strong> Tidak bisa ditambahkan ke keranjang.
            @endif
        </div>
        @endif

        {{-- Price + Category --}}
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-xl font-extrabold text-gray-900">{{ $product->name }}</h1>
            <span class="text-[#1E3A8A] font-extrabold text-lg">{{ $product->price_rupiah }}</span>
        </div>
        @if($product->category)
        <span class="category-pill mb-3 inline-block">{{ $product->category->name }}</span>
        @endif
        <p class="text-sm text-gray-600 leading-relaxed mb-6">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>

        {{-- Add to Cart Form --}}
        <form action="{{ route('cafe.cart.add') }}" method="POST" id="addToCartForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Modifier Groups --}}
            @if($product->modGroups->isNotEmpty())
            <div class="space-y-5 mb-6">
                @foreach($product->modGroups as $modGroup)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-gray-900">{{ $modGroup->name }}</h3>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $modGroup->is_required ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $modGroup->is_required ? 'Wajib' : 'Opsional' }}
                        </span>
                    </div>

                    @if($modGroup->isSingleSelect())
                    {{-- Radio: 2-col grid for single select like Size --}}
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($modGroup->activeOptions as $option)
                        <div class="modifier-option">
                            <input type="radio"
                                   name="modifiers[{{ $modGroup->id }}]"
                                   value="{{ $option->id }}"
                                   id="mod_{{ $option->id }}"
                                   class="hidden"
                                   data-price="{{ $option->price_modifier }}"
                                   {{ $modGroup->is_required && $loop->first ? 'checked' : '' }}>
                            <label for="mod_{{ $option->id }}" class="modifier-label flex-col items-start gap-0">
                                <div class="flex items-center justify-between w-full">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $option->name }}</span>
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center check-icon hidden flex-shrink-0">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                                @if($option->price_modifier != 0)
                                <span class="text-xs font-bold {{ $option->price_modifier > 0 ? 'text-[#1E3A8A]' : 'text-green-700' }} mt-1">
                                    {{ $option->formatted_price_modifier }}
                                </span>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @else
                    {{-- Checkbox: Add-ons in 2-col grid --}}
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($modGroup->activeOptions as $option)
                        <div class="modifier-option">
                            <input type="checkbox"
                                   name="modifiers[]"
                                   value="{{ $option->id }}"
                                   id="mod_{{ $option->id }}"
                                   class="hidden"
                                   data-price="{{ $option->price_modifier }}">
                            <label for="mod_{{ $option->id }}" class="modifier-label flex-col items-start gap-0">
                                <div class="flex items-center justify-between w-full">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $option->name }}</span>
                                    <div class="w-5 h-5 rounded border-2 border-gray-300 flex items-center justify-center check-icon hidden flex-shrink-0" style="border-radius:4px">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                                @if($option->price_modifier != 0)
                                <span class="text-xs font-bold text-[#1E3A8A] mt-1">{{ $option->formatted_price_modifier }}</span>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Notes --}}
            <div class="mb-5">
                <label class="block font-bold text-gray-900 mb-2">Catatan</label>
                <textarea name="note" rows="2" placeholder="Contoh: tidak pakai es, extra gula..."
                          class="w-full px-4 py-3 bg-white rounded-2xl border border-gray-200 outline-none text-gray-800 resize-none text-sm focus:border-[#1E3A8A] transition-colors"></textarea>
            </div>

            {{-- Quantity + Add to Cart - sticky bottom bar --}}
            <div class="bottom-bar">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="updateQty(-1)" class="qty-btn">−</button>
                        <input type="number" name="qty" value="1" min="1" max="20" id="qtyInput"
                               class="w-10 text-center text-lg font-extrabold text-gray-900 bg-transparent border-none outline-none">
                        <button type="button" onclick="updateQty(1)" class="qty-btn">+</button>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 mb-0.5">Total Harga</p>
                        <span class="text-[#1E3A8A] font-extrabold text-lg" id="totalPrice">{{ $product->price_rupiah }}</span>
                    </div>
                </div>

                @if($isCloseOrder)
                <button type="button" disabled class="btn-add-cart" style="background:#E5E7EB;color:#9CA3AF;cursor:not-allowed">
                    🚫 Close Order
                </button>
                @elseif($product->is_sold_out)
                <button type="button" disabled class="btn-add-cart" style="background:#E5E7EB;color:#9CA3AF;cursor:not-allowed">
                    Stok Habis
                </button>
                @else
                <button type="submit" class="btn-add-cart">
                    Tambah ke Keranjang
                </button>
                @endif
            </div>
        </form>

        {{-- Product Reviews --}}
        @if(isset($reviews) && $reviews->isNotEmpty())
        <div class="mt-8 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-gray-900">Review Pelanggan</h2>
                @if($product->average_rating)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="font-bold text-sm text-gray-900">{{ $product->average_rating }}</span>
                    <span class="text-xs text-gray-400">({{ $reviews->count() }})</span>
                </div>
                @endif
            </div>
            <div class="space-y-3">
                @foreach($reviews as $review)
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-1 mb-1">
                        @for($i=1;$i<=5;$i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                        <span class="text-xs text-gray-400 ml-1">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    @if($review->comment)
                        <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="h-6"></div>
    </div>

    <x-slot:scripts>
    <script>
        const basePrice = {{ $product->base_price }};

        function updateQty(delta) {
            const input = document.getElementById('qtyInput');
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            if (val > 20) val = 20;
            input.value = val;
            calculateTotal();
        }

        function calculateTotal() {
            const qty = parseInt(document.getElementById('qtyInput').value) || 1;
            let modTotal = 0;
            document.querySelectorAll('.modifier-option input:checked').forEach(input => {
                modTotal += parseInt(input.dataset.price) || 0;
            });
            const total = (basePrice + modTotal) * qty;
            document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        document.querySelectorAll('.modifier-option input').forEach(input => {
            input.addEventListener('change', calculateTotal);
        });
        document.getElementById('qtyInput').addEventListener('input', calculateTotal);
        calculateTotal();
    </script>
    </x-slot:scripts>
</x-cafe-layout>
