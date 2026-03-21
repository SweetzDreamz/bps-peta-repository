<aside class="flex flex-col w-64 min-h-screen bg-gray-900 text-white shrink-0">

    {{-- Logo --}}
    <div class="px-6 py-6 border-b border-gray-700">
        <h1 class="text-xl font-bold text-white">BPS Kota Bogor</h1>
        <p class="text-xs text-gray-400 mt-1">Sistem Arsip Peta</p>
    </div>

    {{-- Navigasi --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- TRANSAKSI --}}
        <p class="text-xs uppercase text-gray-500 font-semibold px-3 pt-5 pb-2">Transaksi</p>

        {{-- Dropdown History --}}
        <div x-data="{ open: {{ request()->routeIs('history.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                           {{ request()->routeIs('history.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    History
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-700 pl-3">
                <a href="#"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('history.wa') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span>
                    History WA
                </a>
                <a href="#"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('history.wb') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span>
                    History WB
                </a>
                <a href="#"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('history.sls') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span>
                    History SLS
                </a>
            </div>
        </div>

        {{-- DATA MASTER (khusus supervisor) --}}
        @if(auth()->user()->isSupervisor())

        <p class="text-xs uppercase text-gray-500 font-semibold px-3 pt-5 pb-2">Data Master</p>

        {{-- Dropdown Management Sketsa --}}
        <div x-data="{ open: {{ request()->routeIs('sketsa.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                           {{ request()->routeIs('sketsa.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Management Sketsa
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-700 pl-3">
                <a href="#"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('sketsa.wa') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span>
                    Sketsa WA
                </a>
                <a href="#"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('sketsa.wb') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span>
                    Sketsa WB
                </a>
                <a href="#"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('sketsa.sls') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span>
                    Sketsa SLS
                </a>
            </div>
        </div>

        {{-- Data Wilayah --}}
        <a href="#"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  {{ request()->routeIs('wilayah.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Data Wilayah
        </a>

        {{-- Data Kegiatan --}}
        <a href="#"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  {{ request()->routeIs('kegiatan.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Data Kegiatan
        </a>

        {{-- Data Pengguna --}}
        <a href="#"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  {{ request()->routeIs('pengguna.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Data Pengguna
        </a>

        @endif

    </nav>

    {{-- User Info --}}
    <div class="px-4 py-4 border-t border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>

</aside>