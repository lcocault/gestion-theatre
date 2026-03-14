<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Repositories\AdminRepository;

class AuthController extends AdminBaseController
{
    public function loginForm(): void
    {
        if (AuthMiddleware::isLoggedIn()) {
            $this->redirect('/admin');
        }
        $this->render('admin/login', [
            'error'     => null,
            'pageTitle' => 'Administration – Connexion',
        ]);
    }

    public function login(): void
    {
        CsrfMiddleware::check();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $adminRepo = new AdminRepository($this->pdo);
        $admin     = $adminRepo->findByUsername($username);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            AuthMiddleware::login((int) $admin['id'], $admin['username']);
            $this->redirect('/admin');
            return;
        }

        $this->render('admin/login', [
            'error'     => 'Identifiants incorrects.',
            'pageTitle' => 'Administration – Connexion',
        ]);
    }

    public function logout(): void
    {
        AuthMiddleware::logout();
        $this->redirect('/admin/login');
    }
}
