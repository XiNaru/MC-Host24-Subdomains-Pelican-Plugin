<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mchost24_subdomains', function (Blueprint $table) {
            $table->unsignedBigInteger('domain_id')
                ->nullable()
                ->after('server_id');

            $table->string('domain', 255)
                ->nullable()
                ->after('fqdn');
        });

        Schema::table('mchost24_subdomains', function (Blueprint $table) {
            $table->dropUnique('mchost24_subdomains_subdomain_unique');

            $table->unique(
                ['domain_id', 'subdomain'],
                'mchost24_subdomains_domain_subdomain_unique'
            );
        });

        /*
         * Bereits vorhandene Einträge werden, sofern noch vorhanden,
         * mit der bisherigen Einzel-Domain-Konfiguration verknüpft.
         */
        $legacyDomainId = (int) config(
            'mchost24-subdomains.domain_id'
        );

        $legacyDomain = trim(
            (string) config('mchost24-subdomains.domain')
        );

        if ($legacyDomainId > 0 && $legacyDomain !== '') {
            DB::table('mchost24_subdomains')
                ->whereNull('domain_id')
                ->update([
                    'domain_id' => $legacyDomainId,
                    'domain' => $legacyDomain,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('mchost24_subdomains', function (Blueprint $table) {
            $table->dropUnique(
                'mchost24_subdomains_domain_subdomain_unique'
            );

            $table->unique(
                'subdomain',
                'mchost24_subdomains_subdomain_unique'
            );

            $table->dropColumn([
                'domain_id',
                'domain',
            ]);
        });
    }
};