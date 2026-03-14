<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="page-header">
    <h2>Programmations</h2>
</div>

<?php if (empty($programmations)): ?>
    <p>Aucune programmation disponible.</p>
<?php else: ?>
    <div class="cards">
        <?php foreach ($programmations as $prog): ?>
        <div class="card <?= $prog->isActive() ? 'active' : '' ?>">
            <?php if (!empty($prog->afficheVignette)): ?>
                <img src="<?= htmlspecialchars($prog->afficheVignette, ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($prog->nom, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>
            <div class="card-body">
                <h3><?= htmlspecialchars($prog->nom, ENT_QUOTES, 'UTF-8') ?></h3>
                <p>Du <?= htmlspecialchars(date('d/m/Y', strtotime($prog->dateDebut)), ENT_QUOTES, 'UTF-8') ?>
                   au <?= htmlspecialchars(date('d/m/Y', strtotime($prog->dateFin)), ENT_QUOTES, 'UTF-8') ?></p>
                <?php if ($prog->isActive()): ?>
                    <span class="badge active">En cours</span>
                <?php endif; ?>
                <a href="/programmations/<?= (int) $prog->id ?>" class="btn btn-secondary">Voir le programme</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
