<div class="toast-container fixed top-4 right-4 z-50 space-y-2">
    @foreach($notifications as $notification)
        <div 
            class="toast-item alert {{ $this->getAlertClass($notification['type']) }} shadow-lg min-w-80 max-w-md transform transition-all duration-300 ease-in-out animate-slide-in"
            data-toast-id="{{ $notification['id'] }}"
        >
            <div class="flex items-center">
                <!-- Icon based on type -->
                @if($notification['type'] === 'success')
                    <svg class="w-6 h-6 mr-3 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @elseif($notification['type'] === 'error')
                    <svg class="w-6 h-6 mr-3 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                @elseif($notification['type'] === 'warning')
                    <svg class="w-6 h-6 mr-3 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                @else
                    <svg class="w-6 h-6 mr-3 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
                
                <!-- Message -->
                <div class="flex-1">
                    <span class="font-medium">{{ $notification['message'] }}</span>
                </div>
                
                <!-- Close button -->
                <button 
                    type="button" 
                    class="btn btn-ghost btn-sm btn-circle ml-2"
                    wire:click="removeNotification('{{ $notification['id'] }}')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endforeach
</div>

<script>
    // Auto-remove toast after duration
    document.addEventListener('livewire:init', () => {
        Livewire.on('auto-remove-toast', (data) => {
            setTimeout(() => {
                const toastElement = document.querySelector(`[data-toast-id="${data.id}"]`);
                if (toastElement) {
                    toastElement.style.transform = 'translateX(100%)';
                    toastElement.style.opacity = '0';
                    setTimeout(() => {
                        @this.removeNotification(data.id);
                    }, 300);
                }
            }, data.duration);
        });
    });
</script>