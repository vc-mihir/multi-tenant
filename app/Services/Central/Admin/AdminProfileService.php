<?php

namespace App\Services\Central\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminProfileService
{
    /**
     * Update Admin Profile
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function update(User $user, array $data): void
    {
        $user->name  = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
    }
}
