<x-admin-layout title="Settings">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan</h1>
        <p class="text-gray-600">Sesuaikan preferensi aplikasi</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Cafe Open/Close Toggle --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:col-span-2">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                🏪 Status Cafe
            </h2>

            <div class="flex items-center justify-between gap-4 {{ $cafeIsOpen ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">{{ $cafeIsOpen ? '🟢' : '🔴' }}</span>
                    <div>
                        <p class="font-bold text-lg {{ $cafeIsOpen ? 'text-green-800' : 'text-red-800' }}">
                            Cafe {{ $cafeIsOpen ? 'BUKA' : 'TUTUP' }}
                        </p>
                        <p class="text-sm {{ $cafeIsOpen ? 'text-green-600' : 'text-red-600' }}">
                            {{ $cafeIsOpen ? 'Pelanggan bisa memesan saat ini' : 'Semua pesanan diblokir - pelanggan melihat "Cafe Tutup"' }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.settings.toggle-cafe') }}" id="toggle-cafe-form">
                    @csrf
                    <button type="button"
                        class="tap-44 px-6 py-3 rounded-xl font-bold text-sm transition-colors {{ $cafeIsOpen ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}"
                        onclick="confirmToggleCafe('toggle-cafe-form', {{ $cafeIsOpen ? 'true' : 'false' }})">
                        {{ $cafeIsOpen ? '🔒 Tutup Cafe' : '🔓 Buka Cafe' }}
                    </button>
                </form>

            </div>
        </div>

        {{-- Close Order Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                ⏰ Close Order per Kategori
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                Atur jam close order untuk setiap kategori. Produk yang kategorinya sudah melewati jam close order akan tampil abu-abu dan tidak bisa dipesan.
            </p>

            <form action="{{ route('admin.settings.close-order') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-3">
                    @forelse($categories as $category)
                    <div class="flex items-center justify-between gap-3 bg-gray-50 rounded-lg p-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $category->name }}</p>
                            @if($category->close_order_time)
                                @if($category->isClosedOrder())
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 mt-1">
                                    🚫 Sedang Close Order
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-green-600 mt-1">
                                    ✅ Buka (tutup {{ \Carbon\Carbon::parse($category->close_order_time)->format('H:i') }})
                                </span>
                                @endif
                            @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-gray-500 mt-1">
                                ♾️ Tanpa batas waktu
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="time" 
                                   name="close_order_time[{{ $category->id }}]" 
                                   value="{{ $category->close_order_time ? \Carbon\Carbon::parse($category->close_order_time)->format('H:i') : '' }}"
                                   class="tap-44 rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-32"
                                   placeholder="HH:MM">
                            @if($category->close_order_time)
                            <button type="button" 
                                    onclick="this.parentElement.querySelector('input[type=time]').value=''"
                                    class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" 
                                    title="Hapus close order">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-400 text-sm">
                        Belum ada kategori aktif.
                    </div>
                    @endforelse
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                    <p class="text-xs text-amber-700">
                        <strong>💡 Tips:</strong> Kosongkan jam = selalu tersedia selama cafe buka. Jam setelah tengah malam (misal 02:00) berarti close order jam 2 pagi. Close order berlaku otomatis, tidak perlu dimatikan manual.
                    </p>
                </div>

                @if($categories->isNotEmpty())
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                        Simpan Close Order
                    </button>
                </div>
                @endif
            </form>
        </div>

        {{-- Notification Sound Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                🔊 Suara Notifikasi Pesanan
            </h2>
            
            <p class="text-sm text-gray-600 mb-4">
                Upload file audio (MP3/WAV) untuk mengganti suara notifikasi saat ada pesanan baru masuk.
            </p>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Preview Saat Ini</label>
                <audio controls class="w-full">
                    <source src="{{ $soundUrl }}?t={{ time() }}" type="audio/mpeg">
                    Browser Anda tidak mendukung elemen audio.
                </audio>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Suara</label>
                    <input type="file" name="notification_sound" accept=".mp3,.wav" 
                        class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2.5 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100
                        cursor-pointer border border-gray-300 rounded-lg bg-white focus:outline-none"
                        required>
                    <p class="mt-1 text-xs text-gray-500">Maksimal 2MB. Format: MP3, WAV.</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Receipt Customization Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:col-span-2">
            <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                🧾 Kustomisasi Struk
            </h2>
            <p class="text-sm text-gray-600 mb-5">
                Sesuaikan tampilan struk pembelian secara lengkap — logo, identitas, tipografi, warna, garis, ukuran kertas, dan elemen yang ditampilkan.
            </p>

            <form action="{{ route('admin.settings.receipt') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- 1. LOGO --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">📷 Logo Cafe</label>

                    @if($receiptLogo)
                    <div class="flex items-center gap-4">
                        <img src="{{ asset($receiptLogo) }}?t={{ time() }}" alt="Logo" class="w-16 h-16 object-contain rounded-lg border bg-white p-1">
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 font-medium">Logo saat ini</p>
                            <label class="inline-flex items-center gap-2 mt-1">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-xs text-red-600">Hapus logo</span>
                            </label>
                        </div>
                    </div>
                    @endif

                    <input type="file" name="receipt_logo" accept=".png,.jpg,.jpeg,.webp"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-300 rounded-lg bg-white">
                    <p class="text-xs text-gray-400">Maks 1MB. Format: PNG, JPG, WEBP.</p>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="receipt_show_logo" value="1" {{ $receiptShowLogo === '1' ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Tampilkan logo di struk</span>
                    </label>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ukuran Logo (px)</label>
                            <input type="number" name="receipt_logo_size" value="{{ $receiptLogoSize ?? '64' }}" min="32" max="200" step="4"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jarak ke Nama (px)</label>
                            <input type="number" name="receipt_logo_spacing" value="{{ $receiptLogoSpacing ?? '10' }}" min="0" max="60" step="2"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- 2. IDENTITAS CAFE --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">🏪 Identitas Cafe</label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Cafe di Struk</label>
                            <input type="text" name="receipt_cafe_name" value="{{ $receiptCafeName ?? '' }}" maxlength="80"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Kosongkan = pakai nama global">

                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tagline di Struk</label>
                            <input type="text" name="receipt_cafe_tagline" value="{{ $receiptCafeTagline ?? '' }}" maxlength="100"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Kosongkan = pakai tagline global">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">WhatsApp Cafe</label>
                            <input type="text" name="receipt_whatsapp" value="{{ $receiptWhatsapp ?? '' }}" maxlength="20"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi / Alamat</label>
                            <input type="text" name="receipt_cafe_location" value="{{ $receiptCafeLocation ?? '' }}" maxlength="200"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Jl. Contoh No. 123, Kota">
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="receipt_show_tagline" value="1" {{ ($receiptShowTagline ?? '1') === '1' ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Tampilkan tagline cafe di struk</span>
                    </label>
                </div>

                {{-- 3. TIPOGRAFI --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">🔤 Tipografi</label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Font</label>
                            <select name="receipt_font_family"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                <option value="system" {{ ($receiptFontFamily ?? 'system') === 'system' ? 'selected' : '' }}>System (default)</option>
                                <option value="serif" {{ ($receiptFontFamily ?? '') === 'serif' ? 'selected' : '' }}>Serif (klasik)</option>
                                <option value="mono" {{ ($receiptFontFamily ?? '') === 'mono' ? 'selected' : '' }}>Monospace (thermal)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ukuran Nama Cafe (px)</label>
                            <input type="number" name="receipt_header_font_size" value="{{ $receiptHeaderFontSize ?? '20' }}" min="14" max="36" step="1"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ukuran Teks Item (px)</label>
                            <input type="number" name="receipt_body_font_size" value="{{ $receiptBodyFontSize ?? '13' }}" min="10" max="20" step="1"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- 4. WARNA & TEMA --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-4">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">🎨 Warna & Tema</label>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach(['normal' => ['🎨','Normal','Warna & badge'], 'bw' => ['🖨️','Hitam Putih','Hemat tinta'], 'dark' => ['🌑','Gelap','Background hitam'], 'blue' => ['💙','Biru','Branded biru']] as $val => $meta)
                        <label class="cursor-pointer">
                            <input type="radio" name="receipt_theme" value="{{ $val }}" {{ ($receiptTheme ?? 'normal') === $val ? 'checked' : '' }} class="peer sr-only">
                            <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:bg-gray-100">
                                <div class="text-xl mb-1">{{ $meta[0] }}</div>
                                <div class="text-xs font-bold text-gray-900">{{ $meta[1] }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $meta[2] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Warna Aksen (Total, Header)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="receipt_accent_color" value="{{ $receiptAccentColor ?? '#1a1a1a' }}"
                                    class="h-9 w-14 rounded border border-gray-300 cursor-pointer p-0.5">
                                <input type="text" name="receipt_accent_color_text" value="{{ $receiptAccentColor ?? '#1a1a1a' }}" maxlength="7"
                                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                                    placeholder="#1a1a1a">
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Warna Teks Total</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="receipt_total_color" value="{{ $receiptTotalColor ?? '#1a1a1a' }}"
                                    class="h-9 w-14 rounded border border-gray-300 cursor-pointer p-0.5">
                                <input type="text" name="receipt_total_color_text" value="{{ $receiptTotalColor ?? '#1a1a1a' }}" maxlength="7"
                                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                                    placeholder="#1a1a1a">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. GARIS & BATAS --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">📏 Garis & Batas</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['dashed' => '〰️ Titik-titik', 'solid' => '— Garis Solid', 'none' => '✕ Tanpa Garis'] as $val => $label)
                        <label class="cursor-pointer flex-1 min-w-[120px]">
                            <input type="radio" name="receipt_border_style" value="{{ $val }}" {{ ($receiptBorderStyle ?? 'dashed') === $val ? 'checked' : '' }} class="peer sr-only">
                            <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50 border border-gray-200 rounded-xl p-3 text-center text-sm font-medium transition-all hover:bg-gray-100">
                                {{ $label }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-medium text-gray-600">Warna Garis:</label>
                        <input type="color" name="receipt_border_color" value="{{ $receiptBorderColor ?? '#e5e5e5' }}"
                            class="h-8 w-12 rounded border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" name="receipt_border_color_text" value="{{ $receiptBorderColor ?? '#e5e5e5' }}" maxlength="7"
                            class="w-28 rounded-lg border border-gray-300 px-2 py-1.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="#e5e5e5">
                    </div>
                </div>

                {{-- 6. UKURAN KERTAS --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">📄 Ukuran Kertas</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['thermal80' => '🖨️ 80mm Thermal', 'a5' => '📄 A5', 'a4' => '📋 A4'] as $val => $label)
                        <label class="cursor-pointer flex-1 min-w-[100px]">
                            <input type="radio" name="receipt_paper_size" value="{{ $val }}" {{ ($receiptPaperSize ?? 'thermal80') === $val ? 'checked' : '' }} class="peer sr-only">
                            <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50 border border-gray-200 rounded-xl p-3 text-center text-sm font-medium transition-all hover:bg-gray-100">
                                {{ $label }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- 7. ELEMEN YANG DITAMPILKAN --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">☑️ Elemen yang Ditampilkan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach([
                            'receipt_show_customer_info' => ['Info Customer', 'Nama, meja, tipe, tanggal', $receiptShowCustomer ?? '1'],
                            'receipt_show_payment_method' => ['Metode Pembayaran', 'QRIS, GoPay, dll.', $receiptShowPayment ?? '1'],
                            'receipt_show_item_notes' => ['Catatan Item', 'Catatan per produk (📝)', $receiptShowItemNotes ?? '1'],
                            'receipt_show_tagline_header' => ['Tagline Header', 'Tagline di bawah nama cafe', $receiptShowTagline ?? '1'],
                            'receipt_show_order_type' => ['Tipe Pesanan', 'Dine In / Takeaway', $receiptShowOrderType ?? '1'],
                            'receipt_show_table_no' => ['Nomor Meja', 'Nomor meja pelanggan', $receiptShowTableNo ?? '1'],
                        ] as $name => [$label, $desc, $val])
                        <label class="flex items-center gap-3 cursor-pointer bg-white rounded-lg p-3 border border-gray-100 hover:border-indigo-200 transition-colors">
                            <input type="checkbox" name="{{ $name }}" value="1" {{ $val === '1' ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 flex-shrink-0">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                <p class="text-xs text-gray-500">{{ $desc }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- 8. FOOTER --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">📝 Footer Struk</label>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pesan Utama</label>
                        <input type="text" name="receipt_footer_text" maxlength="200" value="{{ $receiptFooterText ?? 'Terima kasih! 🙏' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Terima kasih! 🙏">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Info Tambahan (WiFi, IG, dll.)</label>
                        <input type="text" name="receipt_footer_secondary" maxlength="200" value="{{ $receiptFooterSecondary ?? '' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="WiFi: soraya123 | IG: @soraya.cafe">
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ukuran Font Footer (px)</label>
                            <input type="number" name="receipt_footer_font_size" value="{{ $receiptFooterFontSize ?? '14' }}" min="10" max="24" step="1"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="px-6 py-3 bg-indigo-700 text-white font-bold rounded-xl hover:bg-indigo-800 transition-colors shadow-sm">
                        💾 Simpan Pengaturan Struk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

