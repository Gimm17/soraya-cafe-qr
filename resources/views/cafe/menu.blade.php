<x-cafe-layout :tableNo="$tableNo" title="Menu - Soraya Coffee">
    <x-slot:head>
    <style>
        .cafe-chip-active {
            background: #1E3A8A;
            color: #fff;
            border-color: #1E3A8A;
        }
        .cafe-chip {
            background: #fff;
            color: #374151;
            border: 1.5px solid #E5E7EB;
        }
        .cafe-chip:hover {
            background: #EFF6FF;
            border-color: #1E3A8A;
        }
        .product-card {
            background: #fff;
            border-radius: 1.1rem;
            overflow: hidden;
            box-shadow: 0 1px 6px 0 rgba(30,58,138,0.07);
            transition: box-shadow 0.18s;
        }
        .product-card:active {
            box-shadow: 0 2px 12px 0 rgba(30,58,138,0.14);
        }
        .badge-best {
            background: #1E3A8A;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 99px;
            padding: 2px 10px 2px 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.02em;
        }
        .cafe-search {
            background: #F3F6FF;
            border: 1.5px solid #E5E7EB;
            border-radius: 999px;
            padding: 11px 18px 11px 44px;
            font-size: 15px;
            width: 100%;
            outline: none;
            color: #374151;
            transition: border-color 0.18s;
        }
        .cafe-search:focus {
            border-color: #1E3A8A;
            background: #fff;
        }
        .price-blue {
            color: #1E3A8A;
            font-weight: 800;
        }
    </style>
    </x-slot:head>

    <div class="max-w-lg mx-auto px-4">

        @if(!$cafeIsOpen)
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl text-center">
            <span class="text-2xl">🔴</span>
            <p class="font-bold text-red-800 text-base mt-1">Cafe Sedang Tutup</p>
            <p class="text-sm text-red-600">Maaf, saat ini cafe belum buka. Silakan kembali lagi nanti.</p>
        </div>
        @endif

        {{-- Search Bar --}}
        <div class="mb-5 relative">
            <form action="{{ route('cafe.menu') }}" method="GET">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Cari menu..."
                       class="cafe-search">
                @if($search)
                <a href="{{ route('cafe.menu') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
                @endif
            </form>
        </div>

        {{-- Category Filter Pills --}}
        <div class="mb-5 overflow-x-auto pb-1 -mx-4 px-4">
            <div class="flex gap-2 min-w-max">
                <a href="{{ route('cafe.menu') }}"
                   class="px-5 py-2 rounded-full text-sm font-semibold transition-all {{ !$selectedCategory ? 'cafe-chip-active' : 'cafe-chip' }}">
                    Semua
                </a>
                @foreach($categories as $category)
                <a href="{{ route('cafe.menu', ['category' => $category->id]) }}"
                   class="px-5 py-2 rounded-full text-sm font-semibold transition-all whitespace-nowrap {{ $selectedCategory == $category->id ? 'cafe-chip-active' : 'cafe-chip' }}">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-2xl text-green-700 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Product Grid --}}
        @if($products->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-14 h-14 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 font-medium">Tidak ada menu ditemukan</p>
        </div>
        @else
        <div class="grid grid-cols-2 gap-3">
            @foreach($products as $product)
            @php $isClosed = in_array($product->category_id, $closedCategoryIds); @endphp
            <a href="{{ route('cafe.product.show', $product->slug) }}"
               class="product-card {{ $product->is_sold_out ? 'opacity-60' : '' }} {{ $isClosed ? 'grayscale opacity-50 pointer-events-none' : '' }}">

                {{-- Product Image --}}
                <div class="relative aspect-square bg-blue-50">
                    @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-[#F0F4FF]">
                        <svg class="w-12 h-12 text-[#1E3A8A] opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif

                    {{-- Badges --}}
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @if($isClosed)
                        <span class="bg-red-600 text-white px-2 py-0.5 rounded-full text-[11px] font-bold">🚫 Tutup</span>
                        @endif
                        @if($product->is_best_seller && !$isClosed)
                        <span class="badge-best">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Best
                        </span>
                        @endif
                        @if($product->is_sold_out && !$isClosed)
                        <span class="bg-red-100 text-red-700 border border-red-200 px-2 py-0.5 rounded-full text-[11px] font-bold">Habis</span>
                        @endif
                    </div>
                </div>

                {{-- Product Info --}}
                <div class="p-3 pb-4">
                    <h3 class="font-bold text-gray-900 text-[15px] leading-snug mb-1">{{ $product->name }}</h3>
                    <p class="price-blue text-[15px]">{{ $product->price_rupiah }}</p>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</x-cafe-layout>
