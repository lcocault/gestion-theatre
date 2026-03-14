<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Programmation;

class ProgrammationRepository extends BaseRepository
{
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM programmation ORDER BY date_debut DESC');
        return array_map(fn($row) => Programmation::fromArray($row), $stmt->fetchAll());
    }

    public function findActive(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM programmation WHERE date_debut <= CURRENT_DATE AND date_fin >= CURRENT_DATE ORDER BY nom"
        );
        $stmt->execute();
        return array_map(fn($row) => Programmation::fromArray($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Programmation
    {
        $stmt = $this->pdo->prepare('SELECT * FROM programmation WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Programmation::fromArray($row) : null;
    }

    public function getRepresentations(int $programmationId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, p.titre as piece_titre, p.affiche_vignette, l.nom as lieu_nom
             FROM programmation_representation pr
             JOIN representation r ON r.id = pr.representation_id
             LEFT JOIN piece p ON p.id = r.piece_id
             LEFT JOIN lieu l ON l.id = r.lieu_id
             WHERE pr.programmation_id = :id
             ORDER BY r.date_debut ASC'
        );
        $stmt->execute(['id' => $programmationId]);
        return $stmt->fetchAll();
    }

    public function save(Programmation $prog): Programmation
    {
        $data = [
            'nom'              => $prog->nom,
            'date_debut'       => $prog->dateDebut,
            'date_fin'         => $prog->dateFin,
            'affiche_vignette' => $prog->afficheVignette,
        ];

        if ($prog->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO programmation (nom, date_debut, date_fin, affiche_vignette)
                 VALUES (:nom, :date_debut, :date_fin, :affiche_vignette)
                 RETURNING id'
            );
            $stmt->execute($data);
            $id = (int) $stmt->fetchColumn();
            return new Programmation($id, $prog->nom, $prog->dateDebut, $prog->dateFin, $prog->afficheVignette);
        }

        $data['id'] = $prog->id;
        $stmt = $this->pdo->prepare(
            'UPDATE programmation SET nom=:nom, date_debut=:date_debut, date_fin=:date_fin,
             affiche_vignette=:affiche_vignette, updated_at=NOW() WHERE id=:id'
        );
        $stmt->execute($data);
        return $prog;
    }

    public function saveRepresentations(int $programmationId, array $representationIds): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM programmation_representation WHERE programmation_id = :id'
        );
        $stmt->execute(['id' => $programmationId]);

        $stmt = $this->pdo->prepare(
            'INSERT INTO programmation_representation (programmation_id, representation_id) VALUES (:pid, :rid)'
        );
        foreach ($representationIds as $rid) {
            $stmt->execute(['pid' => $programmationId, 'rid' => (int) $rid]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM programmation WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
