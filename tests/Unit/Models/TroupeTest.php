<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Troupe;
use PHPUnit\Framework\TestCase;

class TroupeTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'id'            => '12',
            'nom'           => 'Les Planches Folles',
            'email_contact' => 'contact@planches.fr',
        ];

        $troupe = Troupe::fromArray($data);

        $this->assertSame(12, $troupe->id);
        $this->assertSame('Les Planches Folles', $troupe->nom);
        $this->assertSame('contact@planches.fr', $troupe->emailContact);
    }

    public function testFromArrayWithoutEmail(): void
    {
        $troupe = Troupe::fromArray(['id' => '7', 'nom' => 'Théâtre du Midi']);

        $this->assertSame(7, $troupe->id);
        $this->assertNull($troupe->emailContact);
    }

    public function testFromArrayWithNullId(): void
    {
        $troupe = Troupe::fromArray(['nom' => 'Nouvelle troupe']);

        $this->assertNull($troupe->id);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $troupe = new Troupe(3, 'Les Masques', 'masques@example.com');

        $array = $troupe->toArray();

        $this->assertSame([
            'id'            => 3,
            'nom'           => 'Les Masques',
            'email_contact' => 'masques@example.com',
        ], $array);
    }

    public function testToArrayWithNullEmail(): void
    {
        $troupe = new Troupe(1, 'Sans Email', null);

        $this->assertNull($troupe->toArray()['email_contact']);
    }
}
