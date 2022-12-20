<?php

namespace Camya\Laravel\Importer\Facades;

use Camya\Laravel\Importer\ImportManager;
use Illuminate\Support\Facades\Facade;

/** @mixin ImportManager */
class Import extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ImportManager::class;
    }
}
