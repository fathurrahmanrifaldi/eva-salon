<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === Role::Owner;
    }
}
