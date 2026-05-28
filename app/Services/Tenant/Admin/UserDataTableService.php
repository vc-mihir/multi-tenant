<?php

namespace App\Services\Tenant\Admin;

use App\Models\Tenant\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserDataTableService
{
    /**
     * Get DataTables response for archived (soft-deleted) tenant users.
     *
     * @return JsonResponse
     */
    public function getArchivedData(): JsonResponse
    {
        try {
            $query = User::onlyTrashed();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('email_verified_at', function ($user) {
                    return $user->email_verified_at
                        ? '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">' . $user->email_verified_at->format('Y-m-d h:i A') . '</span>'
                        : '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">Not Verified</span>';
                })
                ->editColumn('created_at', function ($user) {
                    return '<span class="font-medium text-slate-700">' . $user->created_at->format('M d, Y') . '</span><br><span class="text-[10px] text-slate-400 uppercase">' . $user->created_at->format('h:i A') . '</span>';
                })
                ->editColumn('deleted_at', function ($user) {
                    return '<span class="font-medium text-rose-600">' . $user->deleted_at->format('M d, Y') . '</span><br><span class="text-[10px] text-slate-400 uppercase">' . $user->deleted_at->format('h:i A') . '</span>';
                })
                ->rawColumns(['email_verified_at', 'created_at', 'deleted_at'])
                ->toJson();
        } catch (\Exception $e) {
            Log::error('UserDataTableService::getArchivedData', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to load archived users data. Please try again.');
        }
    }

    /**
     * Get DataTables response for tenant users.
     *
     * @param string $tenant
     * @return JsonResponse
     */
    public function getData(string $tenant): JsonResponse
    {
        try {
            $query = User::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('email_verified_at', function ($user) {
                    return $user->email_verified_at
                        ? '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">' . $user->email_verified_at->format('Y-m-d h:i A') . '</span>'
                        : '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">Not Verified</span>';
                })
                ->editColumn('is_active', function ($user) {
                    return $user->is_active
                        ? '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Active</span>'
                        : '<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">Inactive</span>';
                })
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('Y-m-d h:i:s A');
                })
                ->editColumn('updated_at', function ($user) {
                    return $user->updated_at->format('Y-m-d h:i:s A');
                })
                ->addColumn('actions', function ($user) use ($tenant) {
                    $editUrl = route('tenant.admin.users.edit', ['tenant' => $tenant, 'user' => $user->id]);
                    return '
                        <div class="flex items-center justify-end gap-2 w-full">
                            <a href="' . $editUrl . '" class="p-2 text-slate-400 hover:text-teal-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors delete-user" data-id="' . $user->id . '">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>';
                })
                ->rawColumns(['actions', 'email_verified_at', 'is_active'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('UserDataTableService::getData', [
                'tenant' => $tenant,
                'error'  => $e->getMessage(),
            ]);
            throw new \Exception('Failed to load users data. Please try again.');
        }
    }
}
