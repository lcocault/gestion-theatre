<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Classe de base pour tous les repositories.
 */
abstract class BaseRepository
{
    public function __construct(
        protected readonly PDO $pdo,
    ) {}
}
