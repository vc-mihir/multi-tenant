<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\BulkDeleteUsersRequest;
use App\Http\Requests\Tenant\StoreUserRequest;
use App\Http\Requests\Tenant\UpdateUserRequest;
use App\Models\Tenant\User;
use App\Services\Tenant\Admin\UserDataTableService;
use App\Services\Tenant\Admin\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param UserService $userService
     * @param UserDataTableService $dataTableService
     */
    public function __construct(
        protected UserService $userService,
        protected UserDataTableService $dataTableService,
    ) {}

    /**
     * Display a listing of users.
     *
     * @param string $tenant
     * @return View
     */
    public function index(string $tenant): View
    {
        return view('tenant.admin.users.index');
    }

    /**
     * Show the form for creating a new user.
     *
     * @param string $tenant
     * @return View
     */
    public function create(string $tenant): View
    {
        return view('tenant.admin.users.create');
    }

    /**
     * Store a newly created user.
     *
     * @param string $tenant
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(string $tenant, StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        return redirect()->route('tenant.admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing a user.
     *
     * @param string $tenant
     * @param User $user
     * @return View
     */
    public function edit(string $tenant, User $user): View
    {
        return view('tenant.admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     *
     * @param string $tenant
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(string $tenant, UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()->route('tenant.admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Display the archived (soft-deleted) users listing view.
     *
     * @param string $tenant
     * @return View
     */
    public function archived(string $tenant): View
    {
        return view('tenant.admin.users.archived');
    }

    /**
     * Return DataTables JSON for archived (soft-deleted) users.
     *
     * @param string $tenant
     * @return JsonResponse
     */
    public function archivedData(string $tenant): JsonResponse
    {
        return $this->dataTableService->getArchivedData();
    }

    /**
     * Process datatables ajax request.
     *
     * @param string $tenant
     * @param Request $request
     * @return JsonResponse
     */
    public function data(string $tenant, Request $request): JsonResponse
    {
        return $this->dataTableService->getData($tenant);
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param string $tenant
     * @param User $user
     * @return JsonResponse
     */
    public function restore(string $tenant, User $user): JsonResponse
    {
        $this->userService->restoreUser($user);

        return response()->json(['success' => true, 'message' => 'User has been restored successfully.']);
    }

    /**
     * Permanently delete a soft-deleted user.
     *
     * @param string $tenant
     * @param User $user
     * @return JsonResponse
     */
    public function forceDelete(string $tenant, User $user): JsonResponse
    {
        $this->userService->forceDeleteUser($user);

        return response()->json(['success' => true, 'message' => 'User permanently deleted.']);
    }

    /**
     * Bulk soft-delete multiple users by IDs.
     *
     * @param string $tenant
     * @param BulkDeleteUsersRequest $request
     * @return JsonResponse
     */
    public function bulkDestroy(string $tenant, BulkDeleteUsersRequest $request): JsonResponse
    {
        $count = $this->userService->bulkDeleteUsers($request->validated()['ids']);

        return response()->json([
            'success' => true,
            'message' => "{$count} user(s) archived successfully.",
        ]);
    }

    /**
     * Soft-delete the specified user (moves to archive).
     *
     * @param string $tenant
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(string $tenant, User $user): JsonResponse
    {
        $this->userService->deleteUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User archived successfully.',
        ]);
    }
}
