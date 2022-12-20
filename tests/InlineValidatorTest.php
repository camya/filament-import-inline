<?php

use Camya\Filament\ComponentValidator;
use Illuminate\Validation\ValidationException;

it('shows returns validated data, if required input data is set.', function () {
    $validator = new ComponentValidator('data.demo-component-id');

    $validatedData = $validator->validate(
        [
            'title' => 'Lorem Ipsum',
        ],
        [
            'title' => [
                'required',
            ],
        ],
    );

    $this->assertSame($validatedData['title'], 'Lorem Ipsum');
});

it('throws correct validation exception if required input data is not set.', function () {
    $validator = new ComponentValidator('data.demo-component-id');

    $validator->validate(
        [
            'title' => '',
        ],
        [
            'title' => ['required'],
        ],
        [
            'title.required' => '**The title is required!**',
        ],
    );
})->throws(ValidationException::class, '**The title is required!**');

it('returns only validated data and omits input without corresponding rules.', function () {
    $validator = new ComponentValidator('data.demo-component-id');

    $validatedData = $validator->validate(
        [
            'title' => 'Lorem Ipsum',
            'invalid_input_data' => 'This data should not pass the validation.',
        ],
        [
            'title' => [
                'required',
            ],
        ],
    );

    $this->assertFalse(array_key_exists('invalid_input_data', $validatedData));
    $this->assertTrue(array_key_exists('title', $validatedData));
});

it('throws correct validation exception if setValidationError was called manually.', function () {
    $validator = new ComponentValidator('data.demo-component-id');
    $validator->setValidationError('**error-message**');
})->throws(ValidationException::class, '**error-message**');

it('generates a special component id and uses it as the error array key prefix.', function () {
    $validator = new ComponentValidator('data.component-id');

    try {
        $validator->validate(
            [
                'title' => '',
            ],
            [
                'title' => [
                    'required',
                ],
            ],
        );
    } catch (ValidationException $e) {
        $componentErrors = $validator::componentErrors('data.component-id', $e->errors());

        $this->assertSame('data_component-id_title', key($componentErrors));
    }
});
