<?php

declare(strict_types=1);

namespace App\Models;

class Troupe
{
    public function __construct(
        public readonly ?int $id,
        public string $nom,
        public ?string $emailContact,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            nom: $data['nom'],
            emailContact: $data['email_contact'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'nom'           => $this->nom,
            'email_contact' => $this->emailContact,
        ];
    }
}
