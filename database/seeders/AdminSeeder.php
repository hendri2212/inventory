<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@inventory.com';

        if (User::where('email', $email)->exists()) {
            $this->command?->info('Akun admin sudah tersedia; tidak diubah.');

            return;
        }

        $password = '12345';
        $admin = new User;
        $admin->name = 'Administrator';
        $admin->email = $email;
        $admin->password = $password;
        $admin->role = 'superadmin';
        $admin->email_verified_at = now();
        $admin->save();

        $this->command?->info('Admin dibuat: '.$email);
        $this->command?->info('Password awal: '.$password);
    }
}
