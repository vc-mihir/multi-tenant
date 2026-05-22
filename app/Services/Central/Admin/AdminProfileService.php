<?php

namespace App\Services\Central\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminProfileService
{
    /**
     * Update admin profile
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function update(User $user, array $data): void
    {
        try {
            $user->name  = $data['name'];
            $user->email = $data['email'];

            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();
        } catch (\Exception $e) {
            Log::error('AdminProfileService::update', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new \Exception('Failed to update profile. Please try again.');
        }
    }
}
