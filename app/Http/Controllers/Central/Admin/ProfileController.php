<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Http\Requests\Central\Admin\UpdateProfileRequest;

class ProfileController extends Controller
{
    /**
     * Display the admin profile settings page.
     *
     * @return View
     */
    public function edit(): View
    {
        return view('central.admin.settings.index', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the admin profile.
     *
     * @param UpdateProfileRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
