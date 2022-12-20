<?php

use Camya\Filament\Forms\Components\ImportInlineInput;
use Camya\Filament\Tests\Support\TestableForm;
use Livewire\Livewire;

it('returns OK if component is used.', function () {
    TestableForm::$formSchema = [
        ImportInlineInput::make('Import'),
    ];

    $component = Livewire::test(TestableForm::class);

    $component->assertOk();
});
