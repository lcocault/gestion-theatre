<?php

declare(strict_types=1);

/**
 * Configuration de la connexion PostgreSQL via PDO.
 * Les variables d'environnement sont privilégiées pour la sécurité.
 */
function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '5432';
    $name = getenv('DB_NAME') ?: 'gestion_theatre';
    $user = getenv('DB_USER') ?: 'theatre_user';
    $pass = getenv('DB_PASS') ?: '';

    $dsn = "pgsql:host={$host};port={$port};dbname={$name}";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        if (APP_ENV === 'development') {
            throw $e;
        }
        // En production, ne pas exposer les détails de connexion
        throw new RuntimeException('Erreur de connexion à la base de données.');
    }

    return $pdo;
}
