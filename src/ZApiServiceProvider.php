<?php

namespace Tiagofv\ZApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tiagofv\ZApi\Commands\ZApiCommand;

class ZApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('zapi')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_zapi_table')
            ->hasCommand(ZApiCommand::class);
    }
}
