<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reservation;

class ReservationRepository extends BaseRepository
{
    public function findById(string $id): ?Reservation
    {
        $stmt = $this->pdo->prepare('SELECT * FROM reservation WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Reservation::fromArray($row) : null;
    }

    public function findByRepresentationId(int $representationId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, COALESCE(SUM(rp.quantite), 0) as total_places,
                    COALESCE(SUM(rp.quantite * rp.prix_unitaire), 0) as total_prix
             FROM reservation r
             LEFT JOIN reservation_places rp ON rp.reservation_id = r.id
             WHERE r.representation_id = :id
             GROUP BY r.id
             ORDER BY r.date_creation DESC'
        );
        $stmt->execute(['id' => $representationId]);
        return $stmt->fetchAll();
    }

    public function getPlaces(string $reservationId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM reservation_places WHERE reservation_id = :id'
        );
        $stmt->execute(['id' => $reservationId]);
        return $stmt->fetchAll();
    }

    public function save(Reservation $res): Reservation
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reservation (representation_id, nom, prenom, telephone, email,
             source_decouverte, handicap_visuel_auditif, handicap_moteur, statut)
             VALUES (:representation_id, :nom, :prenom, :telephone, :email,
             :source_decouverte, :handicap_visuel_auditif, :handicap_moteur, :statut)
             RETURNING id, date_creation'
        );
        $stmt->execute([
            'representation_id'       => $res->representationId,
            'nom'                     => $res->nom,
            'prenom'                  => $res->prenom,
            'telephone'               => $res->telephone,
            'email'                   => $res->email,
            'source_decouverte'       => $res->sourceDecouverte,
            'handicap_visuel_auditif' => $res->handicapVisuelAuditif ? 'true' : 'false',
            'handicap_moteur'         => $res->handicapMoteur ? 'true' : 'false',
            'statut'                  => $res->statut,
        ]);
        $row = $stmt->fetch();
        return new Reservation(
            $row['id'],
            $res->representationId,
            $res->nom,
            $res->prenom,
            $res->telephone,
            $res->email,
            $res->sourceDecouverte,
            $res->handicapVisuelAuditif,
            $res->handicapMoteur,
            $res->statut,
            $row['date_creation'],
        );
    }

    public function savePlaces(string $reservationId, array $places): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reservation_places (reservation_id, categorie, quantite, prix_unitaire)
             VALUES (:reservation_id, :categorie, :quantite, :prix_unitaire)'
        );
        foreach ($places as $place) {
            $stmt->execute([
                'reservation_id' => $reservationId,
                'categorie'      => $place['categorie'],
                'quantite'       => (int) $place['quantite'],
                'prix_unitaire'  => (float) $place['prix_unitaire'],
            ]);
        }
    }

    public function updateStatut(string $id, string $statut): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE reservation SET statut = :statut WHERE id = :id"
        );
        $stmt->execute(['statut' => $statut, 'id' => $id]);
    }

    public function cancelByRepresentationId(int $representationId): array
    {
        $stmt = $this->pdo->prepare(
            "UPDATE reservation SET statut = 'annule'
             WHERE representation_id = :id AND statut != 'annule'
             RETURNING id, email, nom, prenom"
        );
        $stmt->execute(['id' => $representationId]);
        return $stmt->fetchAll();
    }
}
