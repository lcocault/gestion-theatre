<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Modèle représentant un lieu.
 */
class Lieu
{
    public function __construct(
        public readonly ?int $id,
        public string $nom,
        public ?string $adresse,
        public ?string $planAcces,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            nom: $data['nom'],
            adresse: $data['adresse'] ?? null,
            planAcces: $data['plan_acces'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'nom'        => $this->nom,
            'adresse'    => $this->adresse,
            'plan_acces' => $this->planAcces,
        ];
    }
}
