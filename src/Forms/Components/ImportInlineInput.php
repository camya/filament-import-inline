<?php

namespace Camya\Filament\Forms\Components;

use Camya\Filament\ComponentValidator;
use Closure;
use Filament\Forms\Components\TextInput;

class ImportInlineInput extends TextInput
{
    protected string $view = 'filament-import-inline::forms.fields.import-inline-input';

    protected null|string|Closure $statusMessageHtml = null;

    protected null|string|Closure $dataHelperLink = null;

    protected null|string $dataHelperLabel = null;

    protected bool $insertOnPaste = true;

    protected string|Closure|null $placeholder = null;

    protected string|Closure|null $dataPlaceholder = null;

    protected int $dataInputRows = 1;

    protected string|Closure|null $dataHelperHtml = null;

    public static function make(string $name): static
    {
        $data = parent::make($name);

        $data->dehydrated(false);

        $data->placeholder ??= trans('filament-import-inline::package.import_inline_placeholder_field');
        $data->dataPlaceholder ??= trans('filament-import-inline::package.import_inline_placeholder_textarea');

        return $data;
    }

    public function dataHelperLink(string|Closure $link, string|null $label = null): static
    {
        $this->dataHelperLink = $link;
        $this->dataHelperLabel = $label;

        return $this;
    }

    public function getDataHelperLink(): string|null
    {
        return $this->evaluate($this->dataHelperLink);
    }

    public function getDataHelperLinkLabel(): string
    {
        return $this->dataHelperLabel ?? trans('filament-import-inline::package.import_inline_action_help_label');
    }

    public function statusMessage(string|Closure|null $message): static
    {
        $this->statusMessageHtml = $message;

        return $this;
    }

    public function getStatusMessageHtml(): string|null
    {
        return $this->evaluate($this->statusMessageHtml);
    }

    public function insertOnPaste(bool $state): static
    {
        $this->insertOnPaste = $state;

        return $this;
    }

    public function getInsertOnPaste(): bool
    {
        return $this->insertOnPaste;
    }

    public function dataPlaceholder(string|Closure|null $state): static
    {
        $this->dataPlaceholder = $state;

        return $this;
    }

    public function getDataPlaceholder(): string|null
    {
        return $this->evaluate($this->dataPlaceholder);
    }

    public function dataHelperHtml(string|Closure|null $html): static
    {
        $this->dataHelperHtml = $html;

        return $this;
    }

    public function getDataHelperHtml(): string|null
    {
        return $this->evaluate($this->dataHelperHtml) ?? '';
    }

    public function dataInputRows(int $rows): static
    {
        $this->dataInputRows = $rows;

        return $this;
    }

    public function getDataInputRows(): int
    {
        return $this->dataInputRows;
    }

    public function validator(): ComponentValidator
    {
        return new ComponentValidator($this->getId());
    }
}
