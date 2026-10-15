<?php

namespace App\Policies;

use App\Models\User;

/** Owner dan kasir aktif boleh mengelola booking. Tidak ada delete (agent.md bagian 4). */
class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user): bool
    {
        return $user->is_active;
    }

    public function delete(User $user): bool
    {
        return false;
    }
}
