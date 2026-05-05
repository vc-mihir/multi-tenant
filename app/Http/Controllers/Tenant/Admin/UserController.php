<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateUserRequest;
use App\Models\Tenant\User;
use App\Http\Requests\Tenant\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $tenant
     * @return View
     */
    public function index(string $tenant): View
    {
        return view('tenant.admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $tenant
     * @return View
     */
    public function create(string $tenant): View
    {
        return view('tenant.admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $tenant
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(string $tenant, StoreUserRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('tenant.admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
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
     * Update the specified resource in storage.
     *
     * @param string $tenant
     * @param UpdateUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(string $tenant, UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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
        $query = User::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('email_verified_at', function ($user) {
                return $user->email_verified_at 
                    ? '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">' . $user->email_verified_at->format('Y-m-d H:i') . '</span>'
                    : '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">Not Verified</span>';
            })
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function ($user) {
                return $user->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('actions', function ($user) use ($tenant) {
                $editUrl = route('tenant.admin.users.edit', ['tenant' => $tenant, 'user' => $user->id]);
                return '
                    <div class="flex items-center justify-start gap-2">
                        <a href="' . $editUrl . '" class="p-2 text-slate-400 hover:text-teal-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </a>
                        <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors delete-user" data-id="' . $user->id . '">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>';
            })
            ->rawColumns(['actions', 'email_verified_at'])
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $tenant
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(string $tenant, User $user): JsonResponse
    {
        try {
            if (DB::getDefaultConnection() === 'mysql') {
                return response()->json([
                    'success' => false,
                    'message' => 'Security Error: Attempted deletion on central database blocked.'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

}

