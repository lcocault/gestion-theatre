<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="page-header">
    <h2>Les pièces</h2>
</div>

<?php if (empty($pieces)): ?>
    <p>Aucune pièce disponible pour le moment.</p>
<?php else: ?>
    <div class="cards">
        <?php foreach ($pieces as $piece): ?>
        <div class="card">
            <?php if (!empty($piece->afficheVignette)): ?>
                <img src="<?= htmlspecialchars($piece->afficheVignette, ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($piece->titre, ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>
                <div class="no-image">🎭</div>
            <?php endif; ?>
            <div class="card-body">
                <h3><?= htmlspecialchars($piece->titre, ENT_QUOTES, 'UTF-8') ?></h3>
                <?php if (!empty($piece->auteur)): ?>
                    <p class="auteur">de <?= htmlspecialchars($piece->auteur, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <?php if (!empty($piece->type)): ?>
                    <span class="badge"><?= htmlspecialchars($piece->type, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
                <?php if (!empty($piece->dureeMinutes)): ?>
                    <p class="duree">Durée : <?= (int) $piece->dureeMinutes ?> min</p>
                <?php endif; ?>
                <a href="/pieces/<?= (int) $piece->id ?>" class="btn btn-secondary">Voir la fiche</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
