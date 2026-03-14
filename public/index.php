<?php

declare(strict_types=1);

/**
 * Point d'entrée unique de l'application.
 * Tous les accès web passent par ce fichier.
 */

// Sécurité : interdire l'accès direct aux fichiers sensibles
define('APP_ENTRY', true);

// Chargement de la configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Autoloader PSR-4 simple (sans Composer)
spl_autoload_register(function (string $className): void {
    // Namespace App\ → /src/
    if (str_starts_with($className, 'App\\')) {
        $relative = substr($className, 4); // retire 'App\'
        $path = SRC_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
});

// Démarrage de la session
session_name(SESSION_NAME);
session_start();

// Connexion à la base de données (lazy)
// La connexion est effectuée uniquement si nécessaire dans les contrôleurs

// Routage
require_once __DIR__ . '/router.php';
