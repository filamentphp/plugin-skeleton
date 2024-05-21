<?php

namespace Tsetis\ChartPlugin;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Widgets\ChartWidget;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ChartPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'chart-plugin';

    public static string $viewNamespace = 'chart-plugin';

    public function configurePackage(Package $package): void
    {

        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        Livewire::component('chart-widget', ChartWidget::class);
        // Asset Registration
        FilamentAsset::register(
            assets: [
                AlpineComponent::make('chart-plugin', __DIR__ . '/../resources/dist/chart-widget.js'),
            ],
            package: 'tsetis/chart-plugin'
        );
    }

    protected function getAssetPackageName(): ?string
    {
        return 'filamentphp/plugin-plugin-skeleton';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('plugin-skeleton', __DIR__ . '/../resources/dist/components/plugin-skeleton.js'),
            Css::make('plugin-skeleton-styles', __DIR__ . '/../resources/dist/plugin-skeleton.css'),
            Js::make('plugin-skeleton-scripts', __DIR__ . '/../resources/dist/plugin-skeleton.js'),
        ];
    }
}
