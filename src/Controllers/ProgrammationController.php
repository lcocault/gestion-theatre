<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProgrammationRepository;

class ProgrammationController extends BaseController
{
    public function index(): void
    {
        $repo           = new ProgrammationRepository($this->pdo);
        $programmations = $repo->findAll();

        $this->render('public/programmations/index', [
            'programmations' => $programmations,
            'pageTitle'      => 'Programmations',
        ]);
    }

    public function show(int $id): void
    {
        $repo          = new ProgrammationRepository($this->pdo);
        $programmation = $repo->findById($id);

        if ($programmation === null) {
            $this->notFound();
            return;
        }

        $representations = $repo->getRepresentations($id);

        $this->render('public/programmations/show', [
            'programmation'   => $programmation,
            'representations' => $representations,
            'pageTitle'       => $programmation->nom,
        ]);
    }
}
