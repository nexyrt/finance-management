<?php

// app/Providers/AppServiceProvider.php - Minimal Flux Colors - Text, Background, Outline

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        TallStackUi::personalize()
            ->modal()
            ->block('wrapper.first', 'fixed inset-0 bg-black/30 transform transition-opacity');
    }
}
