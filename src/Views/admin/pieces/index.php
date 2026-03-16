<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<div class="admin-actions-bar">
    <a href="/admin/pieces/create" class="btn btn-primary">+ Ajouter une pièce</a>
</div>

<?php if (empty($pieces)): ?>
    <p>Aucune pièce enregistrée.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr><th>Titre</th><th>Auteur</th><th>Type</th><th>Durée</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($pieces as $piece): ?>
        <tr>
            <td><?= htmlspecialchars($piece->titre, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($piece->auteur ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($piece->type ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $piece->dureeMinutes ? (int) $piece->dureeMinutes . ' min' : '—' ?></td>
            <td class="actions">
                <a href="/admin/pieces/<?= (int) $piece->id ?>/edit"
                   class="btn btn-sm btn-secondary btn-icon"
                   data-tooltip="Modifier"
                   aria-label="Modifier">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                </a>
                <form method="POST" action="/admin/pieces/<?= (int) $piece->id ?>/delete"
                      style="display:inline"
                      onsubmit="return confirm('Supprimer cette pièce ?')">
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
