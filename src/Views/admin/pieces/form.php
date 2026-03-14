<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= $piece ? '/admin/pieces/' . (int) $piece->id . '/update' : '/admin/pieces/store' ?>">
    <?= $csrfField ?>

    <div class="form-group">
        <label for="titre">Titre <span class="required">*</span></label>
        <input type="text" id="titre" name="titre" required maxlength="255"
               value="<?= htmlspecialchars($piece ? $piece->titre : ($_POST['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-group">
        <label for="auteur">Auteur</label>
        <input type="text" id="auteur" name="auteur" maxlength="255"
               value="<?= htmlspecialchars($piece ? ($piece->auteur ?? '') : ($_POST['auteur'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-group">
        <label for="synopsis">Synopsis</label>
        <textarea id="synopsis" name="synopsis" rows="5"><?= htmlspecialchars(
            $piece ? ($piece->synopsis ?? '') : ($_POST['synopsis'] ?? ''),
            ENT_QUOTES, 'UTF-8'
        ) ?></textarea>
    </div>

    <div class="form-group">
        <label for="troupe_id">Troupe</label>
        <select id="troupe_id" name="troupe_id">
            <option value="">-- Choisir une troupe --</option>
            <?php foreach ($troupes as $t): ?>
                <option value="<?= (int) $t->id ?>"
                    <?= ($piece ? $piece->troupeId : ($_POST['troupe_id'] ?? '')) == $t->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t->nom, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="type">Type</label>
            <input type="text" id="type" name="type" maxlength="100"
                   value="<?= htmlspecialchars($piece ? ($piece->type ?? '') : ($_POST['type'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Comédie, Drame, Musical...">
        </div>
        <div class="form-group">
            <label for="duree_minutes">Durée (minutes)</label>
            <input type="number" id="duree_minutes" name="duree_minutes" min="1"
                   value="<?= (int) ($piece ? ($piece->dureeMinutes ?? 0) : ($_POST['duree_minutes'] ?? 0)) ?: '' ?>">
        </div>
        <div class="form-group">
            <label for="age_minimum">Âge minimum</label>
            <input type="number" id="age_minimum" name="age_minimum" min="0"
                   value="<?= (int) ($piece ? $piece->ageMinimum : ($_POST['age_minimum'] ?? 0)) ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="affiche_vignette">URL affiche / vignette</label>
        <input type="url" id="affiche_vignette" name="affiche_vignette" maxlength="500"
               value="<?= htmlspecialchars($piece ? ($piece->afficheVignette ?? '') : ($_POST['affiche_vignette'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $piece ? 'Enregistrer' : 'Créer' ?></button>
        <a href="/admin/pieces" class="btn btn-secondary">Annuler</a>
    </div>
</form>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
