<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Tests\Support;

use Illuminate\Foundation\Auth\User as Authenticatable;

class DashboardUser extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
