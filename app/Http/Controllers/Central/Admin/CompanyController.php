<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Central\Admin\UpdateCompanyRequest;
use App\Http\Requests\Central\Admin\StoreCompanyRequest;
use App\Services\CompanyService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;

class CompanyController extends Controller
{
    /**
     * The company service instance.
     */
    protected CompanyService $companyService;

    /**
     * Create a new controller instance.
     *
     * @param CompanyService $companyService
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display the admin companies listing page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('central.admin.companies.index');
    }

    /**
     * Show the form for creating a new company.
     *
     * @return View
     */
    public function create(): View
    {
        return view('central.admin.companies.create');
    }

    /**
     * Store a newly created company in storage.
     *
     * @param StoreCompanyRequest $request
     * @return RedirectResponse
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->companyService->createCompany($validated, true);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company created successfully. Database provisioning has been queued.');
        } catch (Exception $e) {
            Log::error('Admin company creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    /**
     * Provide company records for Yajra DataTables.
     *
     * @return JsonResponse
     */
    public function data(): JsonResponse
    {
        $query = Company::with('database');

        $query->when(request('status'), function ($q) {
            return $q->where('status', request('status'));
        });

        return DataTables::of($query)
            ->addColumn('database_name', function ($company) {
                if ($company->database) {
                    return '<code class="px-2 py-1 bg-teal-50 text-teal-700 rounded text-xs font-mono border border-teal-100">' . $company->database->db_name . '</code>';
                }
                
                if ($company->email_verified_at) {
                    return '<span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-bold uppercase">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        Ready to Provision
                    </span>';
                }

                return '<span class="px-2 py-1 rounded-lg bg-slate-50 text-slate-400 border border-slate-100 text-[10px] font-bold uppercase italic">Awaiting Verification</span>';
            })
            ->editColumn('status', function ($company) {
                $class = match ($company->status) {
                    'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50',
                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200/50',
                    'suspended' => 'bg-red-50 text-red-700 border-red-200/50',
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
            ->rawColumns(['status', 'email_verified_at', 'created_at', 'updated_at', 'database_name'])
            ->addIndexColumn()
            ->toJson();
    }

    /**
     * Show the form for editing the specified company.
     *
     * @param Company $company
     * @return View
     */
    public function edit(Company $company): View
    {
        return view('central.admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     *
     * @param UpdateCompanyRequest $request
     * @param Company $company
     * @return RedirectResponse
     */
    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $company->update($validated);

            if ($company->database?->db_name){
                Config::set('database.connections.tenant.database', $company->database->db_name);
                DB::purge('tenant');

                DB::connection('tenant')->table('companies')
                    ->where('master_company_id', $company->id)
                    ->update($validated);
            }

            DB::commit();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company details updated and synced across databases.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update and sync company [{$company->id}] - " . $e->getMessage());
            return back()->with('error', 'Failed to update company details. ' . $e->getMessage());
        }
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

    /**
     * Search for companies (used in global search).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $companies = Company::where('company_name', 'LIKE', "%{$query}%")
            ->orWhere('company_email', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'company_name', 'company_email']);

        $results = $companies->map(function($company) {
            return [
                'id' => $company->id,
                'name' => $company->company_name,
                'email' => $company->company_email,
                'url' => route('admin.companies.edit', $company->id)
            ];
        });

        return response()->json($results);
    }
}
