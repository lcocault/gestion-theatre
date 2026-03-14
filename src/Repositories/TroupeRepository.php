<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Troupe;

class TroupeRepository extends BaseRepository
{
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM troupe ORDER BY nom');
        return array_map(fn($row) => Troupe::fromArray($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Troupe
    {
        $stmt = $this->pdo->prepare('SELECT * FROM troupe WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Troupe::fromArray($row) : null;
    }

    public function save(Troupe $troupe): Troupe
    {
        if ($troupe->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO troupe (nom, email_contact) VALUES (:nom, :email_contact) RETURNING id'
            );
            $stmt->execute([
                'nom'           => $troupe->nom,
                'email_contact' => $troupe->emailContact,
            ]);
            $id = (int) $stmt->fetchColumn();
            return new Troupe($id, $troupe->nom, $troupe->emailContact);
        }

        $stmt = $this->pdo->prepare(
            'UPDATE troupe SET nom = :nom, email_contact = :email_contact, updated_at = NOW() WHERE id = :id'
        );
        $stmt->execute([
            'nom'           => $troupe->nom,
            'email_contact' => $troupe->emailContact,
            'id'            => $troupe->id,
        ]);
        return $troupe;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM troupe WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
