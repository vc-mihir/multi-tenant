<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */ 
    public function index(): View
    {
        return view('tenant.admin.users.index');
    }

    /**
     * Process datatables ajax request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
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
                    <div class="flex items-center justify-end gap-2">
                        <a href="#" class="p-2 text-slate-400 hover:text-teal-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </a>
                    </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

}

