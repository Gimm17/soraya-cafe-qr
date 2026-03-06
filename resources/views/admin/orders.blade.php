<x-admin-layout title="Orders">
    {{-- Page Header --}}
    <div class="admin-page-header">
        <h1>Orders Management</h1>
        <p>Kelola order, menu, dan laporan</p>
    </div>

    {{-- Filters --}}
    <div class="section-card p-4 mb-6">
        <form action="{{ route('admin.orders') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <select name="status" class="admin-select">
                <option value="">Semua Status</option>
                <option value="DITERIMA" {{ request('status') == 'DITERIMA' ? 'selected' : '' }}>Diterima</option>
                <option value="DIPROSES" {{ request('status') == 'DIPROSES' ? 'selected' : '' }}>Diproses</option>
                <option value="READY" {{ request('status') == 'READY' ? 'selected' : '' }}>Ready</option>
                <option value="SELESAI" {{ request('status') == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
            </select>

            <select name="payment" class="admin-select">
                <option value="">Semua Pembayaran</option>
                <option value="PAID" {{ request('payment') == 'PAID' ? 'selected' : '' }}>Sudah Bayar</option>
                <option value="UNPAID" {{ request('payment') == 'UNPAID' ? 'selected' : '' }}>Belum Bayar</option>
            </select>

            <select name="fulfillment" class="admin-select">
                <option value="">Semua Tipe</option>
                <option value="DINE_IN" {{ request('fulfillment') == 'DINE_IN' ? 'selected' : '' }}>Dine In</option>
                <option value="PICKUP" {{ request('fulfillment') == 'PICKUP' ? 'selected' : '' }}>Pickup</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1 justify-center tap-44">Filter</button>
                @if(request()->hasAny(['status', 'payment', 'fulfillment']))
                    <a href="{{ route('admin.orders') }}" class="btn-secondary flex-1 text-center tap-44 flex items-center justify-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Kanban Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
            $statusLabels = [
                'DITERIMA' => ['label' => 'Diterima'],
                'DIPROSES' => ['label' => 'Diproses'],
                'READY'    => ['label' => 'Ready'],
                'SELESAI'  => ['label' => 'Selesai'],
            ];
        @endphp

        @foreach($statusLabels as $status => $config)
        <section class="kanban-col">
            {{-- Blue header for every column --}}
            <div class="kanban-header">
                <span>{{ $config['label'] }}</span>
                <span class="kanban-count">{{ $orders->where('order_status', $status)->count() }}</span>
            </div>

            <div class="p-3 space-y-3">
                @foreach($orders->where('order_status', $status) as $order)
                <div class="ui-card-flat p-4 hover:shadow-soft transition-shadow cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-bold text-gray-900 text-sm">{{ $order->order_code }}</span>
                        <span class="badge {{ $order->payment_status === 'PAID' ? 'badge-green' : ($order->payment_status === 'EXPIRED' || $order->created_at->diffInMinutes(now()) >= 10 ? 'badge-gray' : 'badge-red') }}">
                            @if($order->payment_status === 'PAID') ✓ Paid
                            @elseif($order->payment_status === 'EXPIRED' || $order->created_at->diffInMinutes(now()) >= 10) Expired
                            @else Unpaid
                            @endif
                        </span>
                    </div>

                    <div class="mb-2">
                        <p class="text-sm text-gray-900 font-semibold">{{ $order->customer_name }}</p>
                        <p class="text-xs text-muted">
                            @if($order->fulfillment_type === 'DINE_IN')
                                🪑 Meja {{ $order->table->table_no ?? '-' }}
                            @else
                                📍 Pickup
                            @endif
                        </p>
                    </div>

                    <div class="flex justify-between items-center text-xs text-muted">
                        <span>{{ $order->created_at->diffForHumans() }}</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>

                    @if($order->payment_status !== 'PAID' && $order->created_at->diffInMinutes(now()) < 10)
                    <div class="mt-2 text-[11px] text-amber-700 font-bold bg-amber-50 border border-amber-200 p-2 rounded-xl text-center countdown-timer" data-expires="{{ $order->created_at->addMinutes(10)->toISOString() }}">
                        ⏳ <span class="timer-display">--:--</span> sebelum expired
                    </div>
                    @endif

                    {{-- Quick Status Update --}}
                    @if($status !== 'SELESAI')
                    <div class="mt-3 flex gap-2" onclick="event.stopPropagation()">
                        @php
                            $nextStatus = match($status) {
                                'DITERIMA' => null,
                                'DIPROSES' => 'READY',
                                'READY'    => 'SELESAI',
                                default    => null
                            };
                        @endphp

                        @if($nextStatus)
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                            <button type="submit" class="w-full tap-44 py-2 btn-primary text-sm justify-center">
                                → {{ $statusLabels[$nextStatus]['label'] }}
                            </button>
                        </form>
                        @endif

                        @if($order->payment_status !== 'PAID')
                            <form id="delete-order-{{ $order->id }}" action="{{ route('admin.orders.delete', $order) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" onclick="confirmDelete('delete-order-{{ $order->id }}', 'pesanan {{ $order->order_code }}')" class="tap-44 px-3 py-2 bg-white border border-line text-red-600 rounded-xl text-sm hover:bg-red-50 transition-colors" title="Hapus Pesanan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach

                @if($orders->where('order_status', $status)->isEmpty())
                <div class="text-center py-10 text-muted">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm font-medium">Tidak ada</p>
                </div>
                @endif
            </div>
        </section>
        @endforeach
    </div>

    <script>
        function updateTimers() {
            document.querySelectorAll('.countdown-timer').forEach(el => {
                const expires = new Date(el.dataset.expires).getTime();
                const now = new Date().getTime();
                const distance = expires - now;

                if (distance < 0) {
                    el.innerHTML = '⚠️ Expired';
                    el.classList.add('text-gray-600', 'bg-gray-50');
                    return;
                }

                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const display = el.querySelector('.timer-display');
                if (display) {
                    display.innerText =
                        (minutes < 10 ? '0' : '') + minutes + ':' +
                        (seconds < 10 ? '0' : '') + seconds;
                }
            });
        }
        setInterval(updateTimers, 1000);
        updateTimers();

        // ── Auto-refresh: poll every 15 seconds ──
        let lastOrderCount = {{ $orders->count() }};
        const REFRESH_INTERVAL = 15000;

        async function autoRefreshOrders() {
            if (document.hidden) return;

            try {
                const res = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;

                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newBoard = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-2');
                const oldBoard = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2');
                if (newBoard && oldBoard) {
                    oldBoard.innerHTML = newBoard.innerHTML;
                    updateTimers();
                }

                const newCards = doc.querySelectorAll('.ui-card-flat[onclick]');
                if (newCards.length > lastOrderCount) {
                    const diff = newCards.length - lastOrderCount;
                    if (Notification.permission === 'granted') {
                        new Notification('🔔 Pesanan Baru!', {
                            body: `${diff} pesanan baru masuk`,
                            icon: '/assets/brand/logo.png'
                        });
                    }
                    try {
                        @php
                            $customPath = public_path('custom_notification.mp3');
                            $soundSrc = file_exists($customPath)
                                ? asset('custom_notification.mp3')
                                : 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbsGckGFqIw+DfuHEoJUyAvN3nxII5MWCE0O/dq1s3OHaV3/DexnlHOm+L0/Hhz5NbR2yFyerhz5leUHGLz/Dlz5tmWXiOz/Dlz5tm';
                        @endphp
                        const audio = new Audio("{!! $soundSrc !!}");
                        audio.volume = 0.5;
                        audio.play().catch(e => console.log('Audio play failed:', e));
                    } catch(e) {}
                }
                lastOrderCount = newCards.length;

            } catch (e) {
                console.warn('Auto-refresh failed:', e);
            }
        }

        setInterval(autoRefreshOrders, REFRESH_INTERVAL);

        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    </script>
</x-admin-layout>
