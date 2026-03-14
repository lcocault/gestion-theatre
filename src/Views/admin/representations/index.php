<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<div class="admin-actions-bar">
    <a href="/admin/representations/create" class="btn btn-primary">+ Ajouter une représentation</a>
</div>

<?php if (empty($representations)): ?>
    <p>Aucune représentation enregistrée.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr><th>Pièce</th><th>Date</th><th>Lieu</th><th>Max places</th><th>Statut</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($representations as $rep): ?>
        <tr class="<?= $rep['annulee'] ? 'row-annule' : '' ?>">
            <td><?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($rep['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $rep['max_spectateurs'] ?></td>
            <td>
                <?php if ($rep['annulee']): ?>
                    <span class="badge annule">Annulée</span>
                <?php elseif ($rep['gratuit']): ?>
                    <span class="badge gratuit">Gratuit</span>
                <?php else: ?>
                    <span class="badge active">Active</span>
                <?php endif; ?>
            </td>
            <td class="actions">
                <a href="/admin/representations/<?= (int) $rep['id'] ?>/reservations"
                   class="btn btn-sm btn-secondary">Réservations</a>
                <a href="/admin/representations/<?= (int) $rep['id'] ?>/edit"
                   class="btn btn-sm btn-secondary">Modifier</a>
                <?php if (!$rep['annulee']): ?>
                <form method="POST"
                      action="/admin/representations/<?= (int) $rep['id'] ?>/annuler"
                      style="display:inline">
                    <?= $csrfField ?>
                    <button type="submit" class="btn btn-sm btn-warning"
                            onclick="return confirm('Annuler cette représentation et prévenir tous les spectateurs ?')">
                        Annuler
                    </button>
                </form>
                <?php endif; ?>
                <form method="POST"
                      action="/admin/representations/<?= (int) $rep['id'] ?>/delete"
                      style="display:inline"
                      onsubmit="return confirm('Supprimer définitivement cette représentation ?')">
                    <?= $csrfField ?>
                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
