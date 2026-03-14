/**
 * Gestion Théâtre – JavaScript Administration
 */

document.addEventListener('DOMContentLoaded', function () {
    // ─── Ajout de lignes de tarif dynamiques ──────────────────────
    const addPrixBtn = document.getElementById('add-prix');
    const prixContainer = document.getElementById('prix-container');

    if (addPrixBtn && prixContainer) {
        addPrixBtn.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'prix-row';
            row.innerHTML = `
                <input type="text" name="prix_categorie[]" placeholder="Catégorie (ex: Adulte)">
                <input type="number" name="prix_montant[]" placeholder="Prix (€)" min="0" step="0.01" value="0.00">
                <button type="button" class="btn btn-sm btn-danger remove-prix">✕</button>
            `;
            prixContainer.appendChild(row);
            bindRemovePrix(row.querySelector('.remove-prix'));
        });

        // Lier les boutons de suppression existants
        document.querySelectorAll('.remove-prix').forEach(bindRemovePrix);
    }

    function bindRemovePrix(btn) {
        if (!btn) return;
        btn.addEventListener('click', function () {
            btn.closest('.prix-row').remove();
        });
    }
});
