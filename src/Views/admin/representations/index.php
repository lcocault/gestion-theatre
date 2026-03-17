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
                   class="btn btn-sm btn-secondary btn-icon"
                   data-tooltip="Réservations"
                   aria-label="Réservations">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                </a>
                <a href="/admin/representations/<?= (int) $rep['id'] ?>/edit"
                   class="btn btn-sm btn-secondary btn-icon"
                   data-tooltip="Modifier"
                   aria-label="Modifier">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                </a>
                <?php if (!$rep['annulee']): ?>
                <form method="POST"
                      action="/admin/representations/<?= (int) $rep['id'] ?>/annuler">
                    <?= $csrfField ?>
                    <button type="submit"
                            class="btn btn-sm btn-warning btn-icon"
                            data-tooltip="Annuler"
                            aria-label="Annuler"
                            onclick="return confirm('Annuler cette représentation et prévenir tous les spectateurs ?')">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    </button>
                </form>
                <?php endif; ?>
                <form method="POST"
                      action="/admin/representations/<?= (int) $rep['id'] ?>/delete"
                      onsubmit="return confirm('Supprimer définitivement cette représentation ?')">
                    <?= $csrfField ?>
                    <button type="submit"
                            class="btn btn-sm btn-danger btn-icon"
                            data-tooltip="Supprimer"
                            aria-label="Supprimer">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
