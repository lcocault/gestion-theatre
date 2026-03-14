<?php require VIEW_PATH . '/layouts/header.php'; ?>

<article class="programmation-detail">
    <?php if (!empty($programmation->afficheVignette)): ?>
        <img src="<?= htmlspecialchars($programmation->afficheVignette, ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($programmation->nom, ENT_QUOTES, 'UTF-8') ?>"
             class="programmation-affiche">
    <?php endif; ?>
    <h2><?= htmlspecialchars($programmation->nom, ENT_QUOTES, 'UTF-8') ?></h2>
    <p>Du <?= htmlspecialchars(date('d/m/Y', strtotime($programmation->dateDebut)), ENT_QUOTES, 'UTF-8') ?>
       au <?= htmlspecialchars(date('d/m/Y', strtotime($programmation->dateFin)), ENT_QUOTES, 'UTF-8') ?></p>
</article>

<section class="representations">
    <h3>Représentations au programme</h3>
    <?php if (empty($representations)): ?>
        <p>Aucune représentation dans cette programmation.</p>
    <?php else: ?>
        <?php
            $now = time();
            $futures = array_filter($representations, fn($r) => strtotime($r['date_debut']) >= $now);
            $passees = array_filter($representations, fn($r) => strtotime($r['date_debut']) < $now);
        ?>
        <?php if (!empty($futures)): ?>
        <h4>À venir</h4>
        <div class="rep-list">
            <?php foreach ($futures as $rep): ?>
            <div class="rep-item <?= $rep['annulee'] ? 'annulee' : '' ?>">
                <?php if (!empty($rep['affiche_vignette'])): ?>
                    <img src="<?= htmlspecialchars($rep['affiche_vignette'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="" class="rep-thumb">
                <?php endif; ?>
                <div class="rep-info">
                    <strong><?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <span>Le <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?></span>
                    <span>📍 <?= htmlspecialchars($rep['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if ($rep['gratuit']): ?>
                        <span class="badge gratuit">Gratuit</span>
                    <?php endif; ?>
                    <?php if (!$rep['annulee']): ?>
                        <a href="/reservation/<?= (int) $rep['id'] ?>" class="btn btn-primary btn-sm">Réserver</a>
                    <?php else: ?>
                        <span class="badge annule">Annulée</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($passees)): ?>
        <h4>Passées</h4>
        <div class="rep-list">
            <?php foreach ($passees as $rep): ?>
            <div class="rep-item passee">
                <strong><?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?></strong>
                – <?= htmlspecialchars(date('d/m/Y', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?>
                – <?= htmlspecialchars($rep['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<div class="actions">
    <a href="/programmations" class="btn btn-secondary">← Retour aux programmations</a>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
