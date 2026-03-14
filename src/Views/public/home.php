<?php require VIEW_PATH . '/layouts/header.php'; ?>

<!-- Carrousel des représentations à venir -->
<?php if (!empty($upcomingRepresentations)): ?>
<section class="carousel">
    <h2>Prochaines représentations</h2>
    <div class="carousel-wrapper">
        <?php foreach (array_slice($upcomingRepresentations, 0, 6) as $rep): ?>
        <div class="carousel-item">
            <?php if (!empty($rep['affiche_vignette'])): ?>
                <img src="<?= htmlspecialchars($rep['affiche_vignette'], ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>
                <div class="no-image">🎭</div>
            <?php endif; ?>
            <div class="carousel-info">
                <h3><?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="date"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?></p>
                <p class="lieu"><?= htmlspecialchars($rep['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                <?php if ($rep['gratuit']): ?>
                    <span class="badge gratuit">Gratuit</span>
                <?php endif; ?>
                <a href="/reservation/<?= (int) $rep['id'] ?>" class="btn btn-primary">Réserver</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Programmations en cours -->
<?php if (!empty($activeProgrammations)): ?>
<section class="programmations-actives">
    <h2>Programmations en cours</h2>
    <div class="cards">
        <?php foreach ($activeProgrammations as $prog): ?>
        <div class="card">
            <?php if (!empty($prog->afficheVignette)): ?>
                <img src="<?= htmlspecialchars($prog->afficheVignette, ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($prog->nom, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>
            <div class="card-body">
                <h3><?= htmlspecialchars($prog->nom, ENT_QUOTES, 'UTF-8') ?></h3>
                <p>Du <?= htmlspecialchars(date('d/m/Y', strtotime($prog->dateDebut)), ENT_QUOTES, 'UTF-8') ?>
                   au <?= htmlspecialchars(date('d/m/Y', strtotime($prog->dateFin)), ENT_QUOTES, 'UTF-8') ?></p>
                <a href="/programmations/<?= (int) $prog->id ?>" class="btn btn-secondary">Voir le programme</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (empty($upcomingRepresentations) && empty($activeProgrammations)): ?>
<section class="empty-state">
    <p>Aucune représentation programmée pour le moment. Revenez bientôt !</p>
</section>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
