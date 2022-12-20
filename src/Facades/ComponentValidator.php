<?php

namespace Camya\Filament\Facades;

use Camya\Filament\ComponentValidator;
use Illuminate\Support\Facades\Facade;

/** @mixin ComponentValidator */
class ComponentValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ComponentValidator::class;
    }
}
