document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggleBtn = document.querySelector(".fc-sidebarToggle-button");
    const sidebar = document.querySelector(".app-calendar-sidebar");
    const overlay = document.querySelector(".app-overlay");

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("show");
            if (overlay) overlay.classList.toggle("show");
        });
    }

    // Cerrar sidebar al hacer clic en el overlay
    if (overlay) {
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("show");
            overlay.classList.remove("show");
        });
    }
});
