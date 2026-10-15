// Kinetica Gym - Staff CRM Helpers
document.addEventListener('DOMContentLoaded', () => {
    // Highlight active table rows on hover
    const tableRows = document.querySelectorAll('.crm-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', () => row.style.backgroundColor = 'rgba(255, 255, 255, 0.02)');
        row.addEventListener('mouseleave', () => row.style.backgroundColor = '');
    });
});
