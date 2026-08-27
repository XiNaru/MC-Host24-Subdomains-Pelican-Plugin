<?php

namespace XiNaaru\McHost24Subdomains;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Filament\Panel;
use Illuminate\Support\Facades\Http;
use Throwable;

class McHost24SubdomainsPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    private const API_BASE_URL = 'https://mc-host24.de/api/v1';

    public function getId(): string
    {
        return 'mchost24-subdomains';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(
            plugin_path($this->getId(), "src/Filament/$id/Resources"),
            "XiNaaru\\McHost24Subdomains\\Filament\\$id\\Resources"
        );

        $panel->discoverPages(
            plugin_path($this->getId(), "src/Filament/$id/Pages"),
            "XiNaaru\\McHost24Subdomains\\Filament\\$id\\Pages"
        );
    }

    public function boot(Panel $panel): void
    {
    }

    public function getSettingsFormData(): array
    {
        return [
            'api_token' => config('mchost24-subdomains.api_token', ''),
            'domain_id' => config('mchost24-subdomains.domain_id', ''),
            'domain' => config('mchost24-subdomains.domain', ''),
        ];
    }

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('api_token')
                ->label(__('mchost24-subdomains::strings.api_token'))
                ->password()
                ->revealable()
                ->live(debounce: 500)
                ->required()
                ->helperText(
                    __('mchost24-subdomains::strings.api_token_help')
                ),

            Placeholder::make('api_token_command')
                ->label(
                    __('mchost24-subdomains::strings.api_token_create')
                )
                ->content(
                    __('mchost24-subdomains::strings.api_token_command')
                ),

            Placeholder::make('api_token_tfa_help')
                ->label(
                    __('mchost24-subdomains::strings.tfa_title')
                )
                ->content(
                    __('mchost24-subdomains::strings.tfa_help')
                ),

            Select::make('domain_id')
                ->label(
                    __('mchost24-subdomains::strings.minecraft_domain')
                )
                ->placeholder(
                    __('mchost24-subdomains::strings.domain_placeholder')
                )
                ->options(function (Get $get): array {
                    $token = trim((string) $get('api_token'));

                    if ($token === '') {
                        return [];
                    }

                    try {
                        $response = Http::timeout(10)
                            ->acceptJson()
                            ->withHeaders([
                                'Authorization' => $token,
                            ])
                            ->get(self::API_BASE_URL . '/domain');

                        if (!$response->successful()) {
                            return [];
                        }

                        $domains = $response->json('data');

                        if (!is_array($domains)) {
                            return [];
                        }

                        $options = [];

                        foreach ($domains as $domain) {
                            if (
                                !isset($domain['id']) ||
                                !isset($domain['sld']) ||
                                !isset($domain['tld'])
                            ) {
                                continue;
                            }

                            $domainName = $domain['sld'] . '.' . $domain['tld'];

                            $options[(string) $domain['id']] = $domainName;
                        }

                        return $options;
                    } catch (Throwable) {
                        return [];
                    }
                })
                ->live()
                ->searchable()
                ->preload(false)
                ->required(),

            Placeholder::make('domain_help')
                ->label(
                    __('mchost24-subdomains::strings.minecraft_domain')
                )
                ->content(
                    __('mchost24-subdomains::strings.domain_help')
                ),
        ];
    }

    public function saveSettings(array $data): void
    {
        $apiToken = trim((string) ($data['api_token'] ?? ''));
        $domainId = (string) ($data['domain_id'] ?? '');

        $domainName = '';

        if ($apiToken !== '' && $domainId !== '') {
            try {
                $response = Http::timeout(10)
                    ->acceptJson()
                    ->withHeaders([
                        'Authorization' => $apiToken,
                    ])
                    ->get(self::API_BASE_URL . '/domain');

                if ($response->successful()) {
                    $domains = $response->json('data');

                    if (is_array($domains)) {
                        foreach ($domains as $domain) {
                            if ((string) ($domain['id'] ?? '') !== $domainId) {
                                continue;
                            }

                            if (
                                isset($domain['sld']) &&
                                isset($domain['tld'])
                            ) {
                                $domainName = $domain['sld'] . '.' . $domain['tld'];
                            }

                            break;
                        }
                    }
                }
            } catch (Throwable) {
                $domainName = '';
            }
        }

        $this->writeToEnvironment([
            'MCHOST24_SUBDOMAINS_API_TOKEN' => $apiToken,
            'MCHOST24_SUBDOMAINS_DOMAIN_ID' => $domainId,
            'MCHOST24_SUBDOMAINS_DOMAIN' => $domainName,
        ]);

        Notification::make()
            ->title(
                __('mchost24-subdomains::strings.settings_saved')
            )
            ->success()
            ->send();
    }
}