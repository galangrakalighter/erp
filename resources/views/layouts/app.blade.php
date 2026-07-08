<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Warehouse System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 text-gray-900">

    <div id="overlay" class="fixed inset-0 bg-gray-900/50 z-20 hidden lg:hidden"></div>

    @include('components.sidebar')

    <main id="main-content" class="lg:ml-64 min-h-screen transition-all duration-300">
       <header class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
            <div class="flex items-center gap-4">
                <button id="menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                </button>
            </div>

            <div class="relative">
                <button id="user-menu-btn" class="flex items-center gap-3 hover:bg-gray-100 px-3 py-1.5 rounded-xl transition">
                    <div class="w-8 h-8 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center text-blue-600 text-xs font-bold">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <span class="text-sm font-medium hidden md:block">{{ auth()->user()->name ?? 'Administrator' }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 hidden animate-in fade-in zoom-in-95 duration-200">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="p-6 md:p-8">
            @yield('content')
        </div>
    </main>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        menuBtn.onclick = () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); };
        overlay.onclick = () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); };

        function formatRupiah(input) {
            // Ambil nilai, hapus semua karakter selain angka
            let value = input.value.replace(/[^0-9]/g, '');
            
            // Format ke Rupiah
            if (value) {
                let formatter = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                });
                
                // Simpan format ke input, tapi hilangkan simbol "Rp" agar tetap clean
                // Jika ingin ada simbol Rp, bisa gunakan: input.value = formatter.format(value);
                input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            } else {
                input.value = '';
            }

            if(input.id == "harga_per_box"){
                input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                calculateTotalOrder();
            }

            if(input.id == "sisa_pembayaran"){
                terbilang(value, 'terbilang');
            }
        }

        function terbilang(bilangan, targetId) {
            bilangan = String(bilangan).replace(/\./g, ''); 
            
            if (bilangan == "" || isNaN(bilangan)) {
                document.getElementById(targetId).value = "";
                return;
            }

            const angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

            function baca(n) {
                let str = "";
                if (n < 12) str = " " + angka[n];
                else if (n < 20) str = baca(n - 10) + " Belas";
                else if (n < 100) str = baca(Math.floor(n / 10)) + " Puluh" + baca(n % 10);
                else if (n < 200) str = " Seratus" + baca(n - 100);
                else if (n < 1000) str = baca(Math.floor(n / 100)) + " Ratus" + baca(n % 100);
                else if (n < 2000) str = " Seribu" + baca(n - 1000);
                else if (n < 1000000) str = baca(Math.floor(n / 1000)) + " Ribu" + baca(n % 1000);
                // Tambahan logika untuk jutaan ke atas
                else if (n < 1000000000) str = baca(Math.floor(n / 1000000)) + " Juta" + baca(n % 1000000);
                else if (n < 1000000000000) str = baca(Math.floor(n / 1000000000)) + " Milyar" + baca(n % 1000000000);
                else if (n < 1000000000000000) str = baca(Math.floor(n / 1000000000000)) + " Triliun" + baca(n % 1000000000000);
                return str;
            }
            let hasil = baca(parseInt(bilangan)).trim() + " Rupiah";
            let teksFinal = hasil.charAt(0).toUpperCase() + hasil.slice(1);
            
            // Tampilkan ke input target
            document.getElementById(targetId).value = teksFinal;
        }

        function calculateTotalOrder() {
            // Ambil elemen
            const jumlahBoxInput = document.getElementById('jumlah_box');
            const hargaPerBoxInput = document.getElementById('harga_per_box');
            const totalOrderInput = document.getElementById('total_order'); // Pastikan ID ini ada di form Anda

            // Bersihkan nilai: Hapus titik atau koma
            // .replace(/\./g, '') menghapus semua titik
            // .replace(/,/g, '') menghapus semua koma
            const jumlahBox = parseInt(jumlahBoxInput.value.replace(/\./g, '').replace(/,/g, '')) || 0;
            const hargaPerBox = parseInt(hargaPerBoxInput.value.replace(/\./g, '').replace(/,/g, '')) || 0;

            // Hitung total
            let total = jumlahBox * hargaPerBox;

            // Tampilkan hasil (opsional: format kembali ke rupiah jika perlu)
            if (totalOrderInput) {
                totalOrderInput.value = total.toLocaleString('id-ID');
            }

            console.log("Total:", total);
        }
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');

        userMenuBtn.onclick = (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        };

        // Menutup dropdown saat klik di luar area
        window.onclick = (e) => {
            if (!userMenuBtn.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        };
    </script>
</body>
</html>