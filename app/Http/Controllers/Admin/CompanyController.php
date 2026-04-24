<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CompanyController extends Controller
{
    /**
     * Display the admin companies listing page.
     */
    public function index(): View
    {
        return view('admin.companies.index');
    }

    /**
     * Provide company records for Yajra DataTables.
     *
     * @return JsonResponse
     */
    public function data(): JsonResponse
    {
        $query = Company::with('database');

        // Apply status filter if provided
        $query->when(request('status'), function ($q) {
            return $q->where('status', request('status'));
        });

        return DataTables::of($query)
            ->editColumn('status', function ($company) {
                $class = match ($company->status) {
                    'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50',
                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200/50',
                    'suspended' => 'bg-rose-50 text-rose-700 border-rose-200/50',
                     default => 'bg-slate-50 text-slate-700 border-slate-200/50'
                };
                return '<span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider ' . $class . '">' . $company->status . '</span>';
            })
            ->editColumn('email_verified_at', function ($company) {
                return $company->email_verified_at ? $company->email_verified_at->format('M d, Y H:i') : '<span class="text-slate-400 italic text-xs">Not Verified</span>';
            })
            ->editColumn('created_at', function ($company) {
                return '<span class="font-medium text-slate-700">' . $company->created_at->format('M d, Y') . '</span><br><span class="text-[10px] text-slate-400 uppercase">' . $company->created_at->format('H:i') . '</span>';
            })
            ->editColumn('updated_at', function ($company) {
                return '<span class="font-medium text-slate-700">' . $company->updated_at->format('M d, Y') . '</span><br><span class="text-[10px] text-slate-400 uppercase">' . $company->updated_at->format('H:i') . '</span>';
            })
            ->rawColumns(['status', 'email_verified_at', 'created_at', 'updated_at'])
            ->addIndexColumn()
            ->toJson();
    }

    /**
     * Remove the specified company from master and tenant databases.
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        if (!auth()->user()->hasRole('SuperAdmin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $dbName = $company->database?->db_name;

        try {
            if ($dbName) {
                DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
            }
        } catch (Exception $e) {
            Log::error("Failed to drop database [{$dbName}] - " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to purge tenant database.'], 500);
        }

        try {
            $company->delete();
        } catch (Exception $e) {
            Log::error("Failed to delete master record for [{$company->id}] - " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete master record.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Company and database successfully purged.']);
    }
}
