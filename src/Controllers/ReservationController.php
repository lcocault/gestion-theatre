<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Reservation;
use App\Repositories\RepresentationRepository;
use App\Repositories\ReservationRepository;
use App\Services\ReservationService;
use App\Services\EmailService;

class ReservationController extends BaseController
{
    public function form(int $representationId): void
    {
        $representationRepo = new RepresentationRepository($this->pdo);
        $representation     = $representationRepo->findByIdWithDetails($representationId);

        if ($representation === null || $representation['annulee']) {
            $this->notFound();
            return;
        }

        $prix = $representationRepo->getPrix($representationId);

        $this->render('public/reservations/form', [
            'representation' => $representation,
            'prix'           => $prix,
            'errors'         => [],
            'pageTitle'      => 'Réserver – ' . $representation['piece_titre'],
        ]);
    }

    public function store(int $representationId): void
    {
        $representationRepo = new RepresentationRepository($this->pdo);
        $representation     = $representationRepo->findByIdWithDetails($representationId);

        if ($representation === null || $representation['annulee']) {
            $this->notFound();
            return;
        }

        $errors = $this->validateReservationForm($_POST);

        // Traitement des places
        $places = [];
        $prix   = $representationRepo->getPrix($representationId);
        foreach ($prix as $p) {
            $key = 'places_' . $p['id'];
            $qty = (int) ($_POST[$key] ?? 0);
            if ($qty > 0) {
                $places[] = [
                    'categorie'     => $p['categorie'],
                    'quantite'      => $qty,
                    'prix_unitaire' => $p['prix'],
                ];
            }
        }

        // Si gratuit, on crée une place "entrée libre"
        if ($representation['gratuit'] && empty($places)) {
            $qtyLibre = (int) ($_POST['places_libre'] ?? 1);
            if ($qtyLibre > 0) {
                $places[] = [
                    'categorie'     => 'Entrée libre',
                    'quantite'      => $qtyLibre,
                    'prix_unitaire' => 0,
                ];
            }
        }

        if (empty($places)) {
            $errors[] = 'Veuillez sélectionner au moins une place.';
        }

        if (!empty($errors)) {
            $this->render('public/reservations/form', [
                'representation' => $representation,
                'prix'           => $prix,
                'errors'         => $errors,
                'pageTitle'      => 'Réserver – ' . $representation['piece_titre'],
            ]);
            return;
        }

        try {
            $service = new ReservationService(
                new ReservationRepository($this->pdo),
                $representationRepo,
                new EmailService(),
            );
            $reservation = $service->creerReservation($representationId, $_POST, $places);
            $this->redirect('/reservation/confirmation/' . urlencode($reservation->id));
        } catch (\RuntimeException $e) {
            $this->render('public/reservations/form', [
                'representation' => $representation,
                'prix'           => $prix,
                'errors'         => [$e->getMessage()],
                'pageTitle'      => 'Réserver – ' . $representation['piece_titre'],
            ]);
        }
    }

    public function confirmation(string $reservationId): void
    {
        $reservationRepo = new ReservationRepository($this->pdo);
        $reservation     = $reservationRepo->findById($reservationId);

        if ($reservation === null) {
            $this->notFound();
            return;
        }

        $representationRepo = new RepresentationRepository($this->pdo);
        $representation     = $representationRepo->findByIdWithDetails($reservation->representationId);
        $places             = $reservationRepo->getPlaces($reservationId);

        $this->render('public/reservations/confirmation', [
            'reservation'    => $reservation,
            'representation' => $representation,
            'places'         => $places,
            'pageTitle'      => 'Confirmation de réservation',
        ]);
    }

    public function annuler(string $reservationId): void
    {
        $reservationRepo = new ReservationRepository($this->pdo);
        $reservation     = $reservationRepo->findById($reservationId);

        if ($reservation === null) {
            $this->notFound();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $service = new ReservationService(
                    $reservationRepo,
                    new RepresentationRepository($this->pdo),
                    new EmailService(),
                );
                $service->annulerReservation($reservationId);
                $this->render('public/reservations/annulee', [
                    'reservation' => $reservation,
                    'pageTitle'   => 'Réservation annulée',
                ]);
                return;
            } catch (\RuntimeException $e) {
                $error = $e->getMessage();
            }
        }

        $this->render('public/reservations/annuler', [
            'reservation' => $reservation,
            'error'       => $error ?? null,
            'pageTitle'   => 'Annuler ma réservation',
        ]);
    }

    private function validateReservationForm(array $data): array
    {
        $errors = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $errors[] = 'Le nom est obligatoire.';
        }
        if (empty(trim($data['prenom'] ?? ''))) {
            $errors[] = 'Le prénom est obligatoire.';
        }
        if (empty(trim($data['email'] ?? '')) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Une adresse email valide est obligatoire.';
        }

        return $errors;
    }
}
