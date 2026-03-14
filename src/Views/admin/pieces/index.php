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
                <a href="/admin/pieces/<?= (int) $piece->id ?>/edit" class="btn btn-sm btn-secondary">Modifier</a>
                <form method="POST" action="/admin/pieces/<?= (int) $piece->id ?>/delete"
                      style="display:inline"
                      onsubmit="return confirm('Supprimer cette pièce ?')">
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
