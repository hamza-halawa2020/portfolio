<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProvisionOwnerAdministrator extends Command
{
    protected $signature = 'portfolio:provision-owner-admin';

    protected $description = 'Interactively create or grant explicit dashboard administrator access to the owner account.';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Owner name'));
        $email = Str::lower(trim((string) $this->ask('Owner email')));

        if ($name === '' || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->components->error('A valid owner name and email address are required.');

            return self::FAILURE;
        }

        $password = $this->secret('Owner password');
        $confirmation = $this->secret('Confirm owner password');

        if ($password !== $confirmation) {
            $this->components->error('Password confirmation does not match.');

            return self::FAILURE;
        }

        try {
            validator(
                ['password' => $password],
                ['password' => ['required', 'string', 'min:12']]
            )->validate();
        } catch (ValidationException) {
            $this->components->error('The owner password must be at least 12 characters.');

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => $name,
            'password' => Hash::make($password),
            'is_admin' => true,
            'admin_granted_at' => now(),
            'admin_granted_by' => null,
        ])->save();

        $this->components->info('Owner administrator account provisioned.');

        return self::SUCCESS;
    }
}
