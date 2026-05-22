<?php

namespace App\Services\Central\Admin;

use App\Models\Central\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CompanyDataTableService
{
    /**
     * Get DataTables response for companies
     *
     * @param string|null $statusFilter
     * @return JsonResponse
     */
    public function getData(?string $statusFilter): JsonResponse
    {
        try {
            $query = Company::with('database');

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

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
                        'active'    => 'bg-emerald-50 text-emerald-700 border-emerald-200/50',
                        'pending'   => 'bg-amber-50 text-amber-700 border-amber-200/50',
                        'suspended' => 'bg-red-50 text-red-700 border-red-200/50',
                        default     => 'bg-slate-50 text-slate-700 border-slate-200/50',
                    };
                    return '<span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider ' . $class . '">' . $company->status . '</span>';
                })
                ->editColumn('email_verified_at', function ($company) {
                    return $company->email_verified_at
                        ? $company->email_verified_at->format('M d, Y H:i')
                        : '<span class="text-slate-400 italic text-xs">Not Verified</span>';
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
        } catch (\Exception $e) {
            Log::error('CompanyDataTableService::getData', [
                'status_filter' => $statusFilter,
                'error'         => $e->getMessage(),
            ]);
            throw new \Exception('Failed to load companies data.');
        }
    }
}
