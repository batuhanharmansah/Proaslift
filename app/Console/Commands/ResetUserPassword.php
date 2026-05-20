<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email : User email} {password : New plain password}';

    protected $description = "Reset a user's password (plain password; User model hashes it)";

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $user->password = $password;
        $user->save();

        $this->info("Password updated for: {$email}");
        return 0;
    }
}
