import '../../../../css/central/admin/companies/index.css';

$(function() {
    const tableElement = $('#companies-table');
    const dataUrl = tableElement.data('url');
    const bulkDeleteUrl = tableElement.data('bulk-delete-url');

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

    tableElement.on('click', '.show-more-text', function() {
        const text = $(this).attr('data-full-text');
        showFullText(text);
    });

    let table = tableElement.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            data: function(d) {
                d.status = $('#status-filter').val();
            }
        },
        order: [
            [0, 'desc']
        ],
        pageLength: 10,
        columns: [{
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<input type="checkbox" class="company-checkbox w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer" value="${data}">`;
                }
            },
            {
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

    $(document).on('change', '#select-all', function() {
        $('.company-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });

    $(document).on('change', '.company-checkbox', function() {
        toggleBulkActions();
    });

    function toggleBulkActions() {
        const count = $('.company-checkbox:checked').length;
        if (count > 0) {
            $('#bulk-actions').removeClass('hidden');
            $('#selected-count').text(count);
        } else {
            $('#bulk-actions').addClass('hidden');
        }
    }

    table.on('draw', function() {
        $('#select-all').prop('checked', false);
        toggleBulkActions();
    });

    // Bulk Delete Action
    $('#bulk-delete-btn').on('click', function() {
        const selectedIds = $('.company-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedIds.length} companies and their databases. This action is IRREVERSIBLE!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#be123c',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Everything!',
            borderRadius: '1.5rem'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: bulkDeleteUrl,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds
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
                    error: function() {
                        Swal.fire('Error!', 'An unexpected error occurred.',
                            'error');
                    }
                });
            }
        });
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
