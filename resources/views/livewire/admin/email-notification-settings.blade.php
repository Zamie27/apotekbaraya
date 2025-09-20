<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Email Notifications</h1>
            <p class="text-gray-600">Kelola dan monitor notifikasi email sistem</p>
        </div>
        <button wire:click="processPendingNotifications" 
                class="btn btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
            Proses Pending
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Notifikasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Terkirim</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['sent'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Gagal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['failed'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Notifikasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           wire:model.live="search" 
                           placeholder="Email, subjek, atau nama..."
                           class="input input-bordered w-full">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="statusFilter" class="select select-bordered w-full">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="sent">Terkirim</option>
                        <option value="failed">Gagal</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Notifikasi</label>
                    <select wire:model.live="typeFilter" class="select select-bordered w-full">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" 
                           wire:model.live="dateFilter" 
                           class="input input-bordered w-full">
                </div>
                
                <div class="flex items-end">
                    <button wire:click="resetFilters" 
                            class="btn btn-outline w-full">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left">Penerima</th>
                        <th class="text-left">Subjek</th>
                        <th class="text-left">Jenis Event</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Dibuat</th>
                        <th class="text-left">Dikirim</th>
                        <th class="text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-gray-50">
                            <td>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $notification->recipient_email }}</div>
                                    @if($notification->user)
                                        <div class="text-sm text-gray-500">{{ $notification->user->name }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="max-w-xs truncate" title="{{ $notification->subject }}">
                                    {{ $notification->subject }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-outline">
                                    {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                </span>
                            </td>
                            <td>
                                @if($notification->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($notification->status === 'sent')
                                    <span class="badge badge-success">Terkirim</span>
                                @elseif($notification->status === 'failed')
                                    <span class="badge badge-error">Gagal</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">
                                    {{ $notification->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $notification->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td>
                                @if($notification->sent_at)
                                    <div class="text-sm text-gray-900">
                                        {{ $notification->sent_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $notification->sent_at->format('H:i') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    @if($notification->status === 'failed')
                                        <button wire:click="retryNotification({{ $notification->id }})" 
                                                class="btn btn-xs btn-warning"
                                                title="Coba Lagi">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <button wire:click="deleteNotification({{ $notification->id }})" 
                                            class="btn btn-xs btn-error"
                                            onclick="return confirm('Yakin ingin menghapus notifikasi ini?')"
                                            title="Hapus">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        @if($notification->error_message)
                            <tr>
                                <td colspan="7" class="bg-red-50 border-l-4 border-red-400 p-4">
                                    <div class="text-sm text-red-700">
                                        <strong>Error:</strong> {{ $notification->error_message }}
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                Tidak ada notifikasi ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success mt-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mt-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif
</div>
