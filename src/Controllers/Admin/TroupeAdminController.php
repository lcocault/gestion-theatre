<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Troupe;
use App\Repositories\TroupeRepository;

class TroupeAdminController extends AdminBaseController
{
    private TroupeRepository $repo;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new TroupeRepository($pdo);
    }

    public function index(): void
    {
        $this->requireAuth();
        $troupes = $this->repo->findAll();
        $this->renderAdmin('admin/troupes/index', [
            'troupes'   => $troupes,
            'pageTitle' => 'Gestion des troupes',
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->renderAdmin('admin/troupes/form', [
            'troupe'    => null,
            'errors'    => [],
            'pageTitle' => 'Ajouter une troupe',
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->renderAdmin('admin/troupes/form', [
                'troupe'    => null,
                'errors'    => $errors,
                'pageTitle' => 'Ajouter une troupe',
            ]);
            return;
        }

        $troupe = new Troupe(null, trim($_POST['nom']),
            trim($_POST['email_contact'] ?? '') ?: null);
        $this->repo->save($troupe);
        $this->redirect('/admin/troupes');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $troupe = $this->repo->findById($id);
        if ($troupe === null) { $this->notFound(); return; }

        $this->renderAdmin('admin/troupes/form', [
            'troupe'    => $troupe,
            'errors'    => [],
            'pageTitle' => 'Modifier la troupe',
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $troupe = $this->repo->findById($id);
        if ($troupe === null) { $this->notFound(); return; }

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->renderAdmin('admin/troupes/form', [
                'troupe'    => $troupe,
                'errors'    => $errors,
                'pageTitle' => 'Modifier la troupe',
            ]);
            return;
        }

        $troupe->nom          = trim($_POST['nom']);
        $troupe->emailContact = trim($_POST['email_contact'] ?? '') ?: null;
        $this->repo->save($troupe);
        $this->redirect('/admin/troupes');
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();
        $this->repo->delete($id);
        $this->redirect('/admin/troupes');
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['nom'] ?? ''))) {
            $errors[] = 'Le nom est obligatoire.';
        }
        if (!empty($data['email_contact']) && !filter_var($data['email_contact'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email de contact est invalide.';
        }
        return $errors;
    }
}
