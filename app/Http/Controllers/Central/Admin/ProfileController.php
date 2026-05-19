<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Admin\UpdateProfileRequest;
use App\Services\Central\Admin\AdminProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param AdminProfileService $profileService
     */
    public function __construct(
        protected AdminProfileService $profileService,
    ) {}

    /**
     * Load profile edit view
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
     * Update admin profile
     *
     * @param UpdateProfileRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profileService->update(Auth::user(), $request->validated());

        return back()->with('success', 'Profile updated successfully.');
    }
}
