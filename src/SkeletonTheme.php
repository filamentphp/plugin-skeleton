<?php

namespace Filament;

use Filament\Context;
use Filament\Contracts\Plugin;
use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentAsset;

class SkeletonTheme implements Plugin
{
    public function getId(): string
    {
        return 'skeleton-theme';
    }

    public function register(Context $context): void
    {
        FilamentAsset::register([
            Theme::make('skeleton', __DIR__ . '/../resources/dist/skeleton.css'),
        ]);

        $context
            ->theme('skeleton');
    }

    public function boot(Context $context): void
    {
        //
    }
}
