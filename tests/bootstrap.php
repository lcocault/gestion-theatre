<?php

declare(strict_types=1);

// Constants required by the application code under test
define('APP_NAME', 'Gestion Théâtre (Test)');
define('APP_ENV', 'development');
define('APP_URL', 'http://localhost');
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEW_PATH', SRC_PATH . '/Views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('MAIL_FROM', 'test@example.com');
define('MAIL_FROM_NAME', APP_NAME);
define('SESSION_NAME', 'theatre_test_session');
define('CSRF_TOKEN_NAME', '_csrf_token');

require_once __DIR__ . '/../vendor/autoload.php';
