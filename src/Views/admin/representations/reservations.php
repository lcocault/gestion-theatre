<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<div class="admin-actions-bar">
    <a href="/admin/representations" class="btn btn-secondary">← Retour aux représentations</a>
</div>

<div class="rep-info">
    <p><strong>Pièce :</strong> <?= htmlspecialchars($representation['piece_titre'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Date :</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($representation['date_debut'])), ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Lieu :</strong> <?= htmlspecialchars($representation['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<?php if (empty($reservations)): ?>
    <p>Aucune réservation pour cette représentation.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Places</th>
            <th>Total</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $res): ?>
        <tr class="statut-<?= htmlspecialchars($res['statut'], ENT_QUOTES, 'UTF-8') ?>">
            <td><?= htmlspecialchars($res['nom'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($res['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($res['email'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $res['total_places'] ?></td>
            <td><?= number_format((float) $res['total_prix'], 2, ',', ' ') ?> €</td>
            <td><span class="badge badge-<?= htmlspecialchars($res['statut'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($res['statut'], ENT_QUOTES, 'UTF-8') ?></span></td>
            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($res['date_creation'])), ENT_QUOTES, 'UTF-8') ?></td>
            <td class="actions">
                <?php if ($res['statut'] !== 'confirme'): ?>
                <form method="POST"
                      action="/admin/representations/<?= (int) $representation['id'] ?>/reservations/<?= urlencode($res['id']) ?>/confirmer"
                      style="display:inline">
                    <?= $csrfField ?>
                    <button type="submit" class="btn btn-sm btn-success">Confirmer</button>
                </form>
                <?php endif; ?>
                <?php if ($res['statut'] !== 'annule'): ?>
                <form method="POST"
                      action="/admin/representations/<?= (int) $representation['id'] ?>/reservations/<?= urlencode($res['id']) ?>/annuler"
                      style="display:inline"
                      onsubmit="return confirm('Annuler cette réservation ?')">
                    <?= $csrfField ?>
                    <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
