// Kinetica Gym - Member Portal Dashboard Helpers
document.addEventListener('DOMContentLoaded', () => {
    // Auto-dismiss alert notices after 6 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';
            setTimeout(() => alert.remove(), 400);
        }, 6000);
    });
});
