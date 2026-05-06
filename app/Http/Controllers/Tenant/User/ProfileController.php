<?php

namespace App\Http\Controllers\Tenant\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

use App\Http\Requests\Tenant\User\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Show the user profile page
     *
     * @return View
     */
    public function edit(): View
    {
        return view('tenant.user.profile', [
            'user' => Auth::guard('tenant_user')->user()
        ]);
    }

    /**
     * Update the user profile
     *
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::guard('tenant_user')->user();
        $validated = $request->validated();

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Re-authenticate to keep the session alive after password change
        Auth::guard('tenant_user')->login($user);

        return redirect()
            ->route('tenant.user.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::guard('tenant_user')->user();

        Auth::guard('tenant_user')->logout();

        $user->delete();

        return redirect()
            ->route('tenant.login')
            ->with('success', 'Your account has been deleted.');
    }
}
