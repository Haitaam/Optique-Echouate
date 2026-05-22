@props([
    'count' => 1,
    'class' => 'h-4 w-full',
])

@for ($i = 0; $i < $count; $i++)
    <div data-skeleton {{ $attributes->merge(['class' => $class]) }}></div>
@endfor
