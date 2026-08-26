<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mchost24_subdomains', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('server_id');

            $table->string('subdomain', 63);
            $table->string('fqdn', 255);

            $table->unsignedBigInteger('a_record_id')->nullable();
            $table->unsignedBigInteger('srv_record_id')->nullable();

            $table->string('target_host', 255);
            $table->string('target_ip', 255);
            $table->unsignedInteger('target_port');

            $table->timestamps();

            $table->unique('server_id');
            $table->unique('subdomain');

            $table->foreign('server_id')
                ->references('id')
                ->on('servers')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mchost24_subdomains');
    }
};