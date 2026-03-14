<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Middleware d'authentification pour la zone admin.
 */
class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }

        if (empty($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
        return !empty($_SESSION['admin_logged_in']);
    }

    public static function login(int $adminId, string $username): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id']        = $adminId;
        $_SESSION['admin_username']  = $username;
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
        $_SESSION = [];
        session_destroy();
    }
}
