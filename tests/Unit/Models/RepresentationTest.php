<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Representation;
use PHPUnit\Framework\TestCase;

class RepresentationTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'id'                      => '7',
            'piece_id'                => '2',
            'lieu_id'                 => '4',
            'date_debut'              => '2025-06-15 20:00:00',
            'date_fin'                => '2025-06-15 22:00:00',
            'max_spectateurs'         => '200',
            'date_limite_reservation' => '2025-06-14 23:59:00',
            'gratuit'                 => '0',
            'annulee'                 => '0',
        ];

        $rep = Representation::fromArray($data);

        $this->assertSame(7, $rep->id);
        $this->assertSame(2, $rep->pieceId);
        $this->assertSame(4, $rep->lieuId);
        $this->assertSame('2025-06-15 20:00:00', $rep->dateDebut);
        $this->assertSame('2025-06-15 22:00:00', $rep->dateFin);
        $this->assertSame(200, $rep->maxSpectateurs);
        $this->assertSame('2025-06-14 23:59:00', $rep->dateLimiteReservation);
        $this->assertFalse($rep->gratuit);
        $this->assertFalse($rep->annulee);
    }

    public function testFromArrayWithDefaults(): void
    {
        $rep = Representation::fromArray([
            'piece_id'   => '1',
            'date_debut' => '2025-01-01 19:00:00',
        ]);

        $this->assertNull($rep->id);
        $this->assertNull($rep->lieuId);
        $this->assertNull($rep->dateFin);
        $this->assertSame(100, $rep->maxSpectateurs);
        $this->assertNull($rep->dateLimiteReservation);
        $this->assertFalse($rep->gratuit);
        $this->assertFalse($rep->annulee);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $rep = new Representation(1, 2, 3, '2025-07-01 20:00:00', null, 150, null, true, false);

        $array = $rep->toArray();

        $this->assertSame(1, $array['id']);
        $this->assertSame(2, $array['piece_id']);
        $this->assertSame(3, $array['lieu_id']);
        $this->assertSame('2025-07-01 20:00:00', $array['date_debut']);
        $this->assertNull($array['date_fin']);
        $this->assertSame(150, $array['max_spectateurs']);
        $this->assertTrue($array['gratuit']);
        $this->assertFalse($array['annulee']);
    }

    public function testIsReservationOpenReturnsFalseWhenAnnulee(): void
    {
        $rep = new Representation(1, 1, null, date('Y-m-d H:i:s', strtotime('+1 day')), null, 100, null, false, true);

        $this->assertFalse($rep->isReservationOpen());
    }

    public function testIsReservationOpenReturnsTrueForFutureRepresentation(): void
    {
        $rep = new Representation(1, 1, null, date('Y-m-d H:i:s', strtotime('+10 days')), null, 100, null, false, false);

        $this->assertTrue($rep->isReservationOpen());
    }

    public function testIsReservationOpenReturnsFalseForPastRepresentation(): void
    {
        $rep = new Representation(1, 1, null, date('Y-m-d H:i:s', strtotime('-1 day')), null, 100, null, false, false);

        $this->assertFalse($rep->isReservationOpen());
    }

    public function testIsReservationOpenUsesDateLimiteWhenProvided(): void
    {
        $futureDateLimite = date('Y-m-d H:i:s', strtotime('+1 day'));
        $futureDate       = date('Y-m-d H:i:s', strtotime('+5 days'));

        $rep = new Representation(1, 1, null, $futureDate, null, 100, $futureDateLimite, false, false);

        $this->assertTrue($rep->isReservationOpen());
    }

    public function testIsReservationOpenReturnsFalseWhenDateLimitePassed(): void
    {
        $pastDateLimite = date('Y-m-d H:i:s', strtotime('-1 day'));
        $futureDate     = date('Y-m-d H:i:s', strtotime('+5 days'));

        $rep = new Representation(1, 1, null, $futureDate, null, 100, $pastDateLimite, false, false);

        $this->assertFalse($rep->isReservationOpen());
    }
}
