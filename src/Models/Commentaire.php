<?php

declare(strict_types=1);

namespace App\Models;

class Commentaire
{
    public function __construct(
        public readonly ?int $id,
        public int $representationId,
        public string $nom,
        public ?int $note,
        public string $commentaire,
        public ?string $dateCreation = null,
        public bool $valide = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            representationId: (int) $data['representation_id'],
            nom: $data['nom'],
            note: isset($data['note']) ? (int) $data['note'] : null,
            commentaire: $data['commentaire'],
            dateCreation: $data['date_creation'] ?? null,
            valide: (bool) ($data['valide'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'representation_id' => $this->representationId,
            'nom'               => $this->nom,
            'note'              => $this->note,
            'commentaire'       => $this->commentaire,
            'date_creation'     => $this->dateCreation,
            'valide'            => $this->valide,
        ];
    }
}
