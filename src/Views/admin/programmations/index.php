<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<div class="admin-actions-bar">
    <a href="/admin/programmations/create" class="btn btn-primary">+ Ajouter une programmation</a>
</div>

<?php if (empty($programmations)): ?>
    <p>Aucune programmation enregistrée.</p>
<?php else: ?>
<table class="table">
    <thead>
        <tr><th>Nom</th><th>Du</th><th>Au</th><th>Statut</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($programmations as $prog): ?>
        <tr>
            <td><?= htmlspecialchars($prog->nom, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($prog->dateDebut)), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($prog->dateFin)), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $prog->isActive() ? '<span class="badge active">En cours</span>' : '' ?></td>
            <td class="actions">
                <a href="/admin/programmations/<?= (int) $prog->id ?>/edit" class="btn btn-sm btn-secondary">Modifier</a>
                <form method="POST" action="/admin/programmations/<?= (int) $prog->id ?>/delete"
                      style="display:inline"
                      onsubmit="return confirm('Supprimer cette programmation ?')">
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
