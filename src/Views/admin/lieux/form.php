<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= $lieu ? '/admin/lieux/' . (int) $lieu->id . '/update' : '/admin/lieux/store' ?>">
    <?= $csrfField ?>

    <div class="form-group">
        <label for="nom">Nom <span class="required">*</span></label>
        <input type="text" id="nom" name="nom" required maxlength="255"
               value="<?= htmlspecialchars($lieu ? $lieu->nom : ($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-group">
        <label for="plan_acces">Plan d'accès</label>
        <textarea id="plan_acces" name="plan_acces" rows="6"><?= htmlspecialchars(
            $lieu ? ($lieu->planAcces ?? '') : ($_POST['plan_acces'] ?? ''),
            ENT_QUOTES, 'UTF-8'
        ) ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $lieu ? 'Enregistrer' : 'Créer' ?>
        </button>
        <a href="/admin/lieux" class="btn btn-secondary">Annuler</a>
    </div>
</form>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
