<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

abstract class AdminBaseController extends BaseController
{
    protected function requireAuth(): void
    {
        AuthMiddleware::requireAuth();
    }

    protected function csrf(): string
    {
        return CsrfMiddleware::inputField();
    }

    protected function checkCsrf(): void
    {
        CsrfMiddleware::check();
    }

    protected function renderAdmin(string $view, array $data = []): void
    {
        $data['csrfField']       = CsrfMiddleware::inputField();
        $data['adminUsername']   = $_SESSION['admin_username'] ?? '';
        $this->render($view, $data);
    }
}
