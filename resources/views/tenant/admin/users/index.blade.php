@extends('layouts.tenant-admin')

@section('title', 'Users')
@section('page-title', 'Users Management')
@section('page-subtitle', 'Manage all users for this tenant')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
@endpush

@section('page-id', 'tenant-admin-users-index')

@section('content')
    <div class="rounded-3xl border border-indigo-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-semibold text-slate-800">User Records</h2>
                <div id="bulk-actions" class="hidden">
                    <button id="bulk-delete-btn"
                        class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl hover:bg-rose-100 transition-all text-xs font-bold uppercase tracking-wider shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        Delete Selected (<span id="selected-count">0</span>)
                    </button>
                </div>
            </div>
        </div>

        <div class="relative">
            <table id="users-table" class="w-full text-left border-collapse"
                data-url="{{ route('tenant.admin.users.data') }}"
                data-bulk-delete-url="{{ route('tenant.admin.users.bulk-delete') }}">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" id="select-all"
                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="whitespace-nowrap">Verified At</th>
                        <th class="whitespace-nowrap">Created At</th>
                        <th class="whitespace-nowrap">Updated At</th>
                        <th class="text-right px-6">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
