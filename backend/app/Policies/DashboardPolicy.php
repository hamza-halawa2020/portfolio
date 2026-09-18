<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DashboardPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function view(User $user, Model $model): bool
    {
        return $user->isAdministrator();
    }

    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function update(User $user, Model $model): bool
    {
        return $user->isAdministrator();
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->isAdministrator();
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->isAdministrator();
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->isAdministrator();
    }
}
