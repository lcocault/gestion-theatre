<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Piece;
use App\Repositories\PieceRepository;
use App\Repositories\TroupeRepository;

class PieceAdminController extends AdminBaseController
{
    private PieceRepository $repo;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new PieceRepository($pdo);
    }

    public function index(): void
    {
        $this->requireAuth();
        $pieces = $this->repo->findAll();
        $this->renderAdmin('admin/pieces/index', [
            'pieces'    => $pieces,
            'pageTitle' => 'Gestion des pièces',
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $troupes = (new TroupeRepository($this->pdo))->findAll();
        $this->renderAdmin('admin/pieces/form', [
            'piece'     => null,
            'troupes'   => $troupes,
            'errors'    => [],
            'pageTitle' => 'Ajouter une pièce',
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $troupes = (new TroupeRepository($this->pdo))->findAll();
            $this->renderAdmin('admin/pieces/form', [
                'piece'     => null,
                'troupes'   => $troupes,
                'errors'    => $errors,
                'pageTitle' => 'Ajouter une pièce',
            ]);
            return;
        }

        $piece = $this->buildPiece(null, $_POST);
        $this->repo->save($piece);
        $this->redirect('/admin/pieces');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $piece = $this->repo->findById($id);
        if ($piece === null) { $this->notFound(); return; }

        $troupes = (new TroupeRepository($this->pdo))->findAll();
        $this->renderAdmin('admin/pieces/form', [
            'piece'     => $piece,
            'troupes'   => $troupes,
            'errors'    => [],
            'pageTitle' => 'Modifier la pièce',
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $piece = $this->repo->findById($id);
        if ($piece === null) { $this->notFound(); return; }

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $troupes = (new TroupeRepository($this->pdo))->findAll();
            $this->renderAdmin('admin/pieces/form', [
                'piece'     => $piece,
                'troupes'   => $troupes,
                'errors'    => $errors,
                'pageTitle' => 'Modifier la pièce',
            ]);
            return;
        }

        $updated = $this->buildPiece($id, $_POST);
        $this->repo->save($updated);
        $this->redirect('/admin/pieces');
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();
        $this->repo->delete($id);
        $this->redirect('/admin/pieces');
    }

    private function buildPiece(?int $id, array $data): Piece
    {
        return new Piece(
            id: $id,
            titre: trim($data['titre']),
            auteur: trim($data['auteur'] ?? '') ?: null,
            synopsis: trim($data['synopsis'] ?? '') ?: null,
            troupeId: !empty($data['troupe_id']) ? (int) $data['troupe_id'] : null,
            type: trim($data['type'] ?? '') ?: null,
            dureeMinutes: !empty($data['duree_minutes']) ? (int) $data['duree_minutes'] : null,
            ageMinimum: (int) ($data['age_minimum'] ?? 0),
            afficheVignette: trim($data['affiche_vignette'] ?? '') ?: null,
        );
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['titre'] ?? ''))) {
            $errors[] = 'Le titre est obligatoire.';
        }
        return $errors;
    }
}
