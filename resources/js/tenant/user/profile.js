/**
 * Tenant User Side - Profile Edit
 */
const deleteBtn = document.querySelector("#delete-account-form button");
const deleteForm = document.getElementById("delete-account-form");

if (deleteBtn && deleteForm) {
    deleteBtn.addEventListener("click", function () {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Are you sure?",
                text: "Your account will be permanently deleted. This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e11d48",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, keep it",
                background: "#ffffff",
                color: "#4c0519",
                customClass: {
                    popup: "rounded-[2.5rem] border border-rose-50",
                    confirmButton:
                        "rounded-xl font-bold uppercase tracking-widest text-xs px-6 py-3",
                    cancelButton:
                        "rounded-xl font-bold uppercase tracking-widest text-xs px-6 py-3",
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Deleting Account...",
                        text: "Please wait while we process your request.",
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            deleteForm.submit();
                        }
                    });
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
