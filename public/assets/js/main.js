/**
 * Gestion Théâtre – JavaScript principal
 */

// Confirmation de suppression pour les formulaires sans onclick
document.addEventListener('DOMContentLoaded', function () {
    // Gestion des alertes auto-disparaissantes
    const alerts = document.querySelectorAll('.alert[data-autohide]');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .4s';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 400);
        }, 4000);
    });
});
