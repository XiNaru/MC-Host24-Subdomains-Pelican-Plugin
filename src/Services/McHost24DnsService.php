<?php

namespace XiNaaru\McHost24Subdomains\Services;

use App\Models\Server;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use XiNaaru\McHost24Subdomains\Models\McHost24Subdomain;

class McHost24DnsService
{
    private const API_BASE_URL = 'https://mc-host24.de/api/v1';

    public function getApiToken(): string
    {
        $token = trim(
            (string) config('mchost24-subdomains.api_token')
        );

        if ($token === '') {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.api_token_not_configured')
            );
        }

        return $token;
    }

    public function getConfiguredDomains(): array
    {
        $domains = config('mchost24-subdomains.domains', []);

        if (!is_array($domains)) {
            return [];
        }

        $result = [];

        foreach ($domains as $domain) {
            if (
                !is_array($domain) ||
                !isset($domain['id']) ||
                !isset($domain['domain'])
            ) {
                continue;
            }

            $domainId = (int) $domain['id'];
            $domainName = trim(
                (string) $domain['domain']
            );

            if ($domainId <= 0 || $domainName === '') {
                continue;
            }

            $result[] = [
                'id' => $domainId,
                'domain' => rtrim($domainName, '.'),
            ];
        }

        /*
         * Rückwärtskompatibilität mit der alten Einzel-Domain-Konfiguration.
         */
        if ($result === []) {
            $legacyDomainId = (int) config(
                'mchost24-subdomains.domain_id'
            );

            $legacyDomain = trim(
                (string) config('mchost24-subdomains.domain')
            );

            if (
                $legacyDomainId > 0 &&
                $legacyDomain !== ''
            ) {
                $result[] = [
                    'id' => $legacyDomainId,
                    'domain' => rtrim($legacyDomain, '.'),
                ];
            }
        }

        return $result;
    }

    public function getConfiguredDomainOptions(): array
    {
        $options = [];

        foreach ($this->getConfiguredDomains() as $domain) {
            $options[(string) $domain['id']] = $domain['domain'];
        }

        return $options;
    }

    public function getDomain(int $domainId): array
    {
        foreach ($this->getConfiguredDomains() as $domain) {
            if ((int) $domain['id'] === $domainId) {
                return $domain;
            }
        }

        throw new RuntimeException(
            __('mchost24-subdomains::strings.domain_not_configured')
        );
    }

    public function getDomainId(): int
    {
        $domainId = (int) config(
            'mchost24-subdomains.domain_id'
        );

        if ($domainId <= 0) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.domain_not_selected')
            );
        }

        return $domainId;
    }

    public function getDomainName(): string
    {
        $domain = trim(
            (string) config('mchost24-subdomains.domain')
        );

        if ($domain === '') {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.domain_not_configured')
            );
        }

        return rtrim($domain, '.');
    }

    public function create(
        Server $server,
        string $subdomain,
        int $domainId
    ): McHost24Subdomain {
        $subdomain = strtolower(trim($subdomain));

        $this->validateSubdomain($subdomain);

        $domainData = $this->getDomain($domainId);

        $domain = $domainData['domain'];

        if (
            McHost24Subdomain::query()
                ->where('domain_id', $domainId)
                ->where('subdomain', $subdomain)
                ->exists()
        ) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.subdomain_already_used')
            );
        }

        if (
            McHost24Subdomain::query()
                ->where('server_id', $server->id)
                ->exists()
        ) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.already_exists')
            );
        }

        $allocation = $server->allocation;

        if ($allocation === null) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.allocation_not_found')
            );
        }

        $ip = trim((string) $allocation->ip);
        $port = (int) $allocation->port;

        if ($ip === '') {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.server_ip_not_found')
            );
        }

        if ($port <= 0 || $port > 65535) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.invalid_server_port')
            );
        }

        /*
         * Der interne Hostname bleibt weiterhin pro Server eindeutig.
         */
        $targetHost = 'mc-' . $server->id . '.' . $domain;

        $fqdn = $subdomain . '.' . $domain;

        $addressRecord = $this->createAddressRecord(
            $domainId,
            $this->buildAddressSld($server),
            $ip
        );

        try {
            $srvRecord = $this->createSrvRecord(
                $domainId,
                $subdomain,
                $port,
                $targetHost
            );
        } catch (\Throwable $exception) {
            $this->deleteDnsRecord(
                $domainId,
                (int) $addressRecord['id']
            );

            throw $exception;
        }

        return McHost24Subdomain::create([
            'server_id' => $server->id,
            'domain_id' => $domainId,
            'domain' => $domain,
            'subdomain' => $subdomain,
            'fqdn' => $fqdn,
            'a_record_id' => (int) $addressRecord['id'],
            'srv_record_id' => (int) $srvRecord['id'],
            'target_host' => $targetHost,
            'target_ip' => $ip,
            'target_port' => $port,
        ]);
    }

    public function delete(McHost24Subdomain $record): void
    {
        $domainId = (int) $record->domain_id;

        /*
         * Rückwärtskompatibilität für alte Datensätze.
         */
        if ($domainId <= 0) {
            $domainId = $this->getDomainId();
        }

        if ($record->srv_record_id !== null) {
            $this->deleteDnsRecord(
                $domainId,
                (int) $record->srv_record_id
            );
        }

        if ($record->a_record_id !== null) {
            $this->deleteDnsRecord(
                $domainId,
                (int) $record->a_record_id
            );
        }

        $record->delete();
    }

    private function buildAddressSld(Server $server): string
    {
        return 'mc-' . $server->id;
    }

    private function createAddressRecord(
        int $domainId,
        string $sld,
        string $ip
    ): array {
        $type = str_contains($ip, ':')
            ? 'AAAA'
            : 'A';

        $response = $this->request()
            ->post(
                self::API_BASE_URL .
                '/domain/' .
                $domainId .
                '/dns',
                [
                    'sld' => $sld,
                    'type' => $type,
                    'target' => $ip,
                ]
            );

        $this->ensureSuccessful(
            $response,
            __('mchost24-subdomains::strings.address_record_failed')
        );

        $data = $response->json('data');

        if (!is_array($data) || !isset($data['id'])) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.address_record_id_missing')
            );
        }

        return $data;
    }

    private function createSrvRecord(
        int $domainId,
        string $subdomain,
        int $port,
        string $targetHost
    ): array {
        $response = $this->request()
            ->post(
                self::API_BASE_URL .
                '/domain/' .
                $domainId .
                '/dns',
                [
                    'sld' => '_minecraft._tcp.' . $subdomain,
                    'type' => 'SRV',
                    'target' => '10 0 ' .
                        $port .
                        ' ' .
                        rtrim($targetHost, '.') .
                        '.',
                ]
            );

        $this->ensureSuccessful(
            $response,
            __('mchost24-subdomains::strings.srv_record_failed')
        );

        $data = $response->json('data');

        if (!is_array($data) || !isset($data['id'])) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.srv_record_id_missing')
            );
        }

        return $data;
    }

    private function deleteDnsRecord(
        int $domainId,
        int $recordId
    ): void {
        $response = $this->request()
            ->delete(
                self::API_BASE_URL .
                '/domain/' .
                $domainId .
                '/dns/' .
                $recordId
            );

        /*
         * HTTP 404 bedeutet beim Löschen, dass der DNS-Eintrag
         * bereits nicht mehr vorhanden ist. In diesem Fall soll
         * der lokale Plugin-Datensatz trotzdem gelöscht werden.
         */
        if ($response->status() === 404) {
            return;
        }

        $this->ensureSuccessful(
            $response,
            __('mchost24-subdomains::strings.dns_record_delete_failed')
        );
    }

    private function request()
    {
        return Http::timeout(15)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => $this->getApiToken(),
            ]);
    }

    private function ensureSuccessful(
        Response $response,
        string $message
    ): void {
        if ($response->successful()) {
            return;
        }

        $apiMessage = $response->json('message');

        if (
            is_string($apiMessage) &&
            trim($apiMessage) !== ''
        ) {
            $message .= ' ' . trim($apiMessage);
        }

        throw new RuntimeException(
            $message .
            ' HTTP ' .
            $response->status() .
            '.'
        );
    }

    private function validateSubdomain(
        string $subdomain
    ): void {
        if ($subdomain === '') {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.subdomain_required')
            );
        }

        if (strlen($subdomain) > 63) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.subdomain_too_long')
            );
        }

        if (
            !preg_match(
                '/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/',
                $subdomain
            )
        ) {
            throw new RuntimeException(
                __('mchost24-subdomains::strings.subdomain_invalid')
            );
        }
    }
}