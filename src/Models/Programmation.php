<?php

declare(strict_types=1);

namespace App\Models;

class Programmation
{
    public function __construct(
        public readonly ?int $id,
        public string $nom,
        public string $dateDebut,
        public string $dateFin,
        public ?string $afficheVignette,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            nom: $data['nom'],
            dateDebut: $data['date_debut'],
            dateFin: $data['date_fin'],
            afficheVignette: $data['affiche_vignette'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'nom'              => $this->nom,
            'date_debut'       => $this->dateDebut,
            'date_fin'         => $this->dateFin,
            'affiche_vignette' => $this->afficheVignette,
        ];
    }

    public function isActive(): bool
    {
        $now = date('Y-m-d');
        return $this->dateDebut <= $now && $this->dateFin >= $now;
    }
}
