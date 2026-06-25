@extends('layouts.admin')

@section('title', 'Archived Companies')
@section('page-id', 'central-admin-companies-archived')
@section('page-title', 'Archived Companies')
@section('page-subtitle', 'View and manage soft-deleted tenant companies')

@section('content')
    <div class="rounded-3xl border border-teal-100 bg-white p-4 shadow-sm ring-1 ring-slate-900/5 sm:p-6">
        <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-semibold text-slate-800">Archived Records</h2>
                <div id="bulk-actions" class="hidden flex items-center gap-2">
                    <button id="bulk-restore-btn"
                        class="flex items-center gap-2 px-4 py-2 bg-teal-50 text-teal-600 border border-teal-100 rounded-xl hover:bg-teal-100 transition-all text-xs font-bold uppercase tracking-wider shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        Restore Selected (<span id="restore-selected-count">0</span>)
                    </button>
                    <button id="bulk-force-delete-btn"
                        class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl hover:bg-rose-100 transition-all text-xs font-bold uppercase tracking-wider shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        Delete Selected (<span id="delete-selected-count">0</span>)
                    </button>
                </div>
            </div>
        </div>

        <div class="relative">
            <table id="archived-companies-table" class="w-full text-left border-collapse"
                data-url="{{ route('admin.companies.archived.data') }}"
                data-bulk-restore-url="{{ route('admin.companies.bulk-restore') }}"
                data-bulk-force-delete-url="{{ route('admin.companies.bulk-force-delete') }}">
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
                        <th>Status</th>
                        <th>Database Name</th>
                        <th class="whitespace-nowrap">Created At</th>
                        <th class="whitespace-nowrap">Deleted At</th>
                        <th class="text-right px-6">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
