import "./bootstrap";
import "./validation/common-validation";
import "./central/admin/layout/search";

window.showAlert = (icon, title, text) => {
    if (typeof Swal !== "undefined") {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: "#ffffff",
            color: "#064e3b",
            iconColor: "#059669",
            customClass: {
                popup: "rounded-3xl border border-emerald-50 shadow-2xl shadow-emerald-900/10",
            },
        });
    }
};
import "./csrf-handler";

window.togglePasswordVisibility = (inputId, toggleBtn) => {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isHidden = input.type === "password";
    input.type = isHidden ? "text" : "password";
    const eyeOpen = toggleBtn.querySelector(".eye-open");
    const eyeClosed = toggleBtn.querySelector(".eye-closed");
    if (eyeOpen) eyeOpen.classList.toggle("hidden", !isHidden);
    if (eyeClosed) eyeClosed.classList.toggle("hidden", isHidden);
};

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

/**
 * Dynamic Asset Router
 * Loads page-specific JS/CSS based on document.body.dataset.page
 */
document.addEventListener("DOMContentLoaded", () => {
    const pageId = document.body.dataset.page;
    if (!pageId) return;

    switch (pageId) {
        case "central-admin-dashboard":
            import("./central/admin/dashboard/dashboard.js");
            break;
        case "central-admin-companies-index":
            import("./central/admin/companies/index.js");
            break;
        case "central-admin-companies-create":
            import("./central/admin/companies/create.js");
            break;
        case "central-admin-companies-edit":
            import("./central/admin/companies/edit.js");
            break;
        case "central-auth-register":
            import("./central/auth/register.js");
            break;
        case "central-admin-settings":
            import("./central/admin/settings/settings.js");
            break;
        case "error-page":
            import("../css/errors/error-layout.css");
            import("./shared/error-handler.js");
            break;
        case "error-429":
            import("../css/errors/error-layout.css");
            import("./shared/error-handler.js");
            import("./errors/429.js");
            break;
        case "tenant-admin-login":
            import("../css/tenant/auth/admin-login.css");
            break;
        case "tenant-user-layout":
            import("../css/tenant/user/user-layout.css");
            break;
        case "tenant-user-dashboard":
            import("../css/tenant/user/user-layout.css");
            break;
        case "tenant-user-profile":
            import("../css/tenant/user/user-layout.css");
            import("./tenant/user/profile.js");
            break;
        case "tenant-admin-profile-edit":
            import("./tenant/admin/profile/edit.js");
            break;
        case "tenant-admin-users-index":
            import("../css/tenant/admin/users/index.css");
            import("./tenant/admin/users/index.js");
            break;
        case "tenant-admin-dashboard":
            import("../css/tenant/admin/dashboard.css");
            break;
    }

    if (pageId.startsWith("tenant-admin-")) {
        import("../css/tenant/admin-layout.css");
    }
});
