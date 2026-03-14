<?php require VIEW_PATH . '/layouts/header.php'; ?>

<article class="piece-detail">
    <div class="piece-header">
        <?php if (!empty($piece->afficheVignette)): ?>
            <img src="<?= htmlspecialchars($piece->afficheVignette, ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($piece->titre, ENT_QUOTES, 'UTF-8') ?>"
                 class="piece-affiche">
        <?php endif; ?>
        <div class="piece-meta">
            <h2><?= htmlspecialchars($piece->titre, ENT_QUOTES, 'UTF-8') ?></h2>
            <?php if (!empty($piece->auteur)): ?>
                <p><strong>Auteur :</strong> <?= htmlspecialchars($piece->auteur, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if (!empty($piece->type)): ?>
                <p><strong>Type :</strong> <?= htmlspecialchars($piece->type, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if (!empty($piece->dureeMinutes)): ?>
                <p><strong>Durée :</strong> <?= (int) $piece->dureeMinutes ?> minutes</p>
            <?php endif; ?>
            <?php if ($piece->ageMinimum > 0): ?>
                <p><strong>Âge minimum :</strong> <?= (int) $piece->ageMinimum ?> ans</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($piece->synopsis)): ?>
    <section class="synopsis">
        <h3>Synopsis</h3>
        <p><?= nl2br(htmlspecialchars($piece->synopsis, ENT_QUOTES, 'UTF-8')) ?></p>
    </section>
    <?php endif; ?>
</article>

<!-- Représentations -->
<section class="representations">
    <h3>Représentations</h3>
    <?php if (empty($representations)): ?>
        <p>Aucune représentation programmée.</p>
    <?php else: ?>
        <div class="rep-list">
            <?php foreach ($representations as $rep): ?>
            <div class="rep-item <?= $rep['annulee'] ? 'annulee' : '' ?>">
                <div class="rep-date">
                    <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($rep['annulee']): ?>
                        <span class="badge annule">Annulée</span>
                    <?php endif; ?>
                </div>
                <div class="rep-lieu">
                    📍 <?= htmlspecialchars($rep['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php if ($rep['gratuit']): ?>
                    <span class="badge gratuit">Gratuit</span>
                <?php endif; ?>
                <?php if (!$rep['annulee'] && strtotime($rep['date_debut']) > time()): ?>
                    <a href="/reservation/<?= (int) $rep['id'] ?>" class="btn btn-primary btn-sm">Réserver</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Commentaires -->
<?php if (!empty($commentaires)): ?>
<section class="commentaires">
    <h3>Avis spectateurs</h3>
    <?php foreach ($commentaires as $c): ?>
    <div class="commentaire">
        <div class="commentaire-header">
            <strong><?= htmlspecialchars($c->nom, ENT_QUOTES, 'UTF-8') ?></strong>
            <?php if ($c->note): ?>
                <span class="note"><?= str_repeat('⭐', (int) $c->note) ?></span>
            <?php endif; ?>
            <span class="date"><?= htmlspecialchars(date('d/m/Y', strtotime($c->dateCreation)), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <p><?= nl2br(htmlspecialchars($c->commentaire, ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<div class="actions">
    <a href="/pieces" class="btn btn-secondary">← Retour aux pièces</a>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
