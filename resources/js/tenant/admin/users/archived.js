import '../../../../css/tenant/admin/users/index.css';
import 'datatables.net-dt';

$(function () {
    const tableElement = $('#archived-users-table');
    const dataUrl      = tableElement.data('url');

    const table = tableElement.DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: dataUrl },
        order: [[0, 'desc']],
        pageLength: 10,
        columns: [
            {
                data: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: (data) => `<span class="font-bold text-slate-400">#${data}</span>`
            },
            {
                data: 'name',
                name: 'name',
                render: (data) => `<span class="font-semibold text-slate-900 whitespace-nowrap">${data}</span>`
            },
            {
                data: 'email',
                name: 'email',
                render: (data) => `<span class="text-slate-600">${data}</span>`
            },
            {
                data: 'email_verified_at',
                name: 'email_verified_at',
                className: 'whitespace-nowrap'
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'whitespace-nowrap'
            },
            {
                data: 'deleted_at',
                name: 'deleted_at',
                className: 'whitespace-nowrap'
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'text-right',
                render: (data) => `
                    <div class="flex items-center justify-end gap-2">
                        <button class="restore-user p-2 text-slate-400 hover:text-indigo-600 transition-colors" data-id="${data}" title="Restore">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        <button class="force-delete-user p-2 text-slate-400 hover:text-rose-600 transition-colors" data-id="${data}" title="Permanently Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                `
            }
        ]
    });

    // Restore
    $(document).on('click', '.restore-user', function () {
        const userId = $(this).data('id');

        Swal.fire({
            title: 'Restore User?',
            text: 'This will restore the user and allow them to log in again.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Restore',
            cancelButtonText: 'Cancel',
            borderRadius: '1.5rem'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({ title: 'Restoring...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: `/admin/users/${userId}/restore`,
                type: 'PATCH',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({ title: 'Restored!', text: response.message, icon: 'success', confirmButtonColor: '#6366f1', borderRadius: '1.5rem' });
                        table.draw(false);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message ?? 'An unexpected error occurred.', 'error');
                }
            });
        });
    });

    // Permanent Delete
    $(document).on('click', '.force-delete-user', function () {
        const userId = $(this).data('id');

        Swal.fire({
            title: 'Permanently Delete?',
            text: 'This will permanently erase the user record. This action cannot be undone.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#be123c',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Forever',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
            borderRadius: '1.5rem'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: `/admin/users/${userId}/force-delete`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({ title: 'Deleted!', text: response.message, icon: 'success', confirmButtonColor: '#6366f1', borderRadius: '1.5rem' });
                        table.draw(false);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message ?? 'An unexpected error occurred.', 'error');
                }
            });
        });
    });
});
