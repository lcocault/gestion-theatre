<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Programmation;
use PHPUnit\Framework\TestCase;

class ProgrammationTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'id'               => '4',
            'nom'              => 'Festival de printemps',
            'date_debut'       => '2025-04-01',
            'date_fin'         => '2025-04-30',
            'affiche_vignette' => 'https://example.com/festival.jpg',
        ];

        $prog = Programmation::fromArray($data);

        $this->assertSame(4, $prog->id);
        $this->assertSame('Festival de printemps', $prog->nom);
        $this->assertSame('2025-04-01', $prog->dateDebut);
        $this->assertSame('2025-04-30', $prog->dateFin);
        $this->assertSame('https://example.com/festival.jpg', $prog->afficheVignette);
    }

    public function testFromArrayWithoutAffiche(): void
    {
        $prog = Programmation::fromArray([
            'id'         => '2',
            'nom'        => 'Saison été',
            'date_debut' => '2025-07-01',
            'date_fin'   => '2025-08-31',
        ]);

        $this->assertNull($prog->afficheVignette);
    }

    public function testFromArrayWithNullId(): void
    {
        $prog = Programmation::fromArray([
            'nom'        => 'Nouvelle prog',
            'date_debut' => '2025-01-01',
            'date_fin'   => '2025-01-31',
        ]);

        $this->assertNull($prog->id);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $prog = new Programmation(1, 'Automne théâtral', '2025-10-01', '2025-10-31', null);

        $array = $prog->toArray();

        $this->assertSame([
            'id'               => 1,
            'nom'              => 'Automne théâtral',
            'date_debut'       => '2025-10-01',
            'date_fin'         => '2025-10-31',
            'affiche_vignette' => null,
        ], $array);
    }

    public function testIsActiveReturnsTrueWhenCurrentDateIsWithinRange(): void
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow  = date('Y-m-d', strtotime('+1 day'));
        $prog = new Programmation(1, 'En cours', $yesterday, $tomorrow, null);

        $this->assertTrue($prog->isActive());
    }

    public function testIsActiveReturnsFalseForFutureProgrammation(): void
    {
        $prog = new Programmation(1, 'Future', date('Y-m-d', strtotime('+5 days')), date('Y-m-d', strtotime('+10 days')), null);

        $this->assertFalse($prog->isActive());
    }

    public function testIsActiveReturnsFalseForPastProgrammation(): void
    {
        $prog = new Programmation(1, 'Passée', date('Y-m-d', strtotime('-10 days')), date('Y-m-d', strtotime('-1 day')), null);

        $this->assertFalse($prog->isActive());
    }

    public function testIsActiveReturnsTrueWhenStartDateIsToday(): void
    {
        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $prog = new Programmation(1, 'Commence aujourd\'hui', $today, $tomorrow, null);

        $this->assertTrue($prog->isActive());
    }

    public function testIsActiveReturnsTrueWhenEndDateIsToday(): void
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $today     = date('Y-m-d');
        $prog = new Programmation(1, 'Se termine aujourd\'hui', $yesterday, $today, null);

        $this->assertTrue($prog->isActive());
    }
}
