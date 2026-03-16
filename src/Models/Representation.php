<?php

declare(strict_types=1);

namespace App\Models;

class Representation
{
    public function __construct(
        public readonly ?int $id,
        public int $pieceId,
        public ?int $lieuId,
        public string $dateDebut,
        public int $maxSpectateurs,
        public ?string $dateLimiteReservation,
        public bool $gratuit,
        public bool $annulee,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            pieceId: (int) $data['piece_id'],
            lieuId: isset($data['lieu_id']) ? (int) $data['lieu_id'] : null,
            dateDebut: $data['date_debut'],
            maxSpectateurs: (int) ($data['max_spectateurs'] ?? 100),
            dateLimiteReservation: $data['date_limite_reservation'] ?? null,
            gratuit: (bool) ($data['gratuit'] ?? false),
            annulee: (bool) ($data['annulee'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                      => $this->id,
            'piece_id'                => $this->pieceId,
            'lieu_id'                 => $this->lieuId,
            'date_debut'              => $this->dateDebut,
            'max_spectateurs'         => $this->maxSpectateurs,
            'date_limite_reservation' => $this->dateLimiteReservation,
            'gratuit'                 => $this->gratuit,
            'annulee'                 => $this->annulee,
        ];
    }

    public function isReservationOpen(): bool
    {
        if ($this->annulee) {
            return false;
        }
        if ($this->dateLimiteReservation !== null) {
            return strtotime($this->dateLimiteReservation) >= time();
        }
        return strtotime($this->dateDebut) >= time();
    }
}
