<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Representation;
use App\Repositories\RepresentationRepository;
use App\Repositories\PieceRepository;
use App\Repositories\LieuRepository;
use App\Repositories\ReservationRepository;
use App\Services\EmailService;

class RepresentationAdminController extends AdminBaseController
{
    private RepresentationRepository $repo;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new RepresentationRepository($pdo);
    }

    public function index(): void
    {
        $this->requireAuth();
        $representations = $this->repo->findAll();
        $this->renderAdmin('admin/representations/index', [
            'representations' => $representations,
            'pageTitle'       => 'Gestion des représentations',
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $pieces = (new PieceRepository($this->pdo))->findAll();
        $lieux  = (new LieuRepository($this->pdo))->findAll();
        $this->renderAdmin('admin/representations/form', [
            'representation' => null,
            'pieces'         => $pieces,
            'lieux'          => $lieux,
            'prix'           => [],
            'paiements'      => [],
            'errors'         => [],
            'pageTitle'      => 'Ajouter une représentation',
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->renderFormWithErrors(null, $errors);
            return;
        }

        $rep = $this->buildRepresentation(null, $_POST);
        $saved = $this->repo->save($rep);
        $this->savePrixAndPaiements($saved->id, $_POST);
        $this->redirect('/admin/representations');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $rep = $this->repo->findById($id);
        if ($rep === null) { $this->notFound(); return; }

        $pieces    = (new PieceRepository($this->pdo))->findAll();
        $lieux     = (new LieuRepository($this->pdo))->findAll();
        $prix      = $this->repo->getPrix($id);
        $paiements = $this->repo->getPaiements($id);

        $this->renderAdmin('admin/representations/form', [
            'representation' => $rep,
            'pieces'         => $pieces,
            'lieux'          => $lieux,
            'prix'           => $prix,
            'paiements'      => $paiements,
            'errors'         => [],
            'pageTitle'      => 'Modifier la représentation',
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $rep = $this->repo->findById($id);
        if ($rep === null) { $this->notFound(); return; }

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->renderFormWithErrors($rep, $errors);
            return;
        }

        $updated = $this->buildRepresentation($id, $_POST);
        $this->repo->save($updated);
        $this->savePrixAndPaiements($id, $_POST);
        $this->redirect('/admin/representations');
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();
        $this->repo->delete($id);
        $this->redirect('/admin/representations');
    }

    public function reservations(int $id): void
    {
        $this->requireAuth();
        $rep = $this->repo->findByIdWithDetails($id);
        if ($rep === null) { $this->notFound(); return; }

        $reservationRepo = new ReservationRepository($this->pdo);
        $reservations    = $reservationRepo->findByRepresentationId($id);

        $this->renderAdmin('admin/representations/reservations', [
            'representation' => $rep,
            'reservations'   => $reservations,
            'pageTitle'      => 'Réservations – ' . $rep['piece_titre'],
        ]);
    }

    public function confirmerReservation(int $representationId, string $reservationId): void
    {
        $this->requireAuth();
        $this->checkCsrf();
        $reservationRepo = new ReservationRepository($this->pdo);
        $reservationRepo->updateStatut($reservationId, 'confirme');
        $this->redirect('/admin/representations/' . $representationId . '/reservations');
    }

    public function annulerReservation(int $representationId, string $reservationId): void
    {
        $this->requireAuth();
        $this->checkCsrf();
        $reservationRepo = new ReservationRepository($this->pdo);
        $reservationRepo->updateStatut($reservationId, 'annule');
        $this->redirect('/admin/representations/' . $representationId . '/reservations');
    }

    public function annulerRepresentation(int $id): void
    {
        $this->requireAuth();
        $this->checkCsrf();

        $rep = $this->repo->findByIdWithDetails($id);
        if ($rep === null) { $this->notFound(); return; }

        // Annuler la représentation
        $representation = $this->repo->findById($id);
        $representation->annulee = true;
        $this->repo->save($representation);

        // Annuler toutes les réservations et notifier les spectateurs
        $reservationRepo  = new ReservationRepository($this->pdo);
        $emailService     = new EmailService();
        $annulees         = $reservationRepo->cancelByRepresentationId($id);
        $replacementInfo  = trim($_POST['replacement_info'] ?? '') ?: null;
        $dateFormatee     = date('d/m/Y à H:i', strtotime($rep['date_debut']));

        foreach ($annulees as $r) {
            $emailService->sendAnnulationRepresentation(
                $r['email'],
                $r['prenom'] . ' ' . $r['nom'],
                $rep['piece_titre'],
                $dateFormatee,
                $replacementInfo,
            );
        }

        $this->redirect('/admin/representations');
    }

    private function savePrixAndPaiements(int $id, array $data): void
    {
        $prix = [];
        $categories = $data['prix_categorie'] ?? [];
        $montants   = $data['prix_montant'] ?? [];
        for ($i = 0; $i < count($categories); $i++) {
            $cat = trim($categories[$i] ?? '');
            $mnt = (float) ($montants[$i] ?? 0);
            if ($cat !== '') {
                $prix[] = ['categorie' => $cat, 'prix' => $mnt];
            }
        }
        $this->repo->savePrix($id, $prix);

        $modesValides = ['en_ligne', 'cb', 'cheque', 'especes'];
        $modes = array_filter($data['paiements'] ?? [], fn($m) => in_array($m, $modesValides, true));
        $this->repo->savePaiements($id, array_values($modes));
    }

    private function buildRepresentation(?int $id, array $data): Representation
    {
        return new Representation(
            id: $id,
            pieceId: (int) $data['piece_id'],
            lieuId: !empty($data['lieu_id']) ? (int) $data['lieu_id'] : null,
            dateDebut: $data['date_debut'],
            dateFin: !empty($data['date_fin']) ? $data['date_fin'] : null,
            maxSpectateurs: (int) ($data['max_spectateurs'] ?? 100),
            dateLimiteReservation: !empty($data['date_limite_reservation'])
                ? $data['date_limite_reservation'] : null,
            gratuit: !empty($data['gratuit']),
            annulee: !empty($data['annulee']),
        );
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['piece_id'])) {
            $errors[] = 'La pièce est obligatoire.';
        }
        if (empty($data['date_debut'])) {
            $errors[] = 'La date de début est obligatoire.';
        }
        if (empty($data['max_spectateurs']) || (int) $data['max_spectateurs'] < 1) {
            $errors[] = 'Le nombre maximum de spectateurs doit être supérieur à 0.';
        }
        return $errors;
    }

    private function renderFormWithErrors(?Representation $rep, array $errors): void
    {
        $pieces    = (new PieceRepository($this->pdo))->findAll();
        $lieux     = (new LieuRepository($this->pdo))->findAll();
        $prix      = $rep ? $this->repo->getPrix($rep->id) : [];
        $paiements = $rep ? $this->repo->getPaiements($rep->id) : [];
        $this->renderAdmin('admin/representations/form', [
            'representation' => $rep,
            'pieces'         => $pieces,
            'lieux'          => $lieux,
            'prix'           => $prix,
            'paiements'      => $paiements,
            'errors'         => $errors,
            'pageTitle'      => $rep ? 'Modifier la représentation' : 'Ajouter une représentation',
        ]);
    }
}
