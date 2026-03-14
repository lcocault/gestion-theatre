<?php

declare(strict_types=1);

namespace App\Models;

class Piece
{
    public function __construct(
        public readonly ?int $id,
        public string $titre,
        public ?string $auteur,
        public ?string $synopsis,
        public ?int $troupeId,
        public ?string $type,
        public ?int $dureeMinutes,
        public int $ageMinimum,
        public ?string $afficheVignette,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            titre: $data['titre'],
            auteur: $data['auteur'] ?? null,
            synopsis: $data['synopsis'] ?? null,
            troupeId: isset($data['troupe_id']) ? (int) $data['troupe_id'] : null,
            type: $data['type'] ?? null,
            dureeMinutes: isset($data['duree_minutes']) ? (int) $data['duree_minutes'] : null,
            ageMinimum: isset($data['age_minimum']) ? (int) $data['age_minimum'] : 0,
            afficheVignette: $data['affiche_vignette'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'titre'            => $this->titre,
            'auteur'           => $this->auteur,
            'synopsis'         => $this->synopsis,
            'troupe_id'        => $this->troupeId,
            'type'             => $this->type,
            'duree_minutes'    => $this->dureeMinutes,
            'age_minimum'      => $this->ageMinimum,
            'affiche_vignette' => $this->afficheVignette,
        ];
    }
}
