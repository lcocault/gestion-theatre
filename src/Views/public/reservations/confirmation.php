<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="confirmation-page">
    <div class="confirmation-box">
        <div class="success-icon">✅</div>
        <h2>Réservation confirmée !</h2>

        <p>Bonjour <strong><?= htmlspecialchars($reservation->prenom . ' ' . $reservation->nom, ENT_QUOTES, 'UTF-8') ?></strong>,</p>
        <p>Votre réservation pour <strong><?= htmlspecialchars($representation['piece_titre'], ENT_QUOTES, 'UTF-8') ?></strong>
        le <strong><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($representation['date_debut'])), ENT_QUOTES, 'UTF-8') ?></strong>
        a bien été enregistrée.</p>

        <div class="reservation-details">
            <p><strong>Référence :</strong> <code><?= htmlspecialchars($reservation->id, ENT_QUOTES, 'UTF-8') ?></code></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></p>

            <?php if (!empty($places)): ?>
            <h4>Places réservées :</h4>
            <table class="places-table">
                <thead>
                    <tr><th>Catégorie</th><th>Quantité</th><th>Prix unitaire</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($places as $place): ?>
                    <?php $sous_total = $place['quantite'] * $place['prix_unitaire']; $total += $sous_total; ?>
                    <tr>
                        <td><?= htmlspecialchars($place['categorie'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $place['quantite'] ?></td>
                        <td><?= number_format((float) $place['prix_unitaire'], 2, ',', ' ') ?> €</td>
                        <td><?= number_format($sous_total, 2, ',', ' ') ?> €</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="3"><strong>Total</strong></td><td><strong><?= number_format($total, 2, ',', ' ') ?> €</strong></td></tr>
                </tfoot>
            </table>
            <?php endif; ?>
        </div>

        <p class="email-notice">Un email de confirmation vous a été envoyé à l'adresse indiquée.</p>

        <div class="annulation-section">
            <p>Pour annuler votre réservation :</p>
            <a href="/reservation/annuler/<?= urlencode($reservation->id) ?>" class="btn btn-danger">
                Annuler ma réservation
            </a>
        </div>

        <div class="actions">
            <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
