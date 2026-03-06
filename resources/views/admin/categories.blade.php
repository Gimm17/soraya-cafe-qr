<x-admin-layout title="Kategori">
    <div class="admin-page-header">
        <h1>Kategori</h1>
        <p>Kelola kategori menu untuk memudahkan pelanggan mencari produk.</p>
    </div>

    {{-- Add Category --}}
    <div class="section-card mb-6">
        <div class="section-card-header">
            <div>
                <h3>Tambah Kategori</h3>
            </div>
        </div>
        <div class="section-card-body">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @csrf
                <input class="admin-select" name="name" placeholder="Nama kategori" required>
                <input class="admin-select" type="number" name="sort_order" placeholder="Sort (0...)" value="0">
                <button type="submit" class="btn-primary tap-44 justify-center">Tambah</button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="section-card overflow-hidden">
        <div class="section-card-header">
            <div>
                <h3>Daftar Kategori</h3>
            </div>
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Sort</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-5 font-semibold text-gray-900">{{ $c->name }}</td>
                            <td class="py-3 px-5 text-gray-700">{{ $c->sort_order }}</td>
                            <td class="py-3 px-5 text-right">
                                <form id="delete-category-{{ $c->id }}" method="POST" action="{{ route('admin.categories.delete', $c) }}" class="hidden">@csrf</form>
                                <button type="button" onclick="confirmDelete('delete-category-{{ $c->id }}', 'kategori {{ $c->name }}')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 border border-red-200 text-red-700 font-semibold hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile cards -->
        <div class="md:hidden divide-y ui-divider">
            @foreach($categories as $c)
                <div class="p-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $c->name }}</p>
                        <p class="text-xs text-muted">Sort: {{ $c->sort_order }}</p>
                    </div>
                    <form id="delete-category-m-{{ $c->id }}" method="POST" action="{{ route('admin.categories.delete', $c) }}" class="hidden">@csrf</form>
                    <button type="button" onclick="confirmDelete('delete-category-m-{{ $c->id }}', 'kategori {{ $c->name }}')" class="tap-44 px-3 py-2 rounded-xl bg-red-50 border border-red-200 text-red-700 font-bold">
                        Hapus
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
