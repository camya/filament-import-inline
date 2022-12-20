<?php

namespace Camya\Filament;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ComponentValidator
{
    protected string $validationPrefix;

    public function __construct(protected string $componentId)
    {
        $this->validationPrefix = self::createValidationPrefix($componentId);
    }

    public function validate(array $data, array $rules, array $messages = []): array
    {
        throw_if(! isset($this->validationPrefix), new \Exception('validationPrefix not set, set with forComponentId() first.'));

        $validatedData = Validator::make(
            data: $this->addValidationPrefixKeys($data),
            rules: $this->addValidationPrefixKeys($rules),
            messages: $this->addValidationPrefixKeys($messages),
        )->validate();

        return $this->removeValidationPrefix($validatedData);
    }

    protected function addValidationPrefixKeys(array $data): array
    {
        return collect($data)
            ->mapWithKeys(fn ($value, $key) => [$this->validationPrefix.$key => $value])
            ->toArray();
    }

    protected function removeValidationPrefix(array $data): array
    {
        return collect($data)
            ->mapWithKeys(fn ($value, $key) => [str_replace($this->validationPrefix, '', $key) => $value])
            ->toArray();
    }

    public function setValidationError(null|string $message, null|string $key = null): void
    {
        $key ??= uniqid('error_key', true);
        $message ??= trans('filament-import-inline::package.import_inline_validation_invalid_input');
        throw ValidationException::withMessages([$this->validationPrefix.$key => $message]);
    }

    public static function componentErrors(string $componentId, array $errors): array
    {
        $validationPrefix = self::createValidationPrefix($componentId);

        $componentErrors = [];
        foreach ($errors as $errorKey => $error) {
            if (str_starts_with($errorKey, $validationPrefix)) {
                $componentErrors[$errorKey] = $error;
            }
        }

        return $componentErrors;
    }

    protected static function createValidationPrefix(string $componentId): string
    {
        return str($componentId)
            ->lower()
            ->replace('.', '_')
            ->append('_');
    }
}
