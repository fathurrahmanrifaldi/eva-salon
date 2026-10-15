<?php

namespace App\Policies;

use App\Models\User;

/** Kasir dan owner boleh melihat dan mengedit pelanggan; tidak ada delete. */
class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user): bool
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
