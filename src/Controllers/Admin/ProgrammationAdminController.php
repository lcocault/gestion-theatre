<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Programmation;
use App\Repositories\ProgrammationRepository;
use App\Repositories\RepresentationRepository;

class ProgrammationAdminController extends AdminBaseController
{
    private ProgrammationRepository $repo;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new ProgrammationRepository($pdo);
    }

    public function index(): void
    {
        $this->requireAuth();
        $programmations = $this->repo->findAll();
        $this->renderAdmin('admin/programmations/index', [
            'programmations' => $programmations,
            'pageTitle'      => 'Gestion des programmations',
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $representations = (new RepresentationRepository($this->pdo))->findAll();
        $this->renderAdmin('admin/programmations/form', [
            'programmation'   => null,
            'representations' => $representations,
            'selected'        => [],
            'errors'          => [],
            'pageTitle'       => 'Ajouter une programmation',
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $representations = (new RepresentationRepository($this->pdo))->findAll();
            $this->renderAdmin('admin/programmations/form', [
                'programmation'   => null,
                'representations' => $representations,
                'selected'        => $_POST['representations'] ?? [],
                'errors'          => $errors,
                'pageTitle'       => 'Ajouter une programmation',
            ]);
            return;
        }

        $prog = new Programmation(
            null,
            trim($_POST['nom']),
            $_POST['date_debut'],
            $_POST['date_fin'],
            trim($_POST['affiche_vignette'] ?? '') ?: null,
        );
        $saved = $this->repo->save($prog);
        $this->repo->saveRepresentations($saved->id, $_POST['representations'] ?? []);
        $this->redirect('/admin/programmations');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $prog = $this->repo->findById($id);
        if ($prog === null) { $this->notFound(); return; }

        $representations = (new RepresentationRepository($this->pdo))->findAll();
        $selected        = array_column($this->repo->getRepresentations($id), 'id');

        $this->renderAdmin('admin/programmations/form', [
            'programmation'   => $prog,
            'representations' => $representations,
            'selected'        => array_map('strval', $selected),
            'errors'          => [],
            'pageTitle'       => 'Modifier la programmation',
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $prog = $this->repo->findById($id);
        if ($prog === null) { $this->notFound(); return; }

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $representations = (new RepresentationRepository($this->pdo))->findAll();
            $this->renderAdmin('admin/programmations/form', [
                'programmation'   => $prog,
                'representations' => $representations,
                'selected'        => $_POST['representations'] ?? [],
                'errors'          => $errors,
                'pageTitle'       => 'Modifier la programmation',
            ]);
            return;
        }

        $prog->nom             = trim($_POST['nom']);
        $prog->dateDebut       = $_POST['date_debut'];
        $prog->dateFin         = $_POST['date_fin'];
        $prog->afficheVignette = trim($_POST['affiche_vignette'] ?? '') ?: null;
        $this->repo->save($prog);
        $this->repo->saveRepresentations($id, $_POST['representations'] ?? []);
        $this->redirect('/admin/programmations');
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();
        $this->repo->delete($id);
        $this->redirect('/admin/programmations');
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['nom'] ?? ''))) {
            $errors[] = 'Le nom est obligatoire.';
        }
        if (empty($data['date_debut'])) {
            $errors[] = 'La date de début est obligatoire.';
        }
        if (empty($data['date_fin'])) {
            $errors[] = 'La date de fin est obligatoire.';
        }
        if (!empty($data['date_debut']) && !empty($data['date_fin'])
            && $data['date_fin'] < $data['date_debut']) {
            $errors[] = 'La date de fin doit être après la date de début.';
        }
        return $errors;
    }
}
