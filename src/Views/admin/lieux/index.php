<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<div class="admin-actions-bar">
    <a href="/admin/lieux/create" class="btn btn-primary">+ Ajouter un lieu</a>
</div>

<?php if (empty($lieux)): ?>
    <p>Aucun lieu enregistré.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr><th>Nom</th><th>Plan d'accès</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($lieux as $lieu): ?>
        <tr>
            <td><?= htmlspecialchars($lieu->nom, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $lieu->planAcces ? '✓' : '—' ?></td>
            <td class="actions">
                <a href="/admin/lieux/<?= (int) $lieu->id ?>/edit" class="btn btn-sm btn-secondary">Modifier</a>
                <form method="POST" action="/admin/lieux/<?= (int) $lieu->id ?>/delete"
                      style="display:inline"
                      onsubmit="return confirm('Supprimer ce lieu ?')">
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
