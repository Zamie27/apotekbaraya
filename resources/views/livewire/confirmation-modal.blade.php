<div>
    <!-- Modal Backdrop -->
    <div 
        class="fixed inset-0 z-50 overflow-y-auto {{ $show ? '' : 'hidden' }}"
        style="{{ $show ? 'display: block;' : 'display: none;' }}"
    >
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <!-- Modal container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal content -->
            <div 
                class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all"
            >
                <!-- Modal header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $title }}
                        </h3>
                        <button 
                            wire:click="cancel"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal body -->
                <div class="px-6 py-4">
                    <div class="flex items-start space-x-3">
                        <!-- Warning icon -->
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        
                        <!-- Message -->
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ $message }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button 
                        wire:click="cancel"
                        class="btn btn-ghost btn-sm"
                    >
                        {{ $cancelText }}
                    </button>
                    
                    <button 
                        wire:click="confirm"
                        class="btn {{ $confirmButtonClass }} btn-sm"
                    >
                        {{ $confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>