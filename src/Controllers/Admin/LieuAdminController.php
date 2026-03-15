<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Lieu;
use App\Repositories\LieuRepository;

class LieuAdminController extends AdminBaseController
{
    private LieuRepository $repo;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new LieuRepository($pdo);
    }

    public function index(): void
    {
        $this->requireAuth();
        $lieux = $this->repo->findAll();
        $this->renderAdmin('admin/lieux/index', [
            'lieux'     => $lieux,
            'pageTitle' => 'Gestion des lieux',
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->renderAdmin('admin/lieux/form', [
            'lieu'      => null,
            'errors'    => [],
            'pageTitle' => 'Ajouter un lieu',
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->renderAdmin('admin/lieux/form', [
                'lieu'      => null,
                'errors'    => $errors,
                'pageTitle' => 'Ajouter un lieu',
            ]);
            return;
        }

        $lieu = new Lieu(null, trim($_POST['nom']), trim($_POST['adresse'] ?? '') ?: null, trim($_POST['plan_acces'] ?? '') ?: null);
        $this->repo->save($lieu);
        $this->redirect('/admin/lieux');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $lieu = $this->repo->findById($id);
        if ($lieu === null) { $this->notFound(); return; }

        $this->renderAdmin('admin/lieux/form', [
            'lieu'      => $lieu,
            'errors'    => [],
            'pageTitle' => 'Modifier le lieu',
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $lieu = $this->repo->findById($id);
        if ($lieu === null) { $this->notFound(); return; }

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->renderAdmin('admin/lieux/form', [
                'lieu'      => $lieu,
                'errors'    => $errors,
                'pageTitle' => 'Modifier le lieu',
            ]);
            return;
        }

        $lieu->nom       = trim($_POST['nom']);
        $lieu->adresse   = trim($_POST['adresse'] ?? '') ?: null;
        $lieu->planAcces = trim($_POST['plan_acces'] ?? '') ?: null;
        $this->repo->save($lieu);
        $this->redirect('/admin/lieux');
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();
        $this->repo->delete($id);
        $this->redirect('/admin/lieux');
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['nom'] ?? ''))) {
            $errors[] = 'Le nom est obligatoire.';
        }
        return $errors;
    }
}
