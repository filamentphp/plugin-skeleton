<?php

namespace VendorName\Skeleton;

//use Filament\Support\Assets\Js;
//use Filament\Support\Assets\Css;
use Filament\PluginServiceProvider;
use Filament\Support\Assets\Asset;
use Spatie\LaravelPackageTools\Package;
//use Filament\Support\Assets\AlpineComponent;
use VendorName\Skeleton\Commands\SkeletonCommand;

class SkeletonServiceProvider extends PluginServiceProvider
{
    public static string $name = 'skeleton';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_skeleton_table')
            ->hasCommand(SkeletonCommand::class);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->bind('skeleton', function (): Skeleton {
            return new Skeleton();
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
    }

    protected function getAssetPackage(): ?string
    {
        return 'skeleton';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            //             AlpineComponent::make('skeleton', __DIR__ . '/../resources/dist/components/skeleton.js'),
            //             Css::make('echo', __DIR__ . '/../resources/dist/skeleton.js'),
            //             Js::make('echo', __DIR__ . '/../resources/dist/skeleton.js'),
        ];
    }
}
