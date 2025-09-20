<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Laporan Aktivitas Pengguna</h1>
        <p class="text-gray-600 mt-2">Monitor dan analisis aktivitas pengguna dalam sistem</p>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter Laporan</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Date From -->
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" 
                       id="dateFrom"
                       wire:model.live="dateFrom" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Date To -->
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" 
                       id="dateTo"
                       wire:model.live="dateTo" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- User Filter -->
            <div>
                <label for="userId" class="block text-sm font-medium text-gray-700 mb-2">Pengguna</label>
                <select wire:model.live="userId" 
                        id="userId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700 mb-2">Aksi</label>
                <select wire:model.live="action" 
                        id="action"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $actionItem)
                        <option value="{{ $actionItem }}">{{ ucfirst($actionItem) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Search and Actions Row -->
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" 
                       id="search"
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari berdasarkan deskripsi, IP, nama, atau email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button wire:click="resetFilters" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </button>
                
                <button wire:click="exportToCsv" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Results Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">
                    Hasil Laporan 
                    <span class="text-sm font-normal text-gray-500">
                        ({{ $activities->total() }} total aktivitas)
                    </span>
                </h3>
                
                <!-- Per Page Selector -->
                <div class="flex items-center gap-2">
                    <label for="perPage" class="text-sm text-gray-700">Tampilkan:</label>
                    <select wire:model.live="perPage" 
                            id="perPage"
                            class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">per halaman</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $activity->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->user)
                                    <div class="text-sm font-medium text-gray-900">{{ $activity->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $activity->user->email }}</div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Unknown User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($activity->action === 'login') bg-green-100 text-green-800
                                    @elseif($activity->action === 'logout') bg-yellow-100 text-yellow-800
                                    @elseif($activity->action === 'create') bg-blue-100 text-blue-800
                                    @elseif($activity->action === 'update') bg-purple-100 text-purple-800
                                    @elseif($activity->action === 'delete') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($activity->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate" title="{{ $activity->description }}">
                                    {{ $activity->description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->ip_address }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $activity->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $activity->created_at->format('H:i:s') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada aktivitas ditemukan</p>
                                    <p class="text-sm">Coba ubah filter atau rentang tanggal</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Memuat data...</span>
        </div>
    </div>
</div>
