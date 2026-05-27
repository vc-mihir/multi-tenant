import '../../../../css/tenant/admin/users/index.css';
import 'datatables.net-dt';

/**
 * Tenant Admin - Users Index
 */
$(function () {
    const tableEl = $("#users-table");
    if (!tableEl.length) return;

    const dataUrl = tableEl.data("url");
    const bulkDeleteUrl = tableEl.data("bulk-delete-url");

    let table = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: dataUrl,
        order: [[1, "desc"]],
        pageLength: 10,
        columns: [
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `<input type="checkbox" class="user-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" value="${data}">`;
                },
            },
            {
                data: "DT_RowIndex",
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `<span class="font-bold text-slate-400">#${data}</span>`;
                },
            },
            {
                data: "name",
                name: "name",
                render: function (data) {
                    return `<span class="font-semibold text-slate-900 whitespace-nowrap">${data}</span>`;
                },
            },
            {
                data: "email",
                name: "email",
                render: function (data) {
                    return `<span class="text-slate-600">${data}</span>`;
                },
            },
            {
                data: "email_verified_at",
                name: "email_verified_at",
                className: "whitespace-nowrap",
            },
            {
                data: "created_at",
                name: "created_at",
                className: "whitespace-nowrap text-slate-500",
            },
            {
                data: "updated_at",
                name: "updated_at",
                className: "whitespace-nowrap text-slate-500",
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-right px-6",
            },
        ],
    });

    // Select all checkbox
    $(document).on("change", "#select-all", function () {
        $(".user-checkbox").prop("checked", this.checked);
        toggleBulkActions();
    });

    $(document).on("change", ".user-checkbox", function () {
        toggleBulkActions();
    });

    function toggleBulkActions() {
        const count = $(".user-checkbox:checked").length;
        if (count > 0) {
            $("#bulk-actions").removeClass("hidden");
            $("#selected-count").text(count);
        } else {
            $("#bulk-actions").addClass("hidden");
        }
    }

    // Reset checkboxes on table redraw
    table.on("draw", function () {
        $("#select-all").prop("checked", false);
        toggleBulkActions();
    });

    // Bulk delete
    $("#bulk-delete-btn").on("click", function () {
        const selectedIds = $(".user-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete ${selectedIds.length} user(s). This action cannot be undone!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Delete All!",
            cancelButtonText: "Cancel",
            borderRadius: "1.25rem",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Deleting...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });

                $.ajax({
                    url: bulkDeleteUrl,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: { ids: selectedIds },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#6366f1",
                                borderRadius: "1.25rem",
                            });
                            table.draw(false);
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error!", "An unexpected error occurred.", "error");
                    },
                });
            }
        });
    });

    // Single delete
    $(document).on("click", ".delete-user", function () {
        const userId = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to delete this user from the tenant database. This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            borderRadius: "1.25rem",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${userId}`,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#6366f1",
                                borderRadius: "1.25rem",
                            });
                            table.draw(false);
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function (xhr) {
                        const errorMessage =
                            xhr.responseJSON?.message ?? "An unexpected error occurred.";
                        Swal.fire("Error!", errorMessage, "error");
                    },
                });
            }
        });
    });
});
