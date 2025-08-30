@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M3 3h2l.344 2.063M7 13h10l4-8H5.344M7 13l-1.5 6h13M7 13L5.344 5.063M7 19a1 1 0 11-2 0 1 1 0 012 0zm10 0a1 1 0 102 0 1 1 0 00-2 0z" />
</svg>