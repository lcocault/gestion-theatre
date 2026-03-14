<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reservation;
use App\Repositories\ReservationRepository;
use App\Repositories\RepresentationRepository;

class ReservationService
{
    public function __construct(
        private readonly ReservationRepository $reservationRepo,
        private readonly RepresentationRepository $representationRepo,
        private readonly EmailService $emailService,
    ) {}

    /**
     * Crée une réservation avec ses places, vérifie les capacités disponibles.
     *
     * @throws \RuntimeException si la réservation n'est pas possible
     */
    public function creerReservation(
        int $representationId,
        array $formData,
        array $places,
    ): Reservation {
        $representation = $this->representationRepo->findByIdWithDetails($representationId);
        if ($representation === null) {
            throw new \RuntimeException('Représentation introuvable.');
        }

        if ($representation['annulee']) {
            throw new \RuntimeException('Cette représentation a été annulée.');
        }

        if (!empty($representation['date_limite_reservation'])
            && strtotime($representation['date_limite_reservation']) < time()) {
            throw new \RuntimeException('Les réservations sont closes pour cette représentation.');
        }

        // Vérification capacité
        $dejaPris = $this->representationRepo->countReservations($representationId);
        $totalDemande = array_sum(array_column($places, 'quantite'));
        if ($dejaPris + $totalDemande > $representation['max_spectateurs']) {
            throw new \RuntimeException('Il n\'y a pas assez de places disponibles.');
        }

        // Création de la réservation
        $reservation = new Reservation(
            id: null,
            representationId: $representationId,
            nom: $formData['nom'],
            prenom: $formData['prenom'],
            telephone: $formData['telephone'] ?? null,
            email: $formData['email'],
            sourceDecouverte: $formData['source_decouverte'] ?? null,
            handicapVisuelAuditif: !empty($formData['handicap_visuel_auditif']),
            handicapMoteur: !empty($formData['handicap_moteur']),
            statut: Reservation::STATUT_RESERVE,
        );

        $saved = $this->reservationRepo->save($reservation);
        $this->reservationRepo->savePlaces($saved->id, $places);

        // Envoi de l'email de confirmation
        $dateFormatee = date('d/m/Y à H:i', strtotime($representation['date_debut']));
        $this->emailService->sendConfirmationReservation(
            $saved->email,
            $saved->prenom . ' ' . $saved->nom,
            $representation['piece_titre'],
            $dateFormatee,
            $saved->id,
            $places,
        );

        return $saved;
    }

    /**
     * Annule une réservation individuelle.
     */
    public function annulerReservation(string $reservationId): void
    {
        $reservation = $this->reservationRepo->findById($reservationId);
        if ($reservation === null) {
            throw new \RuntimeException('Réservation introuvable.');
        }

        if ($reservation->statut === Reservation::STATUT_ANNULE) {
            throw new \RuntimeException('Cette réservation est déjà annulée.');
        }

        $this->reservationRepo->updateStatut($reservationId, Reservation::STATUT_ANNULE);

        $representation = $this->representationRepo->findByIdWithDetails($reservation->representationId);
        if ($representation !== null) {
            $dateFormatee = date('d/m/Y à H:i', strtotime($representation['date_debut']));
            $this->emailService->sendAnnulationReservation(
                $reservation->email,
                $reservation->prenom . ' ' . $reservation->nom,
                $representation['piece_titre'],
                $dateFormatee,
            );
        }
    }
}
