<?php

namespace App\Policies;

use App\Models\User;

/** Hanya owner. Dipakai untuk layanan, kategori, cuti, profil salon, galeri, dan akun. */
class OwnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOwner();
    }

    public function view(User $user): bool
    {
        return $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isOwner();
    }

    public function update(User $user): bool
    {
        return $user->isOwner();
    }

    public function delete(User $user): bool
    {
        return $user->isOwner();
    }
}
