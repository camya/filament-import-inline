<?php

namespace Camya\Filament;

use Camya\Laravel\Importer\ImportManager;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentImportInlineServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-import-inline')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }

    protected function getStyles(): array
    {
        return [
            'filament-import-inline-styles' => __DIR__.'/../resources/dist/filament-import-inline.css',
        ];
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(ImportManager::class, function () {
            return new ImportManager();
        });
    }
}
