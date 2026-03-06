<x-admin-layout>
    <x-slot name="title">Kasir</x-slot>

    <x-slot name="head">
    <style>
        /* POS uses full-width layout */
        main { padding: 0 !important; }
        .pos-wrap { display: flex; height: calc(100vh - 73px); overflow: hidden; }
        .pos-catalog { flex: 1; overflow-y: auto; padding: 1.25rem; background: #F0F4FF; }
        .pos-order { width: 420px; min-width: 380px; background: #fff; border-left: 1px solid #e5e7eb; display: flex; flex-direction: column; }

        /* Product Cards */
        .pos-product { cursor: pointer; border: 1px solid #e5e7eb; border-radius: 12px; padding: 0.75rem; background: #fff; transition: all .15s; display: flex; gap: .75rem; align-items: center; }
        .pos-product:hover { border-color: #1E3A8A; box-shadow: 0 2px 8px rgba(30,58,138,.12); transform: translateY(-1px); }
        .pos-product img { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .pos-product .no-img { width: 56px; height: 56px; border-radius: 10px; background: #EEF2FF; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }

        /* Cart Items */
        .cart-item { padding: .75rem 1rem; border-bottom: 1px solid #f3f4f6; }
        .cart-item:last-child { border-bottom: none; }

        /* Category tabs */
        .cat-tab { padding: .5rem 1rem; border-radius: 9999px; font-size: .75rem; font-weight: 600; cursor: pointer; border: 1px solid #e5e7eb; background: #fff; color: #6b7280; transition: all .15s; white-space: nowrap; }
        .cat-tab.active { background: #1E3A8A; color: #fff; border-color: #1E3A8A; }
        .cat-tab:hover:not(.active) { border-color: #1E3A8A; color: #1E3A8A; }

        /* Responsive */
        @media (max-width: 768px) {
            .pos-wrap { flex-direction: column; height: auto; }
            .pos-catalog { min-height: 50vh; }
            .pos-order { width: 100%; min-width: unset; border-left: none; border-top: 1px solid #e5e7eb; }
        }
    </style>
    </x-slot>

    <div class="pos-wrap">

        {{-- LEFT: Product Catalog --}}
        <div class="pos-catalog">
            {{-- Search & Filters --}}
            <div class="mb-4 space-y-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="posSearch" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex gap-2 overflow-x-auto pb-1" id="categoryTabs">
                    <button class="cat-tab active" data-cat="all">Semua</button>
                    @foreach($categories as $cat)
                    <button class="cat-tab" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3" id="productGrid">
                @foreach($products as $product)
                <div class="pos-product" data-product="{{ json_encode([
                    'id' => $product->id,
                    'name' => $product->name,
                    'base_price' => $product->base_price,
                    'image_url' => $product->image_url ? asset($product->image_url) : null,
                    'category_id' => $product->category_id,
                    'modGroups' => $product->activeModGroups->map(fn($g) => [
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
                ]) }}" onclick="handleProductClick(this)">
                    @if($product->image_url)
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                    <div class="no-img">☕</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-900 truncate">{{ $product->name }}</p>
                        <p class="text-sm font-bold text-[#1E3A8A] mt-0.5">Rp {{ number_format($product->base_price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT: Order Panel --}}
        <div class="pos-order">
            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-[#1E3A8A]">🧾 Kasir</h2>
                        <p class="text-xs text-gray-400" id="orderCodePreview">Pesanan baru</p>
                    </div>
                    <button onclick="clearCart()" class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition-colors">Hapus Semua</button>
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="px-5 py-3 border-b border-gray-100 space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Nama Pelanggan</label>
                        <input type="text" id="posCustomerName" placeholder="Nama..." class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">No. Meja</label>
                        <select id="posTableId" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:border-blue-400 bg-white">
                            <option value="">— Tanpa Meja —</option>
                            @foreach($tables as $t)
                            <option value="{{ $t->id }}">Meja {{ $t->table_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto" id="cartItemsWrapper">
                <div id="cartItems" class="divide-y divide-gray-50">
                    {{-- Items injected by JS --}}
                </div>
                <div id="cartEmpty" class="flex flex-col items-center justify-center py-16 text-gray-300">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    <p class="text-sm font-medium">Keranjang kosong</p>
                    <p class="text-xs mt-1">Klik produk untuk menambahkan</p>
                </div>
            </div>

            {{-- Totals + Payment --}}
            <div class="border-t border-gray-200 bg-gray-50">
                <div class="px-5 py-3 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold" id="posSubtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-[#1E3A8A]">
                        <span>Total</span>
                        <span id="posGrandTotal">Rp 0</span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="px-5 py-3 border-t border-gray-200 space-y-3">
                    <p class="text-xs font-semibold text-gray-600">Metode Pembayaran</p>
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="paymentMethod" value="CASH" class="hidden peer" checked>
                            <div class="text-center py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-600 peer-checked:border-[#1E3A8A] peer-checked:bg-blue-50 peer-checked:text-[#1E3A8A] transition-all">
                                💵 Tunai
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="paymentMethod" value="QRIS" class="hidden peer">
                            <div class="text-center py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-600 peer-checked:border-[#1E3A8A] peer-checked:bg-blue-50 peer-checked:text-[#1E3A8A] transition-all">
                                📱 QRIS
                            </div>
                        </label>
                    </div>

                    {{-- Cash Fields (shown when CASH selected) --}}
                    @if($posCashChangeEnabled === '1')
                    <div id="cashFields" class="space-y-2">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Uang Diterima</label>
                            <input type="number" id="posCashReceived" placeholder="0" min="0" step="1000" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:border-blue-400" oninput="calcChange()">
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Kembalian</span>
                            <span class="font-bold text-green-600" id="posCashChange">Rp 0</span>
                        </div>
                    </div>
                    @endif

                    {{-- QRIS display (shown when QRIS selected) --}}
                    <div id="qrisFields" class="hidden text-center py-4 space-y-3">
                        @if($posQrisImage)
                        <img src="{{ asset($posQrisImage) }}" alt="QRIS" class="mx-auto w-48 h-48 object-contain rounded-xl border border-gray-200 p-2 bg-white">
                        @else
                        <div class="mx-auto w-48 h-48 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <p class="text-3xl mb-2">📱</p>
                                <p class="text-xs">QRIS belum diatur</p>
                                <p class="text-xs text-gray-400">Atur di Settings</p>
                            </div>
                        </div>
                        @endif
                        <p class="text-xs text-gray-500">Scan QRIS untuk membayar</p>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="px-5 py-3 border-t border-gray-200">
                    <button type="button" onclick="processOrder()" id="posSubmitBtn" class="w-full py-3.5 rounded-xl bg-[#1E3A8A] text-white font-bold text-sm hover:bg-[#1a3278] transition-all shadow-lg shadow-blue-900/20 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Proses Pesanan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modifier Selection Modal --}}
    <div id="modModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm" onclick="if(event.target===this)closeModModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900" id="modModalTitle">Pilih Varian</h3>
                    <p class="text-xs text-gray-400" id="modModalProduct"></p>
                </div>
                <button onclick="closeModModal()" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">✕</button>
            </div>
            <div class="px-5 py-4 space-y-4 max-h-[60vh] overflow-y-auto" id="modModalBody">
                {{-- modifier groups injected by JS --}}
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex gap-3">
                <button onclick="closeModModal()" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">Batal</button>
                <button onclick="confirmAddWithMods()" class="flex-1 py-2.5 rounded-xl bg-[#1E3A8A] text-white text-sm font-bold hover:bg-[#1a3278]">Tambahkan</button>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div id="successModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Pesanan Berhasil!</h3>
            <p class="text-sm text-gray-500 mb-1" id="successOrderCode"></p>
            <p class="text-2xl font-bold text-[#1E3A8A] mb-6" id="successTotal"></p>
            <div class="flex gap-3">
                <button onclick="printReceipt()" class="flex-1 py-3 rounded-xl border-2 border-[#1E3A8A] text-[#1E3A8A] font-bold text-sm hover:bg-blue-50 transition-colors">🖨️ Cetak Struk</button>
                <button onclick="newOrder()" class="flex-1 py-3 rounded-xl bg-[#1E3A8A] text-white font-bold text-sm hover:bg-[#1a3278] transition-colors">➕ Pesanan Baru</button>
            </div>
        </div>
    </div>

    {{-- QRIS Waiting Modal --}}
    <div id="qrisWaitModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center animate-pulse">
                <span class="text-3xl">📱</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Menunggu Pembayaran QRIS</h3>
            <p class="text-sm text-gray-500 mb-2" id="qrisWaitCode"></p>
            <p class="text-2xl font-bold text-[#1E3A8A] mb-6" id="qrisWaitTotal"></p>
            <button onclick="confirmQrisManual()" class="w-full py-3 rounded-xl bg-green-600 text-white font-bold text-sm hover:bg-green-700 transition-colors mb-3">
                ✅ Sudah Dibayar
            </button>
            <button onclick="cancelQrisWait()" class="w-full py-2.5 rounded-xl border border-gray-200 text-gray-500 text-sm font-semibold hover:bg-gray-50 transition-colors">
                Batalkan
            </button>
        </div>
    </div>

    <x-slot name="scripts">
    <script>
        // ===== STATE =====
        let cart = [];
        let pendingProduct = null;
        let lastOrderResult = null;
        let qrisPollInterval = null;
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        // ===== PRODUCT CLICK =====
        function handleProductClick(el) {
            const product = JSON.parse(el.dataset.product);
            if (product.modGroups && product.modGroups.length > 0) {
                openModModal(product);
            } else {
                addToCart(product, []);
            }
        }

        // ===== MODIFIER MODAL =====
        function openModModal(product) {
            pendingProduct = product;
            document.getElementById('modModalProduct').textContent = product.name + ' — Rp ' + fmt(product.base_price);
            const body = document.getElementById('modModalBody');
            body.innerHTML = '';
            product.modGroups.forEach(group => {
                const isMulti = group.max_select > 1;
                let html = `<div class="space-y-2">
                    <p class="text-sm font-bold text-gray-800">${group.name} ${group.is_required ? '<span class="text-red-500 text-xs">*wajib</span>' : '<span class="text-gray-400 text-xs">(opsional)</span>'}</p>`;
                group.options.forEach(opt => {
                    const inputType = isMulti ? 'checkbox' : 'radio';
                    const priceLabel = opt.price_modifier > 0 ? `+Rp ${fmt(opt.price_modifier)}` : (opt.price_modifier < 0 ? `-Rp ${fmt(Math.abs(opt.price_modifier))}` : '');
                    html += `<label class="flex items-center gap-3 p-2.5 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
                        <input type="${inputType}" name="mod_group_${group.id}" value="${opt.id}" data-group-name="${group.name}" data-option-name="${opt.name}" data-price="${opt.price_modifier}" class="accent-[#1E3A8A]">
                        <span class="flex-1 text-sm">${opt.name}</span>
                        ${priceLabel ? `<span class="text-xs font-semibold text-gray-500">${priceLabel}</span>` : ''}
                    </label>`;
                });
                html += '</div>';
                body.insertAdjacentHTML('beforeend', html);
            });
            document.getElementById('modModal').classList.remove('hidden');
            document.getElementById('modModal').style.display = 'flex';
        }

        function closeModModal() {
            document.getElementById('modModal').classList.add('hidden');
            document.getElementById('modModal').style.display = '';
            pendingProduct = null;
        }

        function confirmAddWithMods() {
            if (!pendingProduct) return;
            const checkedInputs = document.querySelectorAll('#modModalBody input:checked');
            const mods = Array.from(checkedInputs).map(i => ({
                id: parseInt(i.value),
                group_name: i.dataset.groupName,
                option_name: i.dataset.optionName,
                price_modifier: parseInt(i.dataset.price),
            }));
            addToCart(pendingProduct, mods);
            closeModModal();
        }

        // ===== CART LOGIC =====
        function addToCart(product, mods) {
            const modKey = mods.map(m => m.id).sort().join('-');
            const cartKey = `${product.id}_${modKey}`;
            const existing = cart.find(i => i.cartKey === cartKey);
            const modTotal = mods.reduce((s, m) => s + m.price_modifier, 0);
            const unitPrice = product.base_price + modTotal;

            if (existing) {
                existing.qty++;
            } else {
                cart.push({
                    cartKey,
                    product_id: product.id,
                    name: product.name,
                    unit_price: unitPrice,
                    base_price: product.base_price,
                    qty: 1,
                    note: '',
                    modifiers: mods,
                    modSummary: mods.map(m => m.option_name).join(', '),
                });
            }
            renderCart();
        }

        function updateQty(cartKey, delta) {
            const item = cart.find(i => i.cartKey === cartKey);
            if (!item) return;
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.cartKey !== cartKey);
            }
            renderCart();
        }

        function updateNote(cartKey, note) {
            const item = cart.find(i => i.cartKey === cartKey);
            if (item) item.note = note;
        }

        function removeFromCart(cartKey) {
            cart = cart.filter(i => i.cartKey !== cartKey);
            renderCart();
        }

        function clearCart() {
            if (cart.length === 0) return;
            confirmAction({ title: 'Kosongkan Keranjang?', text: 'Semua item akan dihapus.', icon: 'warning', confirmText: 'Ya, hapus' }).then(r => {
                if (r.isConfirmed) { cart = []; renderCart(); }
            });
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const empty = document.getElementById('cartEmpty');
            const subtotal = cart.reduce((s, i) => s + (i.unit_price * i.qty), 0);

            if (cart.length === 0) {
                container.innerHTML = '';
                empty.style.display = 'flex';
            } else {
                empty.style.display = 'none';
                container.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">${item.name}</p>
                                ${item.modSummary ? `<p class="text-xs text-gray-400 truncate">${item.modSummary}</p>` : ''}
                                <p class="text-xs font-semibold text-[#1E3A8A] mt-0.5">Rp ${fmt(item.unit_price)}</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button onclick="updateQty('${item.cartKey}', -1)" class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 text-sm font-bold">−</button>
                                <span class="w-7 text-center text-sm font-bold">${item.qty}</span>
                                <button onclick="updateQty('${item.cartKey}', 1)" class="w-7 h-7 rounded-lg bg-[#1E3A8A] flex items-center justify-center text-white text-sm font-bold hover:bg-[#1a3278]">+</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-1.5">
                            <input type="text" placeholder="Catatan..." value="${item.note}" onchange="updateNote('${item.cartKey}', this.value)" class="text-xs px-2 py-1 rounded border border-gray-100 w-32 focus:outline-none focus:border-blue-300">
                            <span class="text-sm font-bold text-gray-700">Rp ${fmt(item.unit_price * item.qty)}</span>
                        </div>
                    </div>
                `).join('');
            }

            document.getElementById('posSubtotal').textContent = 'Rp ' + fmt(subtotal);
            document.getElementById('posGrandTotal').textContent = 'Rp ' + fmt(subtotal);
            document.getElementById('posSubmitBtn').disabled = cart.length === 0;
            calcChange();
        }

        // ===== PAYMENT =====
        function getPaymentMethod() {
            return document.querySelector('input[name="paymentMethod"]:checked')?.value || 'CASH';
        }

        document.querySelectorAll('input[name="paymentMethod"]').forEach(r => {
            r.addEventListener('change', () => {
                const isCash = r.value === 'CASH';
                const cashFields = document.getElementById('cashFields');
                const qrisFields = document.getElementById('qrisFields');
                if (cashFields) cashFields.style.display = isCash ? 'block' : 'none';
                if (qrisFields) qrisFields.style.display = isCash ? 'none' : 'block';
            });
        });

        function calcChange() {
            const cashInput = document.getElementById('posCashReceived');
            if (!cashInput) return;
            const received = parseInt(cashInput.value) || 0;
            const total = cart.reduce((s, i) => s + (i.unit_price * i.qty), 0);
            const change = Math.max(0, received - total);
            document.getElementById('posCashChange').textContent = 'Rp ' + fmt(change);
            document.getElementById('posCashChange').className = (received >= total && received > 0) ? 'font-bold text-green-600' : 'font-bold text-red-500';
        }

        // ===== SEARCH & FILTER =====
        document.getElementById('posSearch').addEventListener('input', filterProducts);
        document.querySelectorAll('.cat-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                filterProducts();
            });
        });

        function filterProducts() {
            const query = document.getElementById('posSearch').value.toLowerCase();
            const catId = document.querySelector('.cat-tab.active').dataset.cat;
            document.querySelectorAll('.pos-product').forEach(el => {
                const product = JSON.parse(el.dataset.product);
                const matchName = product.name.toLowerCase().includes(query);
                const matchCat = catId === 'all' || product.category_id == catId;
                el.style.display = (matchName && matchCat) ? '' : 'none';
            });
        }

        // ===== PROCESS ORDER =====
        async function processOrder() {
            const name = document.getElementById('posCustomerName').value.trim();
            if (!name) {
                Swal.fire({ icon: 'warning', title: 'Nama pelanggan wajib diisi', confirmButtonColor: '#1E3A8A' });
                return;
            }
            if (cart.length === 0) return;

            const paymentMethod = getPaymentMethod();
            const cashReceived = parseInt(document.getElementById('posCashReceived')?.value) || null;
            const subtotal = cart.reduce((s, i) => s + (i.unit_price * i.qty), 0);

            // Validate cash
            if (paymentMethod === 'CASH' && cashReceived && cashReceived < subtotal) {
                Swal.fire({ icon: 'warning', title: 'Uang tidak cukup', text: 'Uang diterima lebih kecil dari total.', confirmButtonColor: '#1E3A8A' });
                return;
            }

            const result = await confirmAction({
                title: 'Proses Pesanan?',
                text: `Total: Rp ${fmt(subtotal)} — Bayar: ${paymentMethod === 'CASH' ? 'Tunai' : 'QRIS'}`,
                icon: 'question',
                confirmText: 'Ya, proses!'
            });
            if (!result.isConfirmed) return;

            document.getElementById('posSubmitBtn').disabled = true;
            document.getElementById('posSubmitBtn').textContent = 'Memproses...';

            try {
                const resp = await fetch('{{ route("admin.pos.order") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({
                        customer_name: name,
                        table_id: document.getElementById('posTableId').value || null,
                        fulfillment_type: 'DINE_IN',
                        payment_method: paymentMethod,
                        cash_received: cashReceived,
                        items: cart.map(i => ({
                            product_id: i.product_id,
                            name: i.name,
                            unit_price: i.unit_price,
                            qty: i.qty,
                            note: i.note || null,
                            modifiers: i.modifiers,
                        })),
                    }),
                });

                const data = await resp.json();
                if (!resp.ok) throw new Error(data.message || 'Server error');

                lastOrderResult = data.order;

                if (paymentMethod === 'QRIS') {
                    showQrisWait(data.order);
                } else {
                    showSuccess(data.order);
                    speakNominal(data.order.grand_total, 'Tunai');
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.message, confirmButtonColor: '#1E3A8A' });
            } finally {
                document.getElementById('posSubmitBtn').disabled = false;
                document.getElementById('posSubmitBtn').textContent = 'Proses Pesanan';
            }
        }

        // ===== QRIS WAIT =====
        function showQrisWait(order) {
            document.getElementById('qrisWaitCode').textContent = order.order_code;
            document.getElementById('qrisWaitTotal').textContent = 'Rp ' + fmt(order.grand_total);
            document.getElementById('qrisWaitModal').classList.remove('hidden');
            document.getElementById('qrisWaitModal').style.display = 'flex';

            // Poll for auto-confirm (Midtrans webhook)
            qrisPollInterval = setInterval(async () => {
                try {
                    const r = await fetch(`/admin/pos/check-payment/${order.id}`);
                    const d = await r.json();
                    if (d.payment_status === 'PAID') {
                        clearInterval(qrisPollInterval);
                        document.getElementById('qrisWaitModal').style.display = '';
                        document.getElementById('qrisWaitModal').classList.add('hidden');
                        showSuccess(order);
                        speakNominal(order.grand_total, 'QRIS');
                    }
                } catch(e) {}
            }, 3000);
        }

        async function confirmQrisManual() {
            if (!lastOrderResult) return;
            try {
                const resp = await fetch(`/admin/pos/confirm-qris/${lastOrderResult.id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                });
                const data = await resp.json();
                if (data.success) {
                    clearInterval(qrisPollInterval);
                    document.getElementById('qrisWaitModal').style.display = '';
                    document.getElementById('qrisWaitModal').classList.add('hidden');
                    showSuccess(lastOrderResult);
                    speakNominal(lastOrderResult.grand_total, 'QRIS');
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#1E3A8A' });
            }
        }

        function cancelQrisWait() {
            clearInterval(qrisPollInterval);
            document.getElementById('qrisWaitModal').style.display = '';
            document.getElementById('qrisWaitModal').classList.add('hidden');
        }

        // ===== SUCCESS =====
        function showSuccess(order) {
            document.getElementById('successOrderCode').textContent = order.order_code;
            document.getElementById('successTotal').textContent = 'Rp ' + fmt(order.grand_total);
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').style.display = 'flex';
        }

        function printReceipt() {
            if (!lastOrderResult) return;
            const win = window.open(lastOrderResult.receipt_url, '_blank', 'width=400,height=600');
            if (win) win.focus();
        }

        function newOrder() {
            document.getElementById('successModal').style.display = '';
            document.getElementById('successModal').classList.add('hidden');
            cart = [];
            lastOrderResult = null;
            document.getElementById('posCustomerName').value = '';
            document.getElementById('posTableId').value = '';
            if (document.getElementById('posCashReceived')) document.getElementById('posCashReceived').value = '';
            // Reset to CASH
            const cashRadio = document.querySelector('input[name="paymentMethod"][value="CASH"]');
            if (cashRadio) { cashRadio.checked = true; cashRadio.dispatchEvent(new Event('change')); }
            renderCart();
        }

        // ===== AUDIO NOTIFICATION =====
        const POS_AUDIO_ENABLED = {{ $posAudioEnabled === '1' ? 'true' : 'false' }};

        function speakNominal(amount, method) {
            if (!POS_AUDIO_ENABLED) return;
            if (!('speechSynthesis' in window)) return;
            const words = numberToIndonesianWords(amount);
            const text = `Pembayaran ${method} diterima. ${words} rupiah.`;
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.95;
            utterance.pitch = 1;
            speechSynthesis.speak(utterance);
        }

        function numberToIndonesianWords(n) {
            if (n === 0) return 'nol';
            const satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
            const belasan = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
                            'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];

            function convert(num) {
                if (num === 0) return '';
                if (num < 10) return satuan[num];
                if (num < 20) return belasan[num - 10];
                if (num < 100) {
                    const tens = Math.floor(num / 10);
                    const ones = num % 10;
                    return satuan[tens] + ' puluh' + (ones ? ' ' + satuan[ones] : '');
                }
                if (num < 200) return 'seratus' + (num % 100 ? ' ' + convert(num % 100) : '');
                if (num < 1000) {
                    const hundreds = Math.floor(num / 100);
                    return satuan[hundreds] + ' ratus' + (num % 100 ? ' ' + convert(num % 100) : '');
                }
                if (num < 2000) return 'seribu' + (num % 1000 ? ' ' + convert(num % 1000) : '');
                if (num < 1000000) {
                    const thousands = Math.floor(num / 1000);
                    return convert(thousands) + ' ribu' + (num % 1000 ? ' ' + convert(num % 1000) : '');
                }
                if (num < 1000000000) {
                    const millions = Math.floor(num / 1000000);
                    return convert(millions) + ' juta' + (num % 1000000 ? ' ' + convert(num % 1000000) : '');
                }
                return num.toString();
            }
            return convert(n);
        }

        // ===== HELPERS =====
        function fmt(n) {
            return new Intl.NumberFormat('id-ID').format(n);
        }

        // Init
        renderCart();
    </script>
    </x-slot>
</x-admin-layout>
