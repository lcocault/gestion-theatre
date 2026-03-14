<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= $programmation ? '/admin/programmations/' . (int) $programmation->id . '/update' : '/admin/programmations/store' ?>">
    <?= $csrfField ?>

    <div class="form-group">
        <label for="nom">Nom <span class="required">*</span></label>
        <input type="text" id="nom" name="nom" required maxlength="255"
               value="<?= htmlspecialchars($programmation ? $programmation->nom : ($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="date_debut">Date de début <span class="required">*</span></label>
            <input type="date" id="date_debut" name="date_debut" required
                   value="<?= htmlspecialchars($programmation ? $programmation->dateDebut : ($_POST['date_debut'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label for="date_fin">Date de fin <span class="required">*</span></label>
            <input type="date" id="date_fin" name="date_fin" required
                   value="<?= htmlspecialchars($programmation ? $programmation->dateFin : ($_POST['date_fin'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="affiche_vignette">URL affiche / vignette</label>
        <input type="url" id="affiche_vignette" name="affiche_vignette" maxlength="500"
               value="<?= htmlspecialchars($programmation ? ($programmation->afficheVignette ?? '') : ($_POST['affiche_vignette'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-group">
        <label>Représentations associées</label>
        <div class="representation-checkboxes">
            <?php foreach ($representations as $rep): ?>
            <label class="checkbox-item">
                <input type="checkbox" name="representations[]"
                       value="<?= (int) $rep['id'] ?>"
                    <?= in_array((string) $rep['id'], array_map('strval', $selected), true) ? 'checked' : '' ?>>
                <?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?>
                – <?= htmlspecialchars(date('d/m/Y H:i', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($rep['lieu_nom'])): ?>
                    (<?= htmlspecialchars($rep['lieu_nom'], ENT_QUOTES, 'UTF-8') ?>)
                <?php endif; ?>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $programmation ? 'Enregistrer' : 'Créer' ?></button>
        <a href="/admin/programmations" class="btn btn-secondary">Annuler</a>
    </div>
</form>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
