<?php

namespace VendorName\Skeleton;

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Icon;
use Filament\Support\Icons\IconManager;
use Illuminate\Filesystem\Filesystem;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VendorName\Skeleton\Commands\SkeletonCommand;
use VendorName\Skeleton\Testing\TestsSkeleton;

class SkeletonServiceProvider extends PackageServiceProvider
{
    public static string $name = 'skeleton';

    public static string $viewNamespace = 'skeleton';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub(':vendor_slug/:package_slug');
            });

        $configFileName = $package->shortName();

        if (file_exists($this->package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($this->package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($this->package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($this->package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        //        Facade Registration
        $this->app->bind('skeleton', function (): Skeleton {
            return new Skeleton();
        });

        //        Context Registration
        $this->app->resolving('skeleton', function () {
            foreach ($this->getContexts() as $context) {
                Filament::registerContext($context);
            }
        });

        //        Asset Registration
        $this->app->resolving(AssetManager::class, function () {
            FilamentAsset::register($this->getAssets(), $this->getAssetPackage());
            FilamentAsset::registerScriptData($this->getScriptData(), $this->getAssetPackage());
        });

        //        Icon Registration
        $this->app->resolving(IconManager::class, function () {
            FilamentIcon::register($this->getIcons());
        });
    }

    public function packageBooted(): void
    {
        $this->registerMacros();

        //        Handle Stubs
        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__.'/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/skeleton/{$file->getFilename()}"),
                ], 'forms-stubs');
            }
        }

        //        Testing
        TestableLivewire::mixin(new TestsSkeleton());
    }

    protected function getAssetPackage(): ?string
    {
        return static::$name ?? null;
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            //  AlpineComponent::make('skeleton', __DIR__ . '/../resources/dist/components/skeleton.js'),
            Css::make('skeleton-styles', __DIR__.'/../resources/dist/skeleton.js'),
            Js::make('skeleton-scripts', __DIR__.'/../resources/dist/skeleton.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            SkeletonCommand::class,
        ];
    }

    /**
     * @return array<Context>
     */
    protected function getContexts(): array
    {
        return [];
    }

    /**
     * @return array<string, Icon>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_skeleton_table',
        ];
    }

    protected function registerMacros(): void
    {
    }
}
