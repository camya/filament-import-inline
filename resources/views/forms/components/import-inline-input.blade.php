<x-forms::field-wrapper
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
    class="filament-paste-input-wrapper"
>
    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            stateInput: null,
            stateCurrent: null,
            processing: false,
            pastedDataVisible: false,
            placeholderText: '{{ $getPlaceholder() }}',
            update: function(data, updateData = true) {

                this.stateCurrent = data;

                if(updateData) {

                    this.processing = true;
                    this.state = data;

                    $nextTick(() => {

                        $refs.stateInputField.value = '';
                        this.stateInput = '';

                        $refs.stateInputField.setAttribute('placeholder', '');

                        setTimeout(() => {
                            $refs.stateInputField.setAttribute('placeholder', this.placeholderText);
                            this.processing = false;
                         }, 500)

                    });
                }

                this.showPastedData();

            },
            showPastedData: function() {
                this.pastedDataVisible = true;
            }
        }"

    >

        <div
            class="flex space-x-2 mb-4"
            x-show="!pastedDataVisible"
        >

            <textarea
                x-model="stateInput"
                @if($getInsertOnPaste())
                    x-on:paste="if(!stateInput) { update($event.clipboardData.getData('Text')) }"
                @endif
                x-ref="stateInputField"
                rows="{{ $getDataInputRows() }}"

                    {{ $getExtraInputAttributeBag()->class(['
                            bg-gray-100 placeholder-gray-500
                            block w-full transition duration-75 rounded-lg shadow-sm
                            focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600
                            disabled:opacity-70
                        ',
                        'dark:bg-gray-700 dark:text-white' => config('forms.dark_mode'),
                        'border-gray-300' => !$errors->has($getStatePath()),
                        'border border-dashed border-gray-400' => $getInsertOnPaste(),
                        'dark:border-gray-600' => !$errors->has($getStatePath()) && config('forms.dark_mode'),
                        'border-danger-600 ring-danger-600' => $errors->has($getStatePath())]
                    ) }}

                {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}

                ></textarea>

            <a
                x-show="stateInput"
                x-on:click.prevent="update(stateInput)"
                href="#"
                role="button"
                class="
                    select-none
                    filament-button filament-button-size-md inline-flex items-center justify-center py-2.5 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-page-button-action whitespace-nowrap
                "
            >
                {{ trans('filament-import-inline::package.import_inline_action_import_manually') }}
            </a>

        </div>

        <div>
            <div>


                <div

                    x-show="pastedDataVisible"
                    class="relative w-full mb-4"
                >

                    <textarea
                        placeholder="{{ $getDataPlaceholder() }}"
                        {{--                        x-text="state"--}}
                        x-text="stateCurrent"
                        x-ref="fieldOptionTextarea"
                        class="
                            filament-forms-textarea-component font-mono
                            w-full text-xs border-gray-300 block w-full transition duration-75 rounded-lg shadow-sm
                            focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500
                            disabled:opacity-70
                            dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-primary-500
                        "
                        rows="9"
                    ></textarea>

                    <svg
                        x-show="processing"
                        class="absolute right-3 top-3 z-10 animate-spin h-5 w-5 text-gray-400"
                        :class="processing ? 'placeholder:pl-6' : ''"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>

                </div>


                @php
                    $componentErrors  = \Camya\Filament\ComponentValidator::componentErrors(
                        componentId: $getId(),
                        errors: $errors->toArray()
                    );
                    $statusMessageHtml = $getStatusMessageHtml();
                @endphp

                <div class="flex justify-between items-start text-sm space-x-2">


                    <div>

                        @if($componentErrors || $statusMessageHtml)

                            <div class="mb-4">

                                @if($componentErrors)
                                    <ul class="ml-1 list-inside list-disc text-sm text-danger-600">
                                        @foreach($componentErrors as $errorKey => $error)
                                            <li>{{ Arr::join($error,',') }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if($statusMessageHtml)
                                    <div class="text-green-600">
                                        {!! Str::sanitizeHtml($statusMessageHtml) !!}
                                    </div>
                                @endif

                            </div>

                        @endif

                    </div>


                    <div class="flex items-center text-sm space-x-4">

                        <a
                            href="#"
                            role="button"
                            x-on:click.prevent="update(stateInput, false)"
                            class="
                        select-none
                        filament-link cursor-pointer text-sm text-primary-600
                        inline-flex items-center justify-center space-x-1
                        hover:text-primary-500 hover:underline
                        dark:text-primary-500 dark:hover:text-primary-400
                    "
                        >
                    <span
                        x-show="!pastedDataVisible"
                    >{{ trans('filament-import-inline::package.import_inline_action_options') }}</span>

                        </a>

                    </div>

                </div>

                <div
                    x-show="pastedDataVisible"
                    class="flex justify-between items-center text-sm space-x-4"
                >

                    <a
                        href="#"
                        role="button"
                        x-on:click.prevent="update($refs.fieldOptionTextarea.value)"
                        class="
                            select-none
                            filament-button filament-button-size-md inline-flex items-center justify-center py-2.5 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-page-button-action whitespace-nowrap
                        "
                    >{{ trans('filament-import-inline::package.import_inline_action_import_manually') }}</a>

                    <a
                        href="#"
                        role="button"
                        x-on:click.prevent="pastedDataVisible = !pastedDataVisible"
                        class="
                            select-none
                            filament-link cursor-pointer text-primary-600
                            inline-flex items-center justify-center space-x-1
                            hover:text-primary-500 hover:underline
                            dark:text-primary-500 dark:hover:text-primary-400
                        "
                    >

                        <span>{{ trans('filament-import-inline::package.import_inline_action_close') }}</span>

                    </a>

                </div>

            </div>

            @if($getDataHelperHtml() || $getDataHelperLink())
                <div
                    x-show="pastedDataVisible"
                    class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 "
                >

                    @if($getDataHelperLink())

                        <a
                            href="{{ $getDataHelperLink() }}"
                            target="_blank"
                            class="
                                select-none text-sm
                                ml-2 float-right block
                                filament-link cursor-pointer text-primary-600
                                inline-flex items-center justify-center space-x-1
                                hover:text-primary-500 hover:underline
                                dark:text-primary-500 dark:hover:text-primary-400
                            "
                        >
                            @if($getDataHelperLinkLabel())
                                <span>{{ $getDataHelperLinkLabel() }}</span>
                            @endif
                            <x-heroicon-o-external-link class="w-4 h-4"/>

                        </a>

                    @endif

                    <div class="text-sm leading-4  text-gray-400 dark:text-gray-400">
                        {!! Str::sanitizeHtml($getDataHelperHtml()) !!}
                    </div>

                </div>
            @endif

        </div>

    </div>

    @if (($suffixAction = $getSuffixAction()) && (! $suffixAction->isHidden()))
        {{ $suffixAction }}
    @endif

</x-forms::field-wrapper>
