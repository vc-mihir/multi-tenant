@extends('layouts.admin')

@section('title', 'Archived Companies')
@section('page-id', 'central-admin-companies-archived')
@section('page-title', 'Archived Companies')
@section('page-subtitle', 'View and manage soft-deleted tenant companies')

@section('content')
    <div class="rounded-3xl border border-teal-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-slate-800">Archived Records</h2>
        </div>

        <div class="relative">
            <table id="archived-companies-table" class="w-full text-left border-collapse"
                data-url="{{ route('admin.companies.archived.data') }}">
                <thead>
                    <tr>
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
