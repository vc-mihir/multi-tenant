@extends('layouts.admin')

@section('title', 'Companies')
@section('page-title', 'Companies')
@section('page-subtitle', 'Manage all registered tenant companies')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <style>
        .dt-container .dt-layout-row:has(table) {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(45, 212, 191, 0.4) transparent;
        }

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

        #companies-table th,
        #companies-table td {
            padding: 1rem 1.25rem !important;
            vertical-align: middle;
        }

        #companies-table thead th {
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
    <div class="rounded-3xl border border-teal-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-slate-800">Company Records</h2>
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
            <table id="companies-table" class="w-full text-left border-collapse">
                <thead>
                    <tr>
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

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        $(function() {
            const showFullText = (text) => {
                Swal.fire({
                    title: 'Full Details',
                    html: `<div class="text-left p-4 text-slate-600 leading-relaxed break-words">${text}</div>`,
                    confirmButtonColor: '#0d9488',
                    confirmButtonText: 'Close',
                    borderRadius: '1.5rem'
                });
            };

            const formatLongText = (data, limit = 30) => {
                if (!data) return '';
                if (data.length > limit) {
                    return `<span class="cursor-pointer hover:underline decoration-slate-300 show-more-text" data-full-text="${data}">${data.substring(0, limit)}...</span>`;
                }
                return data;
            };

            $('#companies-table').on('click', '.show-more-text', function() {
                const text = $(this).attr('data-full-text');
                showFullText(text);
            });

            let table = $('#companies-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.companies.data') }}',
                    data: function(d) {
                        d.status = $('#status-filter').val();
                    }
                },
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
                            return `<span class="font-semibold text-slate-900 whitespace-nowrap">${formatLongText(data, 30)}</span>`;
                        }
                    },
                    {
                        data: 'subdomain',
                        name: 'subdomain',
                        render: function(data) {
                            return `<code class="px-2 py-1 bg-slate-50 text-slate-600 rounded text-xs font-mono border border-slate-100">${data || '-'}</code>`;
                        }
                    },
                    {
                        data: 'company_email',
                        name: 'company_email',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            return formatLongText(data, 30);
                        }
                    },
                    {
                        data: 'website',
                        name: 'website',
                        render: function(data) {
                            if (!data) return '<span class="text-slate-300">-</span>';
                            const cleanUrl = data.replace('http://', '').replace('https://', '');
                            if (data.length > 30) {
                                return `<div class="flex items-center gap-1.5 whitespace-nowrap">
                                    <span class="cursor-pointer show-more-text" data-full-text="${data}">${cleanUrl.substring(0, 30)}...</span>
                                    <a href="${data}" target="_blank" class="text-slate-300 hover:text-teal-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                </div>`;
                            }
                            return `<a href="${data}" target="_blank" class="text-teal-600 hover:text-teal-700 transition-colors flex items-center gap-1.5 whitespace-nowrap">
                                <span class="inline-block">${cleanUrl}</span>
                                <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>`;
                        }
                    },
                    {
                        data: 'license_number',
                        name: 'license_number',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            return formatLongText(data, 30);
                        }
                    },
                    {
                        data: 'address',
                        name: 'address',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            return formatLongText(data, 30);
                        }
                    },
                    {
                        data: 'country',
                        name: 'country',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            return formatLongText(data, 30);
                        }
                    },
                    {
                        data: 'state',
                        name: 'state',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            return formatLongText(data, 30);
                        }
                    },
                    {
                        data: 'city',
                        name: 'city',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            return formatLongText(data, 30);
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'whitespace-nowrap'
                    },
                    {
                        data: 'email_verified_at',
                        name: 'email_verified_at',
                        className: 'whitespace-nowrap'
                    },
                    {
                        data: 'database_name',
                        name: 'database_name',
                        className: 'whitespace-nowrap',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'whitespace-nowrap'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        className: 'whitespace-nowrap'
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-right px-6',
                        render: function(data) {
                            return `
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/admin/companies/${data}/edit" class="p-2 text-slate-400 hover:text-teal-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors delete-company" data-id="${data}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            $(document).on('change', '#status-filter', function() {
                table.draw();
            });

            $(document).on('click', '.delete-company', function() {
                const companyId = $(this).data('id');

                Swal.fire({
                    title: 'CRITICAL ACTION!',
                    text: "You are about to PERMANENTLY DELETE this company and DROP their entire tenant database. This will destroy all users, records, and settings. THIS CANNOT BE UNDONE!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#be123c',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Destroy Everything!',
                    cancelButtonText: 'Cancel',
                    padding: '2rem',
                    borderRadius: '1.5rem',
                    backdrop: `rgba(158, 89, 89, 0.05)`,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Purging Data...',
                            text: 'Destroying tenant database and records',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/admin/companies/${companyId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#0d9488',
                                        borderRadius: '1.5rem'
                                    });
                                    table.draw(false);
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                let errorMsg = 'An unexpected error occurred.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error!', errorMsg, 'error');
                            }
                        });
                    }
                });
            });
        });

        @if (session('success'))
            $(function() {
                Swal.fire({
                    title: 'Updated!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#0d9488',
                    borderRadius: '1.5rem'
                });
            });
        @endif
    </script>
@endpush
