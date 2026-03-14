<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<div class="admin-actions-bar">
    <a href="/admin/troupes/create" class="btn btn-primary">+ Ajouter une troupe</a>
</div>

<?php if (empty($troupes)): ?>
    <p>Aucune troupe enregistrée.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr><th>Nom</th><th>Email contact</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($troupes as $troupe): ?>
        <tr>
            <td><?= htmlspecialchars($troupe->nom, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($troupe->emailContact ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            <td class="actions">
                <a href="/admin/troupes/<?= (int) $troupe->id ?>/edit" class="btn btn-sm btn-secondary">Modifier</a>
                <form method="POST" action="/admin/troupes/<?= (int) $troupe->id ?>/delete"
                      style="display:inline"
                      onsubmit="return confirm('Supprimer cette troupe ?')">
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
