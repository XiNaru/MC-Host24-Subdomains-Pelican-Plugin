@php
    $record = $this->getCurrentRecord();
@endphp

<div
    id="mchost24-subdomains-container"
    x-data
    wire:ignore.self
    style="margin: 0.25rem 1.5rem 2rem;"
>
    <fieldset
        class="fi-sc-fieldset fi-fieldset"
        style="
            width: fit-content;
            max-width: calc(100% - 3rem);
            min-width: 0;
            margin: 0;
            padding: 0;
        "
    >
        <legend
            style="
                padding: 0 0.5rem;
                margin-left: 1rem;
            "
        >
            {{ __('mchost24-subdomains::strings.title') }}
        </legend>

        <div
            style="
                padding: 0.5rem 2rem 0.75rem;
                margin: 0;
            "
        >
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                {{ __('mchost24-subdomains::strings.description') }}
            </p>

            @if ($record)
                <div class="space-y-4">
                    <div>
                        <div class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ __('mchost24-subdomains::strings.minecraft_address') }}
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <code class="rounded-lg bg-gray-100 px-3 py-2 text-sm text-gray-950 dark:bg-white/10 dark:text-white">
                                {{ $record->fqdn }}
                            </code>

                            <button
                                type="button"
                                wire:click="delete"
                                wire:confirm="{{ __('mchost24-subdomains::strings.delete_confirmation') }}"
                                class="fi-btn fi-btn-size-md relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold outline-none transition duration-75 focus:ring-2"
                            >
                                {{ __('mchost24-subdomains::strings.delete') }}
                            </button>
                        </div>
                    </div>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('mchost24-subdomains::strings.port') }}: {{ $record->target_port }}
                    </div>
                </div>
            @else
                <div style="width: 24rem; max-width: 100%;">
                    <form
                        id="mchost24-create-subdomain-form"
                        wire:submit="save"
                    >
                        {{ $this->form }}

                        <div class="mt-4">
                            <x-filament::button
                                type="submit"
                                :disabled="! $this->canManage()"
                            >
                                {{ __('mchost24-subdomains::strings.create') }}
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </fieldset>

    <script>
        (() => {
            if (!window.mchost24ReloadHandlerInstalled) {
                window.mchost24ReloadHandlerInstalled = true;

                document.addEventListener(
                    'submit',
                    (event) => {
                        const form = event.target;

                        if (
                            !(form instanceof HTMLFormElement) ||
                            form.id !== 'mchost24-create-subdomain-form'
                        ) {
                            return;
                        }

                        window.setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    },
                    true
                );
            }

            const moveSubdomainBlock = () => {
                const block = document.getElementById(
                    'mchost24-subdomains-container'
                );

                if (!block) {
                    return;
                }

                const sections = document.querySelectorAll('.fi-section');

                if (!sections.length) {
                    return;
                }

                const serverInformationSection = sections[0];

                if (!serverInformationSection.contains(block)) {
                    const sectionContent =
                        serverInformationSection.querySelector(
                            '.fi-section-content-ctn'
                        );

                    if (sectionContent) {
                        sectionContent.appendChild(block);
                    } else {
                        serverInformationSection.appendChild(block);
                    }
                }
            };

            requestAnimationFrame(moveSubdomainBlock);

            if (window.Livewire) {
                Livewire.hook('morph.updated', () => {
                    requestAnimationFrame(moveSubdomainBlock);
                });
            }
        })();
    </script>
</div>
