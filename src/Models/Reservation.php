<?php

declare(strict_types=1);

namespace App\Models;

class Reservation
{
    public const STATUT_RESERVE  = 'reserve';
    public const STATUT_CONFIRME = 'confirme';
    public const STATUT_ANNULE   = 'annule';

    public function __construct(
        public readonly ?string $id,
        public int $representationId,
        public string $nom,
        public string $prenom,
        public ?string $telephone,
        public string $email,
        public ?string $sourceDecouverte,
        public bool $handicapVisuelAuditif,
        public bool $handicapMoteur,
        public string $statut,
        public ?string $dateCreation = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            representationId: (int) $data['representation_id'],
            nom: $data['nom'],
            prenom: $data['prenom'],
            telephone: $data['telephone'] ?? null,
            email: $data['email'],
            sourceDecouverte: $data['source_decouverte'] ?? null,
            handicapVisuelAuditif: (bool) ($data['handicap_visuel_auditif'] ?? false),
            handicapMoteur: (bool) ($data['handicap_moteur'] ?? false),
            statut: $data['statut'] ?? self::STATUT_RESERVE,
            dateCreation: $data['date_creation'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'                      => $this->id,
            'representation_id'       => $this->representationId,
            'nom'                     => $this->nom,
            'prenom'                  => $this->prenom,
            'telephone'               => $this->telephone,
            'email'                   => $this->email,
            'source_decouverte'       => $this->sourceDecouverte,
            'handicap_visuel_auditif' => $this->handicapVisuelAuditif,
            'handicap_moteur'         => $this->handicapMoteur,
            'statut'                  => $this->statut,
            'date_creation'           => $this->dateCreation,
        ];
    }
}
