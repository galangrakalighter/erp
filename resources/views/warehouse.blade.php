@extends('layouts.app')

@section('title', 'Warehouse')
@section('page-title', 'Warehouse')

@section('content')
<div class="space-y-6 animate-in fade-in duration-500">
    
    @if (session('success'))
        <div id="flash-msg" class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold">Warehouse</h1>
            <p class="text-gray-500">Kelola data barang warehouse.</p>
        </div>
        <button onclick="toggleModal('modal-add', true)" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition flex items-center gap-2 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Barang
        </button>
    </div>

    <form method="GET" action="{{ route('warehouse.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" 
            placeholder="Cari nama barang..." 
            class="w-full md:w-80 px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
        @if(Auth::user()->cabang == 'Pusat')
        <select name="cabang" class="px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
            <option value="">Semua Cabang</option>
            @foreach(['Jakarta', 'Bekasi'] as $c)
            <option value="{{ $c }}"  {{ request('cabang') == $c ? 'selected' : ''}}>{{ $c }}</option>
            @endforeach
        </select>
        @endif
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
            Cari
        </button>
        @if(request('search') || request('cabang'))
            <a href="{{ route('warehouse.index') }}" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-sm">No</th>
                        <th class="px-6 py-4 font-semibold text-sm">Barang</th>
                        <th class="px-6 py-4 font-semibold text-sm">Jumlah</th>
                        {{-- <th class="px-6 py-4 font-semibold text-sm">Tipe</th> --}}
                        <th class="px-6 py-4 font-semibold text-sm">Harga</th>
                        <th class="px-6 py-4 font-semibold text-sm">Satuan</th>
                        @if(Auth::user()->cabang == "Pusat" && Auth::user()->role == "admin")
                        <th class="px-6 py-4 font-semibold text-sm">Cabang</th>
                        @endif
                        <th class="px-6 py-4 font-semibold text-sm">Tanggal Masuk</th>
                        <th class="px-6 py-4 font-semibold text-sm text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($warehouse as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium">{{ $item->barang }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item->jumlah }}</td>
                        <td class="px-6 py-4 text-sm">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item->satuan }}</td>
                        @if(Auth::user()->cabang == "Pusat" && Auth::user()->role == "admin")
                            <td class="px-6 py-4 text-sm">{{ $item->cabang }}</td>
                        @endif
                        <td class="px-6 py-4 text-sm">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 flex justify-center gap-2">
                            @if(Auth::user()->cabang != $item->cabang)
                            <button class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @else
                            <button onclick="openEditModal({{ json_encode($item) }})" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button onclick="openDeleteModal('{{ route('warehouse.destroy', $item->id) }}')" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-add" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl">
        <h2 class="text-xl font-bold mb-6">Tambah Barang</h2>
        <form action="{{ route('warehouse.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="barang" placeholder="Nama Barang" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                @error('barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <input type="number" name="jumlah" placeholder="Jumlah" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
            <input type="text" name="harga" placeholder="Harga" oninput="formatRupiah(this)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
            <input type="text" value="centimeter" name="satuan" placeholder="Satuan (ex: Pcs, Kg)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-gray-100 cursor-not-allowed text-gray-500" readonly>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="toggleModal('modal-add', false)" class="px-6 py-2 text-gray-600">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-edit" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl">
        <h2 class="text-xl font-bold mb-6">Edit Barang</h2>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <input type="text" name="barang" id="edit-barang" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            <input type="number" name="jumlah" id="edit-jumlah" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none"> 
            <input type="number" name="harga" id="edit-harga" oninput="formatRupiah(this)" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            <input type="text" name="satuan" id="edit-satuan" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-gray-100 cursor-not-allowed text-gray-500" readonly>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="toggleModal('modal-edit', false)" class="px-6 py-2 text-gray-600">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-delete" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-sm p-8 text-center shadow-2xl">
        <h2 class="text-xl font-bold mb-4">Hapus Barang?</h2>
        <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus barang ini?</p>
        <form id="deleteForm" method="POST">
            @csrf @method('DELETE')
            <div class="flex justify-center gap-3">
                <button type="button" onclick="toggleModal('modal-delete', false)" class="px-6 py-2 bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl">Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id, show) {
        document.getElementById(id).classList.toggle('hidden', !show);
    }

    function openEditModal(item) {
        document.getElementById('editForm').action = '/warehouse/' + item.id;
        document.getElementById('edit-barang').value = item.barang;
        document.getElementById('edit-jumlah').value = item.jumlah;
        document.getElementById('edit-harga').value = item.harga;
        document.getElementById('edit-satuan').value = item.satuan;
        toggleModal('modal-edit', true);
    }

    function openDeleteModal(url) {
        document.getElementById('deleteForm').action = url;
        toggleModal('modal-delete', true);
    }

    // Auto-hide Flash
    setTimeout(() => {
        const msg = document.getElementById('flash-msg');
        if(msg) msg.style.display = 'none';
    }, 3000);

    // ESC to Close
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') {
            ['modal-add', 'modal-edit', 'modal-delete'].forEach(id => toggleModal(id, false));
        }
    });
</script>
@endsection