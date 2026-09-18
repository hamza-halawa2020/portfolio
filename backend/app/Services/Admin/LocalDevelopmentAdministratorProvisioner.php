<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class LocalDevelopmentAdministratorProvisioner
{
    public function provisionFromConfig(): ?User
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Local development administrator provisioning is only allowed in local or testing environments.');
        }

        $name = trim((string) config('portfolio.local_admin.name'));
        $email = Str::lower(trim((string) config('portfolio.local_admin.email')));
        $password = (string) config('portfolio.local_admin.password');

        if ($name === '' || $email === '' || $password === '') {
            return null;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('LOCAL_DEV_ADMIN_EMAIL must be a valid email address.');
        }

        $existing = User::query()->where('email', $email)->first();

        if ($existing !== null) {
            if (! $existing->isAdministrator()) {
                throw new RuntimeException('LOCAL_DEV_ADMIN_EMAIL already belongs to a non-administrator account.');
            }

            return $existing;
        }

        $user = new User;
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'is_admin' => true,
            'admin_granted_at' => now(),
            'admin_granted_by' => null,
        ])->save();

        return $user;
    }
}
