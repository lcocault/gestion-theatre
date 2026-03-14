<?php require VIEW_PATH . '/layouts/header.php'; ?>

<article class="reservation-form-page">
    <h2>Réserver pour : <?= htmlspecialchars($representation['piece_titre'], ENT_QUOTES, 'UTF-8') ?></h2>

    <div class="rep-summary">
        <p><strong>Date :</strong> <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($representation['date_debut'])), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Lieu :</strong> <?= htmlspecialchars($representation['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        <?php if ($representation['gratuit']): ?>
            <p><span class="badge gratuit">Entrée gratuite</span></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="/reservation/<?= (int) $representation['id'] ?>/store" class="reservation-form">
        <h3>Vos coordonnées</h3>

        <div class="form-group">
            <label for="nom">Nom <span class="required">*</span></label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   required maxlength="255">
        </div>

        <div class="form-group">
            <label for="prenom">Prénom <span class="required">*</span></label>
            <input type="text" id="prenom" name="prenom"
                   value="<?= htmlspecialchars($_POST['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   required maxlength="255">
        </div>

        <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   required maxlength="255">
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone (optionnel)</label>
            <input type="tel" id="telephone" name="telephone"
                   value="<?= htmlspecialchars($_POST['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   maxlength="30">
        </div>

        <div class="form-group">
            <label for="source_decouverte">Comment avez-vous connu ce spectacle ?</label>
            <select id="source_decouverte" name="source_decouverte">
                <option value="">-- Choisir --</option>
                <?php foreach (['Affiche', 'Réseau social', 'Bouche à oreille', 'Presse', 'Site web', 'Autre'] as $s): ?>
                    <option value="<?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($_POST['source_decouverte'] ?? '') === $s ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <h3>Accessibilité</h3>
        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="handicap_visuel_auditif" value="1"
                    <?= !empty($_POST['handicap_visuel_auditif']) ? 'checked' : '' ?>>
                Besoin d'adaptation pour handicap visuel ou auditif
            </label>
        </div>
        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="handicap_moteur" value="1"
                    <?= !empty($_POST['handicap_moteur']) ? 'checked' : '' ?>>
                Besoin d'adaptation pour handicap moteur
            </label>
        </div>

        <h3>Nombre de places</h3>
        <?php if ($representation['gratuit']): ?>
            <div class="form-group">
                <label for="places_libre">Nombre de places (entrée libre)</label>
                <input type="number" id="places_libre" name="places_libre"
                       value="<?= (int) ($_POST['places_libre'] ?? 1) ?>"
                       min="1" max="20">
            </div>
        <?php elseif (!empty($prix)): ?>
            <?php foreach ($prix as $p): ?>
            <div class="form-group">
                <label for="places_<?= (int) $p['id'] ?>">
                    <?= htmlspecialchars($p['categorie'], ENT_QUOTES, 'UTF-8') ?>
                    – <?= number_format((float) $p['prix'], 2, ',', ' ') ?> €
                </label>
                <input type="number" id="places_<?= (int) $p['id'] ?>"
                       name="places_<?= (int) $p['id'] ?>"
                       value="<?= (int) ($_POST['places_' . $p['id']] ?? 0) ?>"
                       min="0" max="20">
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune tarification définie pour cette représentation.</p>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Confirmer ma réservation</button>
            <a href="/" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</article>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
