<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Commentaire;
use PHPUnit\Framework\TestCase;

class CommentaireTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'id'                => '8',
            'representation_id' => '3',
            'nom'               => 'Sophie M.',
            'note'              => '4',
            'commentaire'       => 'Excellent spectacle, bravo à toute la troupe !',
            'date_creation'     => '2025-05-10 18:30:00',
            'valide'            => '1',
        ];

        $c = Commentaire::fromArray($data);

        $this->assertSame(8, $c->id);
        $this->assertSame(3, $c->representationId);
        $this->assertSame('Sophie M.', $c->nom);
        $this->assertSame(4, $c->note);
        $this->assertSame('Excellent spectacle, bravo à toute la troupe !', $c->commentaire);
        $this->assertSame('2025-05-10 18:30:00', $c->dateCreation);
        $this->assertTrue($c->valide);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $c = Commentaire::fromArray([
            'representation_id' => '1',
            'nom'               => 'Anonyme',
            'commentaire'       => 'Bien.',
        ]);

        $this->assertNull($c->id);
        $this->assertNull($c->note);
        $this->assertNull($c->dateCreation);
        $this->assertFalse($c->valide);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $c = new Commentaire(2, 5, 'Pierre D.', 5, 'Magnifique !', '2025-06-01 20:00:00', true);

        $array = $c->toArray();

        $this->assertSame([
            'id'                => 2,
            'representation_id' => 5,
            'nom'               => 'Pierre D.',
            'note'              => 5,
            'commentaire'       => 'Magnifique !',
            'date_creation'     => '2025-06-01 20:00:00',
            'valide'            => true,
        ], $array);
    }

    public function testValidationDefaultsToFalse(): void
    {
        $c = Commentaire::fromArray([
            'representation_id' => '1',
            'nom'               => 'Test',
            'commentaire'       => 'Test commentaire',
        ]);

        $this->assertFalse($c->valide);
    }

    public function testNoteCanBeNull(): void
    {
        $c = new Commentaire(null, 1, 'Auteur', null, 'Sans note', null, false);

        $this->assertNull($c->note);
        $this->assertNull($c->toArray()['note']);
    }
}
