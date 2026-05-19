<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreUserRequest;
use App\Http\Requests\Tenant\UpdateUserRequest;
use App\Models\Tenant\User;
use App\Services\Tenant\Admin\UserDataTableService;
use App\Services\Tenant\Admin\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
     * Bulk delete multiple users by IDs.
     *
     * @param string $tenant
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDestroy(string $tenant, Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No users selected.'], 422);
            }

            $count = $this->userService->bulkDeleteUsers($ids);

            return response()->json([
                'success' => true,
                'message' => "{$count} user(s) deleted successfully.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user.
     *
     * @param string $tenant
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(string $tenant, User $user): JsonResponse
    {
        try {
            $this->userService->deleteUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
