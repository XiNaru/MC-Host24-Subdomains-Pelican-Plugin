<?php

namespace XiNaaru\McHost24Subdomains\Livewire\Server;

use App\Enums\SubuserPermission;
use App\Models\Server;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Throwable;
use XiNaaru\McHost24Subdomains\Models\McHost24Subdomain;
use XiNaaru\McHost24Subdomains\Services\McHost24DnsService;

class SubdomainManager extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public ?Server $server = null;

    public function mount(): void
    {
        $server = Filament::getTenant();

        abort_unless($server instanceof Server, 404);

        $this->server = $server;

        $record = McHost24Subdomain::query()
            ->where('server_id', $server->id)
            ->first();

        $this->form->fill([
            'subdomain' => $record?->subdomain,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subdomain')
                    ->label(__('mchost24-subdomains::strings.field'))
                    ->placeholder('survival')
                    ->suffix(function (): string {
                        return '.' . rtrim(
                            (string) config('mchost24-subdomains.domain'),
                            '.'
                        );
                    })
                    ->required()
                    ->maxLength(63)
                    ->regex('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/')
                    ->helperText(
                        __('mchost24-subdomains::strings.helper')
                    ),
            ])
            ->statePath('data');
    }

    public function canManage(): bool
    {
        if (!$this->server || !user()) {
            return false;
        }

        return (int) $this->server->user_id === (int) user()->id
            || user()->can(
                SubuserPermission::SettingsRename,
                $this->server
            );
    }

    public function getCurrentRecord(): ?McHost24Subdomain
    {
        if (!$this->server) {
            return null;
        }

        return McHost24Subdomain::query()
            ->where('server_id', $this->server->id)
            ->first();
    }

    public function save(): void
    {
        abort_unless($this->canManage(), 403);

        if (!$this->server) {
            abort(404);
        }

        if (
            McHost24Subdomain::query()
                ->where('server_id', $this->server->id)
                ->exists()
        ) {
            Notification::make()
                ->title(
                    __('mchost24-subdomains::strings.already_exists')
                )
                ->warning()
                ->send();

            return;
        }

        try {
            $data = $this->form->getState();

            app(McHost24DnsService::class)->create(
                $this->server,
                (string) $data['subdomain']
            );

            Notification::make()
                ->title(
                    __('mchost24-subdomains::strings.created_title')
                )
                ->body(
                    __('mchost24-subdomains::strings.created_body')
                )
                ->success()
                ->send();

            $this->form->fill([
                'subdomain' => '',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title(
                    __('mchost24-subdomains::strings.create_failed_title')
                )
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function delete(): void
    {
        abort_unless($this->canManage(), 403);

        $record = $this->getCurrentRecord();

        if (!$record) {
            return;
        }

        try {
            app(McHost24DnsService::class)->delete($record);

            Notification::make()
                ->title(
                    __('mchost24-subdomains::strings.deleted_title')
                )
                ->success()
                ->send();

            $this->form->fill([
                'subdomain' => '',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title(
                    __('mchost24-subdomains::strings.delete_failed_title')
                )
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render(): View
    {
        return view(
            'mchost24-subdomains::livewire.server.subdomain-manager'
        );
    }
}