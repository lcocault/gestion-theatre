<?php

declare(strict_types=1);

// Environnement : 'production' ou 'development'
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_NAME', 'Gestion Théâtre');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// Chemins
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEW_PATH', SRC_PATH . '/Views');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Email
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@theatre.example.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: APP_NAME);

// Session
define('SESSION_NAME', 'theatre_session');

// CSRF
define('CSRF_TOKEN_NAME', '_csrf_token');

// Admin credentials (hashés en base ou via env)
define('ADMIN_PASSWORD_FILE', ROOT_PATH . '/config/.admin_password');

// Affichage des erreurs selon l'environnement
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
