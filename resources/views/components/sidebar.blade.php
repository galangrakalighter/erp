<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-white border-r border-gray-200 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="p-6 flex items-center gap-3">
        <div class="w-8 h-8 bg-blue-600 rounded-full"></div>
        <span class="font-bold text-lg">Warehouse System</span>
    </div>
    
    <nav class="mt-6 px-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>
        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'penjualan')
        <a href="{{ route('purchase-order.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('purchase-order.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-50' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Purchase Order
        </a>
        @endif
        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'gudang')
        <a href="{{ route('warehouse.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('warehouse.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-50' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Warehouse
        </a>
        @endif
        @if(Auth::user()->role == 'admin')
        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21V10.5M12 21V4m-7 17V10.5M5 21H19M5 10.5H19M5 10.5V5a2 2 0 012-2h10a2 2 0 012 2v5.5"></path>
            </svg>
            <span class="font-medium">Manajemen Cabang</span>
        </a>
        @endif
        @if(Auth::user()->role == 'admin')
        <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21V10.5M12 21V4m-7 17V10.5M5 21H19M5 10.5H19M5 10.5V5a2 2 0 012-2h10a2 2 0 012 2v5.5"></path>
            </svg>
            <span class="font-medium">Sales</span>
        </a>
        @endif
    </nav>
</aside>