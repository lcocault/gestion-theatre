<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= $troupe ? '/admin/troupes/' . (int) $troupe->id . '/update' : '/admin/troupes/store' ?>">
    <?= $csrfField ?>

    <div class="form-group">
        <label for="nom">Nom <span class="required">*</span></label>
        <input type="text" id="nom" name="nom" required maxlength="255"
               value="<?= htmlspecialchars($troupe ? $troupe->nom : ($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-group">
        <label for="email_contact">Email de contact</label>
        <input type="email" id="email_contact" name="email_contact" maxlength="255"
               value="<?= htmlspecialchars($troupe ? ($troupe->emailContact ?? '') : ($_POST['email_contact'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $troupe ? 'Enregistrer' : 'Créer' ?></button>
        <a href="/admin/troupes" class="btn btn-secondary">Annuler</a>
    </div>
</form>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
