<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Commentaire;

class CommentaireRepository extends BaseRepository
{
    public function findByRepresentationId(int $representationId, bool $valideOnly = true): array
    {
        $sql = 'SELECT * FROM commentaire WHERE representation_id = :id';
        if ($valideOnly) {
            $sql .= ' AND valide = TRUE';
        }
        $sql .= ' ORDER BY date_creation DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $representationId]);
        return array_map(fn($row) => Commentaire::fromArray($row), $stmt->fetchAll());
    }

    public function save(Commentaire $comment): Commentaire
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO commentaire (representation_id, nom, note, commentaire)
             VALUES (:representation_id, :nom, :note, :commentaire)
             RETURNING id, date_creation'
        );
        $stmt->execute([
            'representation_id' => $comment->representationId,
            'nom'               => $comment->nom,
            'note'              => $comment->note,
            'commentaire'       => $comment->commentaire,
        ]);
        $row = $stmt->fetch();
        return new Commentaire(
            $row['id'],
            $comment->representationId,
            $comment->nom,
            $comment->note,
            $comment->commentaire,
            $row['date_creation'],
            false,
        );
    }

    public function valider(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE commentaire SET valide = TRUE WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM commentaire WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
