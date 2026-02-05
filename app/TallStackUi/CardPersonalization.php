<?php

namespace App\TallStackUi;

class CardPersonalization
{
    public function __invoke(array $data): string
    {
        // Match the outline style from Transaction Categories page
        // Using zinc-200 for light mode and dark-600 for dark mode
        return 'dark:bg-dark-800 flex w-full flex-col rounded-lg bg-white border border-zinc-200 dark:border-dark-600 shadow-sm hover:shadow-md transition-shadow duration-150';
    }
}
