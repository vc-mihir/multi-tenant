/**
 * Tenant Admin - Users Index
 */
$(function () {
    const tableEl = $("#users-table");
    if (!tableEl.length) return;

    const dataUrl = tableEl.data("url");

    let table = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: dataUrl,
        order: [[0, "desc"]],
        pageLength: 10,
        columns: [
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

    $(document).on("click", ".delete-user", function () {
        const userId = $(this).data("id");
        if (typeof Swal !== "undefined") {
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
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
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
                            let errorMessage = "An unexpected error occurred.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire("Error!", errorMessage, "error");
                        },
                    });
                }
            });
        }
    });
});
