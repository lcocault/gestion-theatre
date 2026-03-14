<?php require VIEW_PATH . '/layouts/header.php'; ?>

<article class="lieu-detail">
    <h2>📍 <?= htmlspecialchars($lieu->nom, ENT_QUOTES, 'UTF-8') ?></h2>

    <?php if (!empty($lieu->planAcces)): ?>
    <section class="plan-acces">
        <h3>Plan d'accès</h3>
        <div class="plan-content">
            <?= nl2br(htmlspecialchars($lieu->planAcces, ENT_QUOTES, 'UTF-8')) ?>
        </div>
    </section>
    <?php endif; ?>
</article>

<section class="representations">
    <h3>Représentations dans ce lieu</h3>
    <?php if (empty($representations)): ?>
        <p>Aucune représentation dans ce lieu.</p>
    <?php else: ?>
        <?php
            $now = time();
            $passees = array_filter($representations, fn($r) => strtotime($r['date_debut']) < $now);
            $futures = array_filter($representations, fn($r) => strtotime($r['date_debut']) >= $now);
        ?>
        <?php if (!empty($futures)): ?>
        <h4>Représentations à venir</h4>
        <div class="rep-list">
            <?php foreach ($futures as $rep): ?>
            <div class="rep-item">
                <strong><?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?></strong>
                – <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?>
                <?php if (!$rep['annulee']): ?>
                    <a href="/reservation/<?= (int) $rep['id'] ?>" class="btn btn-primary btn-sm">Réserver</a>
                <?php else: ?>
                    <span class="badge annule">Annulée</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($passees)): ?>
        <h4>Représentations passées</h4>
        <div class="rep-list">
            <?php foreach ($passees as $rep): ?>
            <div class="rep-item passee">
                <strong><?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?></strong>
                – <?= htmlspecialchars(date('d/m/Y', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<div class="actions">
    <a href="/lieux" class="btn btn-secondary">← Retour aux lieux</a>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
