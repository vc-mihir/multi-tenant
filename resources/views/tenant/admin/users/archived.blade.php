@extends('layouts.tenant-admin')

@section('title', 'Archived Users')
@section('page-id', 'tenant-admin-users-archived')
@section('page-title', 'Archived Users')
@section('page-subtitle', 'View and manage soft-deleted tenant users')

@section('content')
    <div class="rounded-3xl border border-indigo-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-slate-800">Archived Records</h2>
            <a href="{{ route('tenant.admin.users.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl hover:bg-slate-100 transition-all text-xs font-bold uppercase tracking-wider shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>

        <div class="relative">
            <table id="archived-users-table" class="w-full text-left border-collapse"
                data-url="{{ route('tenant.admin.users.archived.data') }}">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="whitespace-nowrap">Verified At</th>
                        <th class="whitespace-nowrap">Created At</th>
                        <th class="whitespace-nowrap">Archived At</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
