<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function ($user) {
                return $user->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('actions', function ($user) {
                return '
                    <div class="flex items-center justify-start gap-2">
                        <a href="#" class="p-2 text-slate-400 hover:text-teal-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </a>
                        <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors delete-user" data-id="' . $user->id . '">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>';
            })
            ->rawColumns(['actions'])
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

