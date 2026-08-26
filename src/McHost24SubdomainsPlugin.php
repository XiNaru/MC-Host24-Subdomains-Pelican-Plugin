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

    private function isGerman(): bool
    {
        return app()->getLocale() === 'de';
    }

    private function text(string $key): string
    {
        $de = [
            'api_token' => 'MC-HOST24 API Token',
            'api_token_help' => 'Trage hier deinen MC-HOST24 API-Token ein.',
            'api_token_create' => 'API-Token erstellen',
            'api_token_command' => <<<'TEXT'
curl -X POST "https://mc-host24.de/api/v1/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "DEIN_BENUTZERNAME",
    "password": "DEIN_PASSWORT",
    "tfa": 123456
  }'
TEXT,
            'tfa_title' => 'Hinweis zu tfa',
            'tfa_help' => 'Das Feld "tfa" ist optional und wird nur benötigt, wenn für dein MC-HOST24-Konto die Zwei-Faktor-Authentifizierung (2FA) aktiviert ist. In diesem Fall wird der aktuelle 6-stellige 2FA-Code eingetragen.',
            'minecraft_domain' => 'Minecraft-Domain',
            'domain_placeholder' => 'Zuerst einen gültigen API-Token eingeben',
            'domain_help' => 'Die verfügbaren Domains werden automatisch anhand deines MC-HOST24 API-Tokens geladen. Die interne Domain-ID wird automatisch verwendet und muss nicht manuell eingetragen werden.',
            'subdomain_limit' => 'Subdomains pro Server',
            'settings_saved' => 'Einstellungen gespeichert',
        ];

        $en = [
            'api_token' => 'MC-HOST24 API Token',
            'api_token_help' => 'Enter your MC-HOST24 API token here.',
            'api_token_create' => 'Create API token',
            'api_token_command' => <<<'TEXT'
curl -X POST "https://mc-host24.de/api/v1/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD",
    "tfa": 123456
  }'
TEXT,
            'tfa_title' => 'tfa notice',
            'tfa_help' => 'The "tfa" field is optional and is only required if two-factor authentication (2FA) is enabled for your MC-HOST24 account. In that case, enter the current 6-digit 2FA code.',
            'minecraft_domain' => 'Minecraft Domain',
            'domain_placeholder' => 'Enter a valid API token first',
            'domain_help' => 'Available domains are loaded automatically using your MC-HOST24 API token. The internal domain ID is handled automatically and does not need to be entered manually.',
            'subdomain_limit' => 'Subdomains per Server',
            'settings_saved' => 'Settings saved',
        ];

        return ($this->isGerman() ? $de : $en)[$key];
    }

    public function getSettingsFormData(): array
    {
        return [
            'api_token' => config('mchost24-subdomains.api_token', ''),
            'domain_id' => config('mchost24-subdomains.domain_id', ''),
            'domain' => config('mchost24-subdomains.domain', ''),
            'subdomain_limit' => config('mchost24-subdomains.subdomain_limit', 1),
        ];
    }

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('api_token')
                ->label($this->text('api_token'))
                ->password()
                ->revealable()
                ->live(debounce: 500)
                ->required()
                ->helperText($this->text('api_token_help')),

            Placeholder::make('api_token_command')
                ->label($this->text('api_token_create'))
                ->content($this->text('api_token_command')),

            Placeholder::make('api_token_tfa_help')
                ->label($this->text('tfa_title'))
                ->content($this->text('tfa_help')),

            Select::make('domain_id')
                ->label($this->text('minecraft_domain'))
                ->placeholder($this->text('domain_placeholder'))
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
                ->label($this->text('minecraft_domain'))
                ->content($this->text('domain_help')),

            TextInput::make('subdomain_limit')
                ->label($this->text('subdomain_limit'))
                ->numeric()
                ->minValue(0)
                ->default(1)
                ->required(),
        ];
    }

    public function saveSettings(array $data): void
    {
        $apiToken = trim((string) ($data['api_token'] ?? ''));
        $domainId = (string) ($data['domain_id'] ?? '');
        $subdomainLimit = (int) ($data['subdomain_limit'] ?? 1);

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
            'MCHOST24_SUBDOMAINS_LIMIT' => $subdomainLimit,
        ]);

        Notification::make()
            ->title($this->text('settings_saved'))
            ->success()
            ->send();
    }
}