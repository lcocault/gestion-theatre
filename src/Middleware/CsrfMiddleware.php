<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Protection CSRF pour les formulaires admin.
 */
class CsrfMiddleware
{
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }

        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }

        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public static function validateToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }

        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }

        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    public static function check(): void
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!self::validateToken($token)) {
            http_response_code(403);
            die('Requête invalide (CSRF).');
        }
    }

    /**
     * Génère un champ input caché pour les formulaires HTML.
     */
    public static function inputField(): string
    {
        $token = self::generateToken();
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            htmlspecialchars(CSRF_TOKEN_NAME, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}
