<div>
    @if($this->activePrescriptionsCount > 0)
        <span class="absolute -top-1 -right-1 sm:-top-1.5 sm:-right-1.5 bg-red-500 text-white text-xs rounded-full h-5 w-5 sm:h-6 sm:w-6 flex items-center justify-center">
            {{ $this->activePrescriptionsCount }}
        </span>
    @endif
</div>