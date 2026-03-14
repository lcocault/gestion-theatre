<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Piece;
use PHPUnit\Framework\TestCase;

class PieceTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'id'               => '10',
            'titre'            => 'Le Bourgeois Gentilhomme',
            'auteur'           => 'Molière',
            'synopsis'         => 'Une comédie-ballet.',
            'troupe_id'        => '3',
            'type'             => 'Comédie',
            'duree_minutes'    => '120',
            'age_minimum'      => '8',
            'affiche_vignette' => 'https://example.com/affiche.jpg',
        ];

        $piece = Piece::fromArray($data);

        $this->assertSame(10, $piece->id);
        $this->assertSame('Le Bourgeois Gentilhomme', $piece->titre);
        $this->assertSame('Molière', $piece->auteur);
        $this->assertSame('Une comédie-ballet.', $piece->synopsis);
        $this->assertSame(3, $piece->troupeId);
        $this->assertSame('Comédie', $piece->type);
        $this->assertSame(120, $piece->dureeMinutes);
        $this->assertSame(8, $piece->ageMinimum);
        $this->assertSame('https://example.com/affiche.jpg', $piece->afficheVignette);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $piece = Piece::fromArray(['titre' => 'Hamlet']);

        $this->assertNull($piece->id);
        $this->assertSame('Hamlet', $piece->titre);
        $this->assertNull($piece->auteur);
        $this->assertNull($piece->synopsis);
        $this->assertNull($piece->troupeId);
        $this->assertNull($piece->type);
        $this->assertNull($piece->dureeMinutes);
        $this->assertSame(0, $piece->ageMinimum);
        $this->assertNull($piece->afficheVignette);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $piece = new Piece(1, 'Tartuffe', 'Molière', null, 2, 'Comédie', 90, 0, null);

        $array = $piece->toArray();

        $this->assertSame(1, $array['id']);
        $this->assertSame('Tartuffe', $array['titre']);
        $this->assertSame('Molière', $array['auteur']);
        $this->assertNull($array['synopsis']);
        $this->assertSame(2, $array['troupe_id']);
        $this->assertSame('Comédie', $array['type']);
        $this->assertSame(90, $array['duree_minutes']);
        $this->assertSame(0, $array['age_minimum']);
        $this->assertNull($array['affiche_vignette']);
    }

    public function testAgeMinimumDefaultsToZero(): void
    {
        $piece = Piece::fromArray(['titre' => 'Spectacle familial']);

        $this->assertSame(0, $piece->ageMinimum);
    }
}
