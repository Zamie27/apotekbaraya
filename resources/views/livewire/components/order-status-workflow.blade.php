<div class="order-status-workflow">
    @if($size === 'compact')
        <!-- Compact Timeline View -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-2">
            @foreach($workflowSteps as $index => $step)
                <div class="flex items-center flex-shrink-0">
                    <!-- Step Circle -->
                    <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 
                        {{ $step['completed'] ? 'bg-success border-success text-success-content' : 
                           ($step['current'] ? 'bg-primary border-primary text-primary-content' : 'bg-base-200 border-base-300 text-base-content') }}">
                        @if($step['completed'])
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <span class="text-xs font-semibold">{{ $index + 1 }}</span>
                        @endif
                    </div>
                    
                    <!-- Step Label -->
                    <div class="ml-2 text-sm font-medium {{ $step['current'] ? 'text-primary' : 'text-base-content' }}">
                        {{ $step['label'] }}
                    </div>
                    
                    <!-- Connector Line -->
                    @if(!$loop->last && $step['status'] !== 'cancelled')
                        <div class="w-8 h-0.5 mx-2 {{ $step['completed'] ? 'bg-success' : 'bg-base-300' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- Full Timeline View -->
        <div class="space-y-6">
            @foreach($workflowSteps as $index => $step)
                <div class="flex items-start space-x-4">
                    <!-- Timeline Line -->
                    <div class="flex flex-col items-center">
                        <!-- Step Circle -->
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 
                            {{ $step['completed'] ? 'bg-success border-success text-success-content' : 
                               ($step['current'] ? 'bg-primary border-primary text-primary-content' : 'bg-base-200 border-base-300 text-base-content') }}">
                            @if($step['completed'])
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($step['current'])
                                <div class="w-3 h-3 bg-current rounded-full animate-pulse"></div>
                            @else
                                <span class="text-sm font-semibold">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        
                        <!-- Connector Line -->
                        @if(!$loop->last && $step['status'] !== 'cancelled')
                            <div class="w-0.5 h-12 mt-2 {{ $step['completed'] ? 'bg-success' : 'bg-base-300' }}"></div>
                        @endif
                    </div>
                    
                    <!-- Step Content -->
                    <div class="flex-1 pb-8">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold {{ $step['current'] ? 'text-primary' : 'text-base-content' }}">
                                {{ $step['label'] }}
                            </h4>
                            @if($step['date'])
                                <span class="text-sm text-base-content/70">
                                    {{ $step['date']->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>
                        
                        @if($showDetails)
                            <p class="text-base-content/80 mt-1">
                                {{ $step['description'] }}
                            </p>
                        @endif
                        
                        <!-- Special handling for cancelled status -->
                        @if($step['status'] === 'cancelled')
                            <div class="mt-2 p-3 bg-error/10 border border-error/20 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-error" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-error font-medium">Pesanan Dibatalkan</span>
                                </div>
                                @if($order->cancel_reason)
                                    <p class="text-error/80 mt-1 text-sm">
                                        Alasan: {{ $order->cancel_reason }}
                                    </p>
                                @endif
                                @if($order->cancelledBy)
                                    <p class="text-error/80 mt-1 text-sm">
                                        Dibatalkan oleh: {{ $order->cancelledBy->name }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    <!-- Order Summary Card -->
    @if($size === 'normal')
        <div class="mt-6 p-4 bg-base-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="font-semibold text-base-content">Status Saat Ini</h5>
                    <p class="text-base-content/70">{{ $order->status_label }}</p>
                </div>
                <div class="text-right">
                    <span class="badge {{ $order->status_badge_color }} badge-lg">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
            
            @if($order->canBeCancelled())
                <div class="mt-3 pt-3 border-t border-base-300">
                    <p class="text-sm text-base-content/70">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Pesanan ini masih dapat dibatalkan
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>