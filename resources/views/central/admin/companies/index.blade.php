@extends('layouts.admin')

@section('title', 'Companies')
@section('page-id', 'central-admin-companies-index')
@section('page-title', 'Companies')
@section('page-subtitle', 'Manage all registered tenant companies')


@section('content')
    <div class="rounded-3xl border border-teal-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-semibold text-slate-800">Company Records</h2>
                <div id="bulk-actions" class="hidden">
                    <button id="bulk-delete-btn"
                        class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl hover:bg-rose-100 transition-all text-xs font-bold uppercase tracking-wider shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        Delete Selected (<span id="selected-count">0</span>)
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status:</span>
                    <select id="status-filter"
                        class="px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm bg-slate-50/50 transition-all cursor-pointer min-w-[120px]">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="relative">
            <table id="companies-table" class="w-full text-left border-collapse"
                data-url="{{ route('admin.companies.data') }}"
                data-bulk-delete-url="{{ route('admin.companies.bulk-delete') }}">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" id="select-all"
                                class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                        </th>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Subdomain</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>License Number</th>
                        <th>Address</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Email Verified At</th>
                        <th>Database Name</th>
                        <th class="whitespace-nowrap">Created At</th>
                        <th class="whitespace-nowrap">Updated At</th>
                        <th class="text-right px-6">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

