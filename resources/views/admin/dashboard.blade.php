<x-admin-layout title="Dashboard">
    {{-- Page Header --}}
    <div class="admin-page-header">
        <h1>Dashboard</h1>
        <p>Kelola order, menu, dan laporan</p>
    </div>

    {{-- Cafe Open/Close Toggle --}}
    <div class="cafe-status-bar border-l-4 {{ $cafeIsOpen ? 'border-green-500' : 'border-red-500' }}">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl {{ $cafeIsOpen ? 'bg-green-50' : 'bg-red-50' }} flex items-center justify-center">
                    <span class="text-2xl">{{ $cafeIsOpen ? '🟢' : '🔴' }}</span>
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-lg">Cafe {{ $cafeIsOpen ? 'BUKA' : 'TUTUP' }}</p>
                    <p class="text-xs text-muted">{{ $cafeIsOpen ? 'Pelanggan bisa memesan saat ini' : 'Semua pesanan diblokir' }}</p>
                </div>
        </div>
        <form method="POST" action="{{ route('admin.settings.toggle-cafe') }}" id="toggle-cafe-form-dash">
            @csrf
            <button type="button"
                class="tap-44 px-6 py-2.5 rounded-xl font-bold text-sm transition-colors {{ $cafeIsOpen ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}"
                onclick="confirmToggleCafe('toggle-cafe-form-dash', {{ $cafeIsOpen ? 'true' : 'false' }})">
                {{ $cafeIsOpen ? '🔒 Tutup Cafe' : '🔓 Buka Cafe' }}
            </button>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div class="kpi-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="kpi-badge">Hari ini</span>
            </div>
            <p class="kpi-label">Total Penjualan</p>
            <p class="kpi-value text-base sm:text-2xl">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</p>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div class="kpi-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="kpi-badge">Hari ini</span>
            </div>
            <p class="kpi-label">Total Pesanan</p>
            <p class="kpi-value">{{ $todayOrders ?? 0 }}</p>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div class="kpi-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="kpi-badge">Aktif</span>
            </div>
            <p class="kpi-label">Diproses</p>
            <p class="kpi-value">{{ $pendingOrders ?? 0 }}</p>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div class="kpi-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="kpi-badge">Total</span>
            </div>
            <p class="kpi-label">Menu Aktif</p>
            <p class="kpi-value">{{ $totalProducts ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Recent Orders --}}
        <div class="section-card">
            <div class="section-card-header">
                <div>
                    <h3>Pesanan terbaru</h3>
                    <p>Pantau order yang masuk.</p>
                </div>
                <a href="{{ route('admin.orders') }}" class="text-sm font-semibold text-[#1E3A8A] hover:underline">Lihat semua →</a>
            </div>
            <div class="divide-y" style="border-color: var(--line)">
                @forelse($recentOrders ?? [] as $order)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-3 hover:bg-blue-50/30 transition-colors">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 text-sm truncate">{{ $order->order_code }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ $order->customer_name }} • Meja {{ $order->table->table_no ?? '-' }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-gray-900 text-sm">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                            <span class="badge mt-1
                                {{ $order->order_status === 'DITERIMA' ? 'badge-blue' : '' }}
                                {{ $order->order_status === 'DIPROSES' ? 'badge-amber' : '' }}
                                {{ $order->order_status === 'READY' ? 'badge-green' : '' }}
                                {{ $order->order_status === 'SELESAI' ? 'badge-gray' : '' }}">
                                {{ $order->order_status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-muted text-sm">Belum ada pesanan hari ini</div>
                @endforelse
            </div>
        </div>

        {{-- Top Products --}}
        <div class="section-card">
            <div class="section-card-header">
                <div>
                    <h3>Produk terlaris</h3>
                    <p>Berdasarkan jumlah terjual.</p>
                </div>
            </div>
            <div class="divide-y" style="border-color: var(--line)">
                @forelse($topProducts ?? [] as $product)
                    <div class="px-5 py-3 flex items-center gap-4 hover:bg-blue-50/30 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $product->name }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ $product->order_count ?? 0 }} terjual</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-[#1E3A8A] text-sm">{{ $product->price_rupiah }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-muted text-sm">Belum ada data penjualan</div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
