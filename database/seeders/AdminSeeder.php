<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('gpsen.admin_password');

        if (empty($password)) {
            $this->command?->error('ADMIN_PASSWORD est vide : définis-le dans le fichier .env.');
            return;
        }

        $admin = User::firstOrNew(['email' => config('gpsen.admin_email')]);
        $admin->name = 'Administrateur GPsen';
        $admin->phone = '+221770000000';
        $admin->password = $password;
        $admin->role = Role::admin;
        $admin->status = 'actif';
        $admin->locale = 'fr';
        $admin->email_verified_at = now();
        $admin->save();
    }
}
