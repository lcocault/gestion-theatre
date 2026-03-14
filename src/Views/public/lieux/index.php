<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="page-header">
    <h2>Les lieux</h2>
</div>

<?php if (empty($lieux)): ?>
    <p>Aucun lieu disponible pour le moment.</p>
<?php else: ?>
    <div class="cards">
        <?php foreach ($lieux as $lieu): ?>
        <div class="card">
            <div class="card-body">
                <h3>📍 <?= htmlspecialchars($lieu->nom, ENT_QUOTES, 'UTF-8') ?></h3>
                <a href="/lieux/<?= (int) $lieu->id ?>" class="btn btn-secondary">Voir les représentations</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
