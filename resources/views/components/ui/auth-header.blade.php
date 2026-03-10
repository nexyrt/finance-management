@props([
    'title',
    'description',
])

<div class="flex w-full flex-col mb-8">
    <h1 class="text-[1.75rem] font-bold text-dark-900 dark:text-dark-50 leading-tight tracking-tight mb-1.5">{{ $title }}</h1>
    <p class="text-sm text-dark-500 dark:text-dark-400">{{ $description }}</p>
</div>
