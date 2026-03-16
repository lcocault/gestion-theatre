<?php require VIEW_PATH . '/layouts/admin_header.php'; ?>

<div class="dashboard">
    <div class="dashboard-cards">
        <div class="dash-card">
            <h3>Représentations à venir</h3>
            <p class="dash-count"><?= count($upcoming) ?></p>
            <a href="/admin/representations" class="btn btn-secondary">Gérer</a>
        </div>
        <div class="dash-card">
            <h3>Programmations actives</h3>
            <p class="dash-count"><?= count($activeProgrammations) ?></p>
            <a href="/admin/programmations" class="btn btn-secondary">Gérer</a>
        </div>
    </div>

    <?php if (!empty($upcoming)): ?>
    <section class="upcoming-reps">
        <h3>Prochaines représentations</h3>
        <table class="table">
            <thead>
                <tr><th>Pièce</th><th>Date</th><th>Lieu</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($upcoming, 0, 10) as $rep): ?>
                <tr>
                    <td><?= htmlspecialchars($rep['piece_titre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($rep['date_debut'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($rep['lieu_nom'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="/admin/representations/<?= (int) $rep['id'] ?>/reservations"
                           class="btn btn-sm btn-secondary btn-icon"
                           data-tooltip="Réservations"
                           aria-label="Réservations">
                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/admin_footer.php'; ?>
