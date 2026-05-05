@extends('layouts.tenant-admin')

@section('title', 'Users')
@section('page-title', 'Users Management')
@section('page-subtitle', 'Manage all users for this tenant')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <style>
        .dt-container .dt-layout-row:has(table) {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(99, 102, 241, 0.4) transparent;
        }

        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar {
            height: 6px;
        }

        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar-track {
            background: transparent;
        }

        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.2);
            border-radius: 10px;
        }

        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.4);
        }

        .dt-search input {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.375rem 0.75rem;
            outline: none;
            background: #f8fafc;
        }

        .dt-paging-button.current {
            background: #6366f1 !important;
            color: white !important;
            border: none !important;
            border-radius: 0.5rem !important;
        }

        #users-table th,
        #users-table td {
            padding: 1rem 1.25rem !important;
            vertical-align: middle;
        }

        #users-table thead th {
            white-space: nowrap;
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
@endpush

@section('content')
    <div class="rounded-3xl border border-indigo-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-slate-800">User Records</h2>
            <div class="flex items-center gap-4">
                {{-- Add any filters here if needed --}}
            </div>
        </div>

        <div class="relative">
            <table id="users-table" class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="whitespace-nowrap">Created At</th>
                        <th class="whitespace-nowrap">Updated At</th>
                        <th class="text-right px-6">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            let table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('tenant.admin.users.data') }}',
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<span class="font-bold text-slate-400">#${data}</span>`;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data) {
                            return `<span class="font-semibold text-slate-900 whitespace-nowrap">${data}</span>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        render: function(data) {
                            return `<span class="text-slate-600">${data}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'whitespace-nowrap text-slate-500'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        className: 'whitespace-nowrap text-slate-500'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-right px-6'
                    }
                ]
            });

        });
    </script>
@endpush
