<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="confirmation-page">
    <div class="confirmation-box">
        <div class="success-icon">❌</div>
        <h2>Réservation annulée</h2>
        <p>Votre réservation a bien été annulée.</p>
        <p>Un email de confirmation vous a été envoyé à l'adresse <strong><?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></strong>.</p>
        <div class="actions">
            <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
