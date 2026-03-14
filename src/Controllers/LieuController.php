<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\LieuRepository;
use App\Repositories\RepresentationRepository;

class LieuController extends BaseController
{
    public function index(): void
    {
        $lieuRepo = new LieuRepository($this->pdo);
        $lieux    = $lieuRepo->findAll();

        $this->render('public/lieux/index', [
            'lieux'     => $lieux,
            'pageTitle' => 'Les lieux',
        ]);
    }

    public function show(int $id): void
    {
        $lieuRepo           = new LieuRepository($this->pdo);
        $representationRepo = new RepresentationRepository($this->pdo);

        $lieu = $lieuRepo->findById($id);
        if ($lieu === null) {
            $this->notFound();
            return;
        }

        $representations = $representationRepo->findByLieuId($id);

        $this->render('public/lieux/show', [
            'lieu'            => $lieu,
            'representations' => $representations,
            'pageTitle'       => $lieu->nom,
        ]);
    }
}
