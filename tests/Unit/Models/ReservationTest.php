<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Reservation;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'id'                      => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
            'representation_id'       => '5',
            'nom'                     => 'Dupont',
            'prenom'                  => 'Jean',
            'telephone'               => '0612345678',
            'email'                   => 'jean.dupont@example.com',
            'source_decouverte'       => 'Affiche',
            'handicap_visuel_auditif' => '1',
            'handicap_moteur'         => '0',
            'statut'                  => 'reserve',
            'date_creation'           => '2025-03-01 10:00:00',
        ];

        $res = Reservation::fromArray($data);

        $this->assertSame('f47ac10b-58cc-4372-a567-0e02b2c3d479', $res->id);
        $this->assertSame(5, $res->representationId);
        $this->assertSame('Dupont', $res->nom);
        $this->assertSame('Jean', $res->prenom);
        $this->assertSame('0612345678', $res->telephone);
        $this->assertSame('jean.dupont@example.com', $res->email);
        $this->assertSame('Affiche', $res->sourceDecouverte);
        $this->assertTrue($res->handicapVisuelAuditif);
        $this->assertFalse($res->handicapMoteur);
        $this->assertSame('reserve', $res->statut);
        $this->assertSame('2025-03-01 10:00:00', $res->dateCreation);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $res = Reservation::fromArray([
            'representation_id' => '1',
            'nom'               => 'Martin',
            'prenom'            => 'Alice',
            'email'             => 'alice@example.com',
        ]);

        $this->assertNull($res->id);
        $this->assertNull($res->telephone);
        $this->assertNull($res->sourceDecouverte);
        $this->assertFalse($res->handicapVisuelAuditif);
        $this->assertFalse($res->handicapMoteur);
        $this->assertSame(Reservation::STATUT_RESERVE, $res->statut);
        $this->assertNull($res->dateCreation);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $res = new Reservation(
            'uuid-test',
            3,
            'Leroy',
            'Marie',
            null,
            'marie@example.com',
            'Bouche à oreille',
            false,
            true,
            Reservation::STATUT_CONFIRME,
            '2025-04-15 09:00:00',
        );

        $array = $res->toArray();

        $this->assertSame('uuid-test', $array['id']);
        $this->assertSame(3, $array['representation_id']);
        $this->assertSame('Leroy', $array['nom']);
        $this->assertSame('Marie', $array['prenom']);
        $this->assertNull($array['telephone']);
        $this->assertSame('marie@example.com', $array['email']);
        $this->assertSame('Bouche à oreille', $array['source_decouverte']);
        $this->assertFalse($array['handicap_visuel_auditif']);
        $this->assertTrue($array['handicap_moteur']);
        $this->assertSame(Reservation::STATUT_CONFIRME, $array['statut']);
        $this->assertSame('2025-04-15 09:00:00', $array['date_creation']);
    }

    public function testStatutConstants(): void
    {
        $this->assertSame('reserve', Reservation::STATUT_RESERVE);
        $this->assertSame('confirme', Reservation::STATUT_CONFIRME);
        $this->assertSame('annule', Reservation::STATUT_ANNULE);
    }

    public function testDefaultStatutIsReserve(): void
    {
        $res = Reservation::fromArray([
            'representation_id' => '1',
            'nom'               => 'Test',
            'prenom'            => 'User',
            'email'             => 'test@example.com',
        ]);

        $this->assertSame(Reservation::STATUT_RESERVE, $res->statut);
    }
}
