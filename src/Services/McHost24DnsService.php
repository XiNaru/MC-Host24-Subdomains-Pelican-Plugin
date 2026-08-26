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
        $token = trim((string) config('mchost24-subdomains.api_token'));

        if ($token === '') {
            throw new RuntimeException('MC-HOST24 API-Token ist nicht konfiguriert.');
        }

        return $token;
    }

    public function getDomainId(): int
    {
        $domainId = (int) config('mchost24-subdomains.domain_id');

        if ($domainId <= 0) {
            throw new RuntimeException('Keine MC-HOST24-Domain wurde ausgewählt.');
        }

        return $domainId;
    }

    public function getDomain(): string
    {
        $domain = trim((string) config('mchost24-subdomains.domain'));

        if ($domain === '') {
            throw new RuntimeException('Keine MC-HOST24-Domain wurde konfiguriert.');
        }

        return rtrim($domain, '.');
    }

    public function create(Server $server, string $subdomain): McHost24Subdomain
    {
        $subdomain = strtolower(trim($subdomain));

        $this->validateSubdomain($subdomain);

        if (McHost24Subdomain::query()->where('subdomain', $subdomain)->exists()) {
            throw new RuntimeException('Diese Subdomain wird bereits verwendet.');
        }

        if (McHost24Subdomain::query()->where('server_id', $server->id)->exists()) {
            throw new RuntimeException('Für diesen Server existiert bereits eine Subdomain.');
        }

        $allocation = $server->allocation;

        if ($allocation === null) {
            throw new RuntimeException('Für diesen Server wurde keine Allocation gefunden.');
        }

        $ip = trim((string) $allocation->ip);
        $port = (int) $allocation->port;

        if ($ip === '') {
            throw new RuntimeException('Die Server-IP konnte nicht ermittelt werden.');
        }

        if ($port <= 0 || $port > 65535) {
            throw new RuntimeException('Der Server-Port ist ungültig.');
        }

        $domain = $this->getDomain();

        /*
         * Der A/AAAA-Record verwendet einen internen Hostnamen.
         * Dadurch bleibt der vom Benutzer gewünschte Name ausschließlich
         * die eigentliche Minecraft-Adresse.
         */
        $targetHost = 'mc-' . $server->id . '.' . $domain;

        $fqdn = $subdomain . '.' . $domain;

        $addressRecord = $this->createAddressRecord(
            $this->getDomainId(),
            $this->buildAddressSld($server),
            $ip
        );

        try {
            $srvRecord = $this->createSrvRecord(
                $this->getDomainId(),
                $subdomain,
                $port,
                $targetHost
            );
        } catch (\Throwable $exception) {
            $this->deleteDnsRecord(
                $this->getDomainId(),
                (int) $addressRecord['id']
            );

            throw $exception;
        }

        return McHost24Subdomain::create([
            'server_id' => $server->id,
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
        $domainId = $this->getDomainId();

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
        $type = str_contains($ip, ':') ? 'AAAA' : 'A';

        $response = $this->request()
            ->post(self::API_BASE_URL . '/domain/' . $domainId . '/dns', [
                'sld' => $sld,
                'type' => $type,
                'target' => $ip,
            ]);

        $this->ensureSuccessful($response, 'A/AAAA-Record konnte nicht erstellt werden.');

        $data = $response->json('data');

        if (!is_array($data) || !isset($data['id'])) {
            throw new RuntimeException(
                'MC-HOST24 hat keine gültige Record-ID für den A/AAAA-Record zurückgegeben.'
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
            ->post(self::API_BASE_URL . '/domain/' . $domainId . '/dns', [
                'sld' => '_minecraft._tcp.' . $subdomain,
                'type' => 'SRV',
                'target' => '10 0 ' . $port . ' ' . rtrim($targetHost, '.') . '.',
            ]);

        $this->ensureSuccessful($response, 'SRV-Record konnte nicht erstellt werden.');

        $data = $response->json('data');

        if (!is_array($data) || !isset($data['id'])) {
            throw new RuntimeException(
                'MC-HOST24 hat keine gültige Record-ID für den SRV-Record zurückgegeben.'
            );
        }

        return $data;
    }

    private function deleteDnsRecord(int $domainId, int $recordId): void
    {
        $response = $this->request()
            ->delete(
                self::API_BASE_URL .
                '/domain/' .
                $domainId .
                '/dns/' .
                $recordId
            );

        $this->ensureSuccessful(
            $response,
            'DNS-Record konnte nicht gelöscht werden.'
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

        if (is_string($apiMessage) && trim($apiMessage) !== '') {
            $message .= ' ' . trim($apiMessage);
        }

        throw new RuntimeException(
            $message . ' HTTP ' . $response->status() . '.'
        );
    }

    private function validateSubdomain(string $subdomain): void
    {
        if ($subdomain === '') {
            throw new RuntimeException('Bitte gib eine Subdomain ein.');
        }

        if (strlen($subdomain) > 63) {
            throw new RuntimeException(
                'Die Subdomain darf maximal 63 Zeichen lang sein.'
            );
        }

        if (!preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/', $subdomain)) {
            throw new RuntimeException(
                'Die Subdomain darf nur Kleinbuchstaben, Zahlen und Bindestriche enthalten und darf nicht mit einem Bindestrich beginnen oder enden.'
            );
        }
    }
}