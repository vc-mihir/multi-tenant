/**
 * Tenant Admin - Profile Edit
 */
document.addEventListener("DOMContentLoaded", function () {
    const deleteBtn = document.getElementById("delete-account-btn");
    const deleteForm = document.getElementById("delete-account-form");

    if (deleteBtn && deleteForm) {
        deleteBtn.addEventListener("click", function () {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    title: "Are you absolutely sure?",
                    text: "This will permanently delete your company account and completely erase your database. This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc2626",
                    cancelButtonColor: "#475569",
                    confirmButtonText: "Yes, permanently delete it",
                    cancelButtonText: "Cancel",
                    reverseButtons: true,
                    customClass: {
                        title: "text-xl font-bold text-slate-800",
                        htmlContainer: "text-sm text-slate-500",
                        confirmButton:
                            "px-6 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all",
                        cancelButton:
                            "px-6 py-2.5 rounded-lg font-bold text-sm transition-all",
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Deleting Account...",
                            text: "Please wait while we erase your data.",
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                        deleteForm.submit();
                    }
                });
            } else {
                if (
                    confirm(
                        "Are you sure you want to delete your account? This cannot be undone."
                    )
                ) {
                    deleteForm.submit();
                }
            }
        });
    }
});
