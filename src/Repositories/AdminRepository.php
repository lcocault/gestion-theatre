<?php

declare(strict_types=1);

namespace App\Repositories;

class AdminRepository extends BaseRepository
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_user WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
