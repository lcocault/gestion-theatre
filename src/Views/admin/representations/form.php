<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<?php
$action = $representation
    ? '/admin/representations/' . (int) $representation->id . '/update'
    : '/admin/representations/store';
?>
<form method="POST" action="<?= $action ?>">
    <?= $csrfField ?>

    <div class="form-row">
        <div class="form-group">
            <label for="piece_id">Pièce <span class="required">*</span></label>
            <select id="piece_id" name="piece_id" required>
                <option value="">-- Choisir une pièce --</option>
                <?php foreach ($pieces as $p): ?>
                    <option value="<?= (int) $p->id ?>"
                        <?= ($representation ? $representation->pieceId : ($_POST['piece_id'] ?? '')) == $p->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p->titre, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="lieu_id">Lieu</label>
            <select id="lieu_id" name="lieu_id">
                <option value="">-- Choisir un lieu --</option>
                <?php foreach ($lieux as $l): ?>
                    <option value="<?= (int) $l->id ?>"
                        <?= ($representation ? $representation->lieuId : ($_POST['lieu_id'] ?? '')) == $l->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l->nom, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="date_debut">Date de début <span class="required">*</span></label>
            <input type="datetime-local" id="date_debut" name="date_debut" required
                   value="<?= htmlspecialchars(
                       $representation
                           ? date('Y-m-d\TH:i', strtotime($representation->dateDebut))
                           : ($_POST['date_debut'] ?? ''),
                       ENT_QUOTES, 'UTF-8'
                   ) ?>">
        </div>
        <div class="form-group">
            <label for="date_fin">Date de fin</label>
            <input type="datetime-local" id="date_fin" name="date_fin"
                   value="<?= htmlspecialchars(
                       $representation && $representation->dateFin
                           ? date('Y-m-d\TH:i', strtotime($representation->dateFin))
                           : ($_POST['date_fin'] ?? ''),
                       ENT_QUOTES, 'UTF-8'
                   ) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="max_spectateurs">Maximum spectateurs <span class="required">*</span></label>
            <input type="number" id="max_spectateurs" name="max_spectateurs" min="1" required
                   value="<?= (int) ($representation ? $representation->maxSpectateurs : ($_POST['max_spectateurs'] ?? 100)) ?>">
        </div>
        <div class="form-group">
            <label for="date_limite_reservation">Date limite de réservation</label>
            <input type="datetime-local" id="date_limite_reservation" name="date_limite_reservation"
                   value="<?= htmlspecialchars(
                       $representation && $representation->dateLimiteReservation
                           ? date('Y-m-d\TH:i', strtotime($representation->dateLimiteReservation))
                           : ($_POST['date_limite_reservation'] ?? ''),
                       ENT_QUOTES, 'UTF-8'
                   ) ?>">
        </div>
    </div>

    <div class="form-row checkboxes">
        <label>
            <input type="checkbox" name="gratuit" value="1"
                <?= ($representation ? $representation->gratuit : !empty($_POST['gratuit'])) ? 'checked' : '' ?>>
            Entrée gratuite
        </label>
        <label>
            <input type="checkbox" name="annulee" value="1"
                <?= ($representation ? $representation->annulee : !empty($_POST['annulee'])) ? 'checked' : '' ?>>
            Représentation annulée
        </label>
    </div>

    <hr>
    <h3>Tarifs</h3>
    <div id="prix-container">
        <?php foreach ($prix as $i => $p): ?>
        <div class="prix-row" data-index="<?= $i ?>">
            <input type="text" name="prix_categorie[]" placeholder="Catégorie (ex: Adulte)"
                   value="<?= htmlspecialchars($p['categorie'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="number" name="prix_montant[]" placeholder="Prix (€)" min="0" step="0.01"
                   value="<?= number_format((float) $p['prix'], 2, '.', '') ?>">
            <button type="button" class="btn btn-sm btn-danger remove-prix">✕</button>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add-prix" class="btn btn-secondary">+ Ajouter un tarif</button>

    <hr>
    <h3>Modes de paiement acceptés</h3>
    <?php
    $modesDisponibles = ['en_ligne' => 'En ligne', 'cb' => 'Carte bancaire', 'cheque' => 'Chèque', 'especes' => 'Espèces'];
    $selectedModes = array_column($paiements, 'mode');
    ?>
    <div class="checkboxes">
        <?php foreach ($modesDisponibles as $val => $label): ?>
        <label>
            <input type="checkbox" name="paiements[]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"
                <?= in_array($val, $selectedModes, true) ? 'checked' : '' ?>>
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </label>
        <?php endforeach; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $representation ? 'Enregistrer' : 'Créer' ?></button>
        <a href="/admin/representations" class="btn btn-secondary">Annuler</a>
    </div>
</form>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
