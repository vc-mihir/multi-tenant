@extends('layouts.tenant-admin')

@section('title', 'Users')
@section('page-title', 'Users Management')
@section('page-subtitle', 'Manage all users for this tenant')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('page-id', 'tenant-admin-users-index')

@section('content')
    <div class="rounded-3xl border border-indigo-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-slate-800">User Records</h2>
            <div class="flex items-center gap-4">
                {{-- Add any filters here if needed --}}
            </div>
        </div>

        <div class="relative">
            <table id="users-table" class="w-full text-left border-collapse" data-url="{{ route('tenant.admin.users.data') }}">
                <thead>
                    <tr>
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
