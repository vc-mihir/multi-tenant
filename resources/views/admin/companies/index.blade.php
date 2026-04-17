@extends('layouts.admin')

@section('title', 'Companies')
@section('page-title', 'Companies')
@section('page-subtitle', 'Manage all registered tenant companies')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <style>
        /* Isolate horizontal scroll to the table area only */
        .dt-container .dt-layout-row:has(table) {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(45, 212, 191, 0.4) transparent;
        }

        /* Custom scrollbar for Webkit */
        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar {
            height: 6px;
        }

        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar-track {
            background: transparent;
        }

        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar-thumb {
            background: rgba(45, 212, 191, 0.2);
            border-radius: 10px;
        }

        .dt-container .dt-layout-row:has(table)::-webkit-scrollbar-thumb:hover {
            background: rgba(45, 212, 191, 0.4);
        }

        /* Ensure controls stay pinned and table has room to breathe */
        .dt-search input {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.375rem 0.75rem;
            outline: none;
            background: #f8fafc;
        }

        .dt-paging-button.current {
            background: #0d9488 !important;
            color: white !important;
            border: none !important;
            border-radius: 0.5rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="rounded-3xl border border-teal-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="relative">
            <table id="companies-table" class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
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
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        $(function() {
            $('#companies-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.companies.data') }}',
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
                        data: 'company_name',
                        name: 'company_name',
                        render: function(data) {
                            return `<span class="font-semibold text-slate-900">${data}</span>`;
                        }
                    },
                    {
                        data: 'company_email',
                        name: 'company_email',
                    },
                    {
                        data: 'website',
                        name: 'website',
                        render: function(data) {
                            if (!data) return '<span class="text-slate-300">-</span>';
                            return `<a href="${data}" target="_blank" class="text-teal-600 hover:text-teal-700 transition-colors flex items-center gap-1.5">
                                <span class="truncate max-w-[150px] inline-block">${data.replace('http://', '').replace('https://', '')}</span>
                                <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>`;
                        }
                    },
                    {
                        data: 'license_number',
                        name: 'license_number'
                    },
                    {
                        data: 'address',
                        name: 'address',
                        className: 'max-w-xs truncate'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'email_verified_at',
                        name: 'email_verified_at'
                    },
                    {
                        data: 'database_name',
                        name: 'database_name',
                        render: function(data) {
                            return `<code class="px-2 py-1 bg-slate-100 rounded text-xs font-mono text-slate-600">${data}</code>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    }
                ]
            });
        });
    </script>
@endpush
