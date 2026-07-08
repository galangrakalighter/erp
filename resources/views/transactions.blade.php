@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-6 animate-in fade-in duration-500">
    
    <div>
        <h1 class="text-2xl font-bold">Riwayat Transaksi</h1>
        <p class="text-gray-500">Daftar arus keluar masuk dana perusahaan.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-sm">Tanggal</th>
                        <th class="px-6 py-4 font-semibold text-sm">Deskripsi</th>
                        <th class="px-6 py-4 font-semibold text-sm">Kategori</th>
                        <th class="px-6 py-4 font-semibold text-sm">Nominal</th>
                        <th class="px-6 py-4 font-semibold text-sm text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transactions as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $t->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 font-medium">{{ $t->description }}</td>
                        <td class="px-6 py-4 text-sm">{{ $t->category }}</td>
                        <td class="px-6 py-4 font-semibold {{ $t->type == 'keluar' ? 'text-red-600' : 'text-green-600' }}">
                            {{ $t->type == 'keluar' ? '-' : '+' }} {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openDetailModal({{ $t->toJson() }})" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="modal-trans-detail" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Detail Transaksi</h2>
        <div class="space-y-4">
            <div><p class="text-xs text-gray-400">Deskripsi</p><p id="det-desc" class="font-medium"></p></div>
            
            <!-- Area Daftar Barang -->
            <div id="det-items-container">
                <p class="text-xs text-gray-400 mb-2">Daftar Barang:</p>
                <div id="det-items-list" class="space-y-2 border rounded-xl p-3 bg-gray-50 text-sm">
                    <!-- List barang akan masuk ke sini -->
                </div>
            </div>

            <div class="border-t pt-4">
                <p class="text-xs text-gray-400">Total Nominal</p>
                <p id="det-amount" class="text-lg font-bold text-red-600"></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="toggleModal('modal-trans-detail', false)" class="px-6 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>

<script>
    function toggleModal(id, show) { document.getElementById(id).classList.toggle('hidden', !show); }
    
    function openDetailModal(t) {
        document.getElementById('det-desc').textContent = t.description;
        document.getElementById('det-amount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(t.amount);
        
        const itemList = document.getElementById('det-items-list');
        itemList.innerHTML = ''; // Reset

        // Cek apakah ada data requisition dan items
        if (t.requisition && t.requisition.items) {
            t.requisition.items.forEach(item => {
                const namaBarang = item.warehouse ? item.warehouse.barang : 'Barang Terhapus';
                const harga = new Intl.NumberFormat('id-ID').format(item.harga || 0);
                itemList.innerHTML += `
                    <div class="flex justify-between border-b last:border-0 pb-1">
                        <span>${namaBarang} <span class="text-gray-500 text-xs">x${item.jumlah_minta}</span></span>
                        <span class="font-medium">Rp ${harga}</span>
                    </div>
                `;
            });
        } else {
            itemList.innerHTML = '<p class="text-gray-400 italic">Data barang tidak tersedia.</p>';
        }

        toggleModal('modal-trans-detail', true);
    }
</script>
@endsection