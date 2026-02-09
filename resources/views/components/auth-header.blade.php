@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center mb-6">
    <h1 class="text-2xl font-bold text-dark-900 dark:text-dark-50 mb-2">{{ $title }}</h1>
    <p class="text-sm text-dark-600 dark:text-dark-400">{{ $description }}</p>
</div>
