<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="annuler-page">
    <h2>Annuler ma réservation</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <p>Vous souhaitez annuler votre réservation pour :</p>
    <div class="reservation-summary">
        <p><strong>Référence :</strong> <code><?= htmlspecialchars($reservation->id, ENT_QUOTES, 'UTF-8') ?></code></p>
        <p><strong>Nom :</strong> <?= htmlspecialchars($reservation->prenom . ' ' . $reservation->nom, ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Statut actuel :</strong> <?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <?php if ($reservation->statut === 'annule'): ?>
        <p class="already-cancelled">Cette réservation est déjà annulée.</p>
    <?php else: ?>
        <form method="POST" action="/reservation/annuler/<?= urlencode($reservation->id) ?>">
            <p>Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.</p>
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Oui, annuler ma réservation</button>
                <a href="/" class="btn btn-secondary">Non, retour à l'accueil</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
