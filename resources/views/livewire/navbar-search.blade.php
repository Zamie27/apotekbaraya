<div class="relative">
    <!-- Search Input -->
    <div class="flex join hover:shadow-md transition-shadow">
        <input 
            type="text" 
            placeholder="Cari obat, vitamin, atau produk kesehatan (hanya huruf dan angka)..."
            class="input input-bordered join-item flex-1 w-40 sm:w-56 md:w-80 input-sm sm:input-md focus:outline-none focus:ring-2 focus:ring-green-500 text-xs sm:text-sm"
            wire:model="query"
            @keydown.enter.prevent="$wire.search()"
            pattern="[a-zA-Z0-9\s]*"
            title="Hanya huruf, angka, dan spasi yang diperbolehkan"
            maxlength="100"
            oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '')"
        />
        <button 
            type="button" 
            class="rounded-l-none btn btn-success btn-sm sm:btn-md"
            wire:click="search"
        >
            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </button>
    </div>
</div>