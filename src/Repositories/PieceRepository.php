<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Piece;

class PieceRepository extends BaseRepository
{
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM piece ORDER BY titre');
        return array_map(fn($row) => Piece::fromArray($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Piece
    {
        $stmt = $this->pdo->prepare('SELECT * FROM piece WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Piece::fromArray($row) : null;
    }

    public function findByTroupeId(int $troupeId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM piece WHERE troupe_id = :troupe_id ORDER BY titre');
        $stmt->execute(['troupe_id' => $troupeId]);
        return array_map(fn($row) => Piece::fromArray($row), $stmt->fetchAll());
    }

    public function save(Piece $piece): Piece
    {
        $data = [
            'titre'            => $piece->titre,
            'auteur'           => $piece->auteur,
            'synopsis'         => $piece->synopsis,
            'troupe_id'        => $piece->troupeId,
            'type'             => $piece->type,
            'duree_minutes'    => $piece->dureeMinutes,
            'age_minimum'      => $piece->ageMinimum,
            'affiche_vignette' => $piece->afficheVignette,
        ];

        if ($piece->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO piece (titre, auteur, synopsis, troupe_id, type, duree_minutes, age_minimum, affiche_vignette)
                 VALUES (:titre, :auteur, :synopsis, :troupe_id, :type, :duree_minutes, :age_minimum, :affiche_vignette)
                 RETURNING id'
            );
            $stmt->execute($data);
            $id = (int) $stmt->fetchColumn();
            return new Piece($id, $piece->titre, $piece->auteur, $piece->synopsis,
                $piece->troupeId, $piece->type, $piece->dureeMinutes, $piece->ageMinimum, $piece->afficheVignette);
        }

        $data['id'] = $piece->id;
        $stmt = $this->pdo->prepare(
            'UPDATE piece SET titre=:titre, auteur=:auteur, synopsis=:synopsis, troupe_id=:troupe_id,
             type=:type, duree_minutes=:duree_minutes, age_minimum=:age_minimum,
             affiche_vignette=:affiche_vignette, updated_at=NOW() WHERE id=:id'
        );
        $stmt->execute($data);
        return $piece;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM piece WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
