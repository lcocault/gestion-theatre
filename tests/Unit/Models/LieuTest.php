<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Lieu;
use PHPUnit\Framework\TestCase;

class LieuTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'id'         => '5',
            'nom'        => 'Salle des fêtes',
            'plan_acces' => 'Prendre la D2, tourner à gauche.',
        ];

        $lieu = Lieu::fromArray($data);

        $this->assertSame(5, $lieu->id);
        $this->assertSame('Salle des fêtes', $lieu->nom);
        $this->assertSame('Prendre la D2, tourner à gauche.', $lieu->planAcces);
    }

    public function testFromArrayWithNullPlanAcces(): void
    {
        $data = ['id' => '1', 'nom' => 'Théâtre du Capitole'];

        $lieu = Lieu::fromArray($data);

        $this->assertSame(1, $lieu->id);
        $this->assertNull($lieu->planAcces);
    }

    public function testFromArrayWithNullId(): void
    {
        $lieu = Lieu::fromArray(['nom' => 'Nouveau lieu']);

        $this->assertNull($lieu->id);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $lieu = new Lieu(3, 'Amphithéâtre', 'Route de Toulouse');

        $array = $lieu->toArray();

        $this->assertSame([
            'id'         => 3,
            'nom'        => 'Amphithéâtre',
            'plan_acces' => 'Route de Toulouse',
        ], $array);
    }

    public function testToArrayWithNullValues(): void
    {
        $lieu = new Lieu(null, 'Salle', null);

        $array = $lieu->toArray();

        $this->assertNull($array['id']);
        $this->assertNull($array['plan_acces']);
    }
}
