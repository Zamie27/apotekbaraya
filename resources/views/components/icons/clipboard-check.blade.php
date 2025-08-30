@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 002 2h2a2 2 0 002-2V3a2 2 0 012 2v6h-3a3 3 0 00-3 3v3H6a2 2 0 01-2-2V5zm9.707 4.293a1 1 0 00-1.414-1.414L9 11.172 7.707 9.879a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
    <path d="M15 13a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2a1 1 0 01-1-1v-4z"></path>
</svg>