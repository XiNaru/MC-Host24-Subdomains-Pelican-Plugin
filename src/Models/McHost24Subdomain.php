<?php

namespace XiNaaru\McHost24Subdomains\Models;

use App\Models\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class McHost24Subdomain extends Model
{
    protected $table = 'mchost24_subdomains';

    protected $fillable = [
        'server_id',
        'subdomain',
        'fqdn',
        'a_record_id',
        'srv_record_id',
        'target_host',
        'target_ip',
        'target_port',
    ];

    protected $casts = [
        'a_record_id' => 'integer',
        'srv_record_id' => 'integer',
        'target_port' => 'integer',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}