<?php

namespace App\Models\SiteVisit;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected  $table = 'site_visit';
    protected  $fillable = [
        'ip',
        'city',
        'user_agent',
        'site',
    ];
    protected  $casts = [
        'ip' => 'string',
        'city' => 'string',
        'user_agent' => 'string',
        'site' => 'string',
    ];
    public $timestamps = true;
}
