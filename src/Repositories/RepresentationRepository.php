<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Representation;

class RepresentationRepository extends BaseRepository
{
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT r.*, p.titre as piece_titre, l.nom as lieu_nom
             FROM representation r
             LEFT JOIN piece p ON p.id = r.piece_id
             LEFT JOIN lieu l ON l.id = r.lieu_id
             ORDER BY r.date_debut DESC'
        );
        return $stmt->fetchAll();
    }

    public function findUpcoming(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, p.titre as piece_titre, p.affiche_vignette, p.synopsis,
                    l.nom as lieu_nom
             FROM representation r
             LEFT JOIN piece p ON p.id = r.piece_id
             LEFT JOIN lieu l ON l.id = r.lieu_id
             WHERE r.date_debut >= NOW() AND r.annulee = FALSE
             ORDER BY r.date_debut ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Representation
    {
        $stmt = $this->pdo->prepare('SELECT * FROM representation WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Representation::fromArray($row) : null;
    }

    public function findByIdWithDetails(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, p.titre as piece_titre, p.auteur, p.synopsis, p.affiche_vignette,
                    p.type as piece_type, p.duree_minutes, p.age_minimum,
                    l.nom as lieu_nom, l.plan_acces,
                    t.nom as troupe_nom, t.email_contact as troupe_email
             FROM representation r
             LEFT JOIN piece p ON p.id = r.piece_id
             LEFT JOIN lieu l ON l.id = r.lieu_id
             LEFT JOIN troupe t ON t.id = p.troupe_id
             WHERE r.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByPieceId(int $pieceId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, l.nom as lieu_nom
             FROM representation r
             LEFT JOIN lieu l ON l.id = r.lieu_id
             WHERE r.piece_id = :piece_id
             ORDER BY r.date_debut DESC'
        );
        $stmt->execute(['piece_id' => $pieceId]);
        return $stmt->fetchAll();
    }

    public function findByLieuId(int $lieuId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, p.titre as piece_titre
             FROM representation r
             LEFT JOIN piece p ON p.id = r.piece_id
             WHERE r.lieu_id = :lieu_id
             ORDER BY r.date_debut DESC'
        );
        $stmt->execute(['lieu_id' => $lieuId]);
        return $stmt->fetchAll();
    }

    public function getPrix(int $representationId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM representation_prix WHERE representation_id = :id ORDER BY prix'
        );
        $stmt->execute(['id' => $representationId]);
        return $stmt->fetchAll();
    }

    public function getPaiements(int $representationId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM representation_paiement WHERE representation_id = :id'
        );
        $stmt->execute(['id' => $representationId]);
        return $stmt->fetchAll();
    }

    public function save(Representation $rep): Representation
    {
        $data = [
            'piece_id'                => $rep->pieceId,
            'lieu_id'                 => $rep->lieuId,
            'date_debut'              => $rep->dateDebut,
            'date_fin'                => $rep->dateFin,
            'max_spectateurs'         => $rep->maxSpectateurs,
            'date_limite_reservation' => $rep->dateLimiteReservation,
            'gratuit'                 => $rep->gratuit ? 'true' : 'false',
            'annulee'                 => $rep->annulee ? 'true' : 'false',
        ];

        if ($rep->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO representation (piece_id, lieu_id, date_debut, date_fin, max_spectateurs,
                 date_limite_reservation, gratuit, annulee)
                 VALUES (:piece_id, :lieu_id, :date_debut, :date_fin, :max_spectateurs,
                 :date_limite_reservation, :gratuit, :annulee)
                 RETURNING id'
            );
            $stmt->execute($data);
            $id = (int) $stmt->fetchColumn();
            return new Representation($id, $rep->pieceId, $rep->lieuId, $rep->dateDebut,
                $rep->dateFin, $rep->maxSpectateurs, $rep->dateLimiteReservation, $rep->gratuit, $rep->annulee);
        }

        $data['id'] = $rep->id;
        $stmt = $this->pdo->prepare(
            'UPDATE representation SET piece_id=:piece_id, lieu_id=:lieu_id, date_debut=:date_debut,
             date_fin=:date_fin, max_spectateurs=:max_spectateurs,
             date_limite_reservation=:date_limite_reservation, gratuit=:gratuit,
             annulee=:annulee, updated_at=NOW() WHERE id=:id'
        );
        $stmt->execute($data);
        return $rep;
    }

    public function savePrix(int $representationId, array $prix): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM representation_prix WHERE representation_id = :id');
        $stmt->execute(['id' => $representationId]);

        $stmt = $this->pdo->prepare(
            'INSERT INTO representation_prix (representation_id, categorie, prix) VALUES (:rid, :categorie, :prix)'
        );
        foreach ($prix as $p) {
            $stmt->execute([
                'rid'      => $representationId,
                'categorie' => $p['categorie'],
                'prix'     => $p['prix'],
            ]);
        }
    }

    public function savePaiements(int $representationId, array $modes): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM representation_paiement WHERE representation_id = :id');
        $stmt->execute(['id' => $representationId]);

        $stmt = $this->pdo->prepare(
            'INSERT INTO representation_paiement (representation_id, mode) VALUES (:rid, :mode)'
        );
        foreach ($modes as $mode) {
            $stmt->execute(['rid' => $representationId, 'mode' => $mode]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM representation WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function countReservations(int $representationId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(rp.quantite), 0)
             FROM reservation r
             JOIN reservation_places rp ON rp.reservation_id = r.id
             WHERE r.representation_id = :id AND r.statut != 'annule'"
        );
        $stmt->execute(['id' => $representationId]);
        return (int) $stmt->fetchColumn();
    }
}
