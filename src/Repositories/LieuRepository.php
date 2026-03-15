<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Lieu;

class LieuRepository extends BaseRepository
{
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM lieu ORDER BY nom');
        return array_map(fn($row) => Lieu::fromArray($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Lieu
    {
        $stmt = $this->pdo->prepare('SELECT * FROM lieu WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Lieu::fromArray($row) : null;
    }

    public function save(Lieu $lieu): Lieu
    {
        if ($lieu->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO lieu (nom, adresse, plan_acces) VALUES (:nom, :adresse, :plan_acces) RETURNING id'
            );
            $stmt->execute([
                'nom'        => $lieu->nom,
                'adresse'    => $lieu->adresse,
                'plan_acces' => $lieu->planAcces,
            ]);
            $id = (int) $stmt->fetchColumn();
            return new Lieu($id, $lieu->nom, $lieu->adresse, $lieu->planAcces);
        }

        $stmt = $this->pdo->prepare(
            'UPDATE lieu SET nom = :nom, adresse = :adresse, plan_acces = :plan_acces, updated_at = NOW() WHERE id = :id'
        );
        $stmt->execute([
            'nom'        => $lieu->nom,
            'adresse'    => $lieu->adresse,
            'plan_acces' => $lieu->planAcces,
            'id'         => $lieu->id,
        ]);
        return $lieu;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM lieu WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
