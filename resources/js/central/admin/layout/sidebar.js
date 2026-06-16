/**
 * Admin Layout - Responsive Sidebar Drawer
 *
 * On screens below the `lg` breakpoint the sidebar is hidden off-canvas and
 * toggled open via the header hamburger button. A backdrop overlay closes it.
 * Guarded: only runs when the sidebar markup is present on the page.
 */
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("admin-sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const toggleBtn = document.getElementById("sidebar-toggle");
    const closeBtn = document.getElementById("sidebar-close");

    if (!sidebar || !overlay) return;

    const openSidebar = () => {
        sidebar.classList.remove("-translate-x-full");
        sidebar.classList.add("translate-x-0");
        overlay.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    };

    const closeSidebar = () => {
        sidebar.classList.add("-translate-x-full");
        sidebar.classList.remove("translate-x-0");
        overlay.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    };

    if (toggleBtn) toggleBtn.addEventListener("click", openSidebar);
    if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
    overlay.addEventListener("click", closeSidebar);

    // Close on Escape key
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeSidebar();
    });

    // Reset state when resizing up to desktop (lg = 1024px)
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 1024) closeSidebar();
    });
});
