<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PieceRepository;
use App\Repositories\RepresentationRepository;
use App\Repositories\CommentaireRepository;

class PieceController extends BaseController
{
    public function index(): void
    {
        $pieceRepo = new PieceRepository($this->pdo);
        $pieces    = $pieceRepo->findAll();

        $this->render('public/pieces/index', [
            'pieces'    => $pieces,
            'pageTitle' => 'Les pièces',
        ]);
    }

    public function show(int $id): void
    {
        $pieceRepo          = new PieceRepository($this->pdo);
        $representationRepo = new RepresentationRepository($this->pdo);
        $commentaireRepo    = new CommentaireRepository($this->pdo);

        $piece = $pieceRepo->findById($id);
        if ($piece === null) {
            $this->notFound();
            return;
        }

        $representations = $representationRepo->findByPieceId($id);
        $commentaires    = $commentaireRepo->findByRepresentationId(
            // Affiche les commentaires de toutes les représentations de la pièce
            // On prend le premier pour simplifier – voir logique plus complète en prod
            $representations[0]['id'] ?? 0,
            true
        );

        $this->render('public/pieces/show', [
            'piece'           => $piece,
            'representations' => $representations,
            'commentaires'    => $commentaires,
            'pageTitle'       => $piece->titre,
        ]);
    }
}
