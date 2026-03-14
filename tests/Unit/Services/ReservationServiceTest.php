<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Reservation;
use App\Repositories\RepresentationRepository;
use App\Repositories\ReservationRepository;
use App\Services\EmailService;
use App\Services\ReservationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReservationServiceTest extends TestCase
{
    private ReservationRepository&MockObject $reservationRepo;
    private RepresentationRepository&MockObject $representationRepo;
    private EmailService&MockObject $emailService;
    private ReservationService $service;

    protected function setUp(): void
    {
        $this->reservationRepo    = $this->createMock(ReservationRepository::class);
        $this->representationRepo = $this->createMock(RepresentationRepository::class);
        $this->emailService       = $this->createMock(EmailService::class);

        $this->service = new ReservationService(
            $this->reservationRepo,
            $this->representationRepo,
            $this->emailService,
        );
    }

    // ─── creerReservation – cas d'erreur ─────────────────────────────────────

    public function testCreerReservationThrowsWhenRepresentationNotFound(): void
    {
        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Représentation introuvable.');

        $this->service->creerReservation(99, $this->validFormData(), $this->validPlaces());
    }

    public function testCreerReservationThrowsWhenAnnulee(): void
    {
        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn($this->representationData(['annulee' => true]));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cette représentation a été annulée.');

        $this->service->creerReservation(1, $this->validFormData(), $this->validPlaces());
    }

    public function testCreerReservationThrowsWhenDateLimiteDepassee(): void
    {
        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn($this->representationData([
                'date_limite_reservation' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ]));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Les réservations sont closes pour cette représentation.');

        $this->service->creerReservation(1, $this->validFormData(), $this->validPlaces());
    }

    public function testCreerReservationThrowsWhenCapaciteInsuffisante(): void
    {
        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn($this->representationData(['max_spectateurs' => 10]));

        $this->representationRepo
            ->method('countReservations')
            ->willReturn(9);

        $places = [['categorie' => 'Adulte', 'quantite' => 5, 'prix_unitaire' => 10.0]];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Il n\'y a pas assez de places disponibles.');

        $this->service->creerReservation(1, $this->validFormData(), $places);
    }

    // ─── creerReservation – cas nominal ──────────────────────────────────────

    public function testCreerReservationSavesAndSendsConfirmation(): void
    {
        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn($this->representationData());

        $this->representationRepo
            ->method('countReservations')
            ->willReturn(0);

        $savedReservation = new Reservation(
            'uuid-1234',
            1,
            'Dupont',
            'Jean',
            null,
            'jean@example.com',
            null,
            false,
            false,
            Reservation::STATUT_RESERVE,
            '2025-06-01 10:00:00',
        );

        $this->reservationRepo
            ->expects($this->once())
            ->method('save')
            ->willReturn($savedReservation);

        $this->reservationRepo
            ->expects($this->once())
            ->method('savePlaces')
            ->with('uuid-1234', $this->validPlaces());

        $this->emailService
            ->expects($this->once())
            ->method('sendConfirmationReservation')
            ->with(
                'jean@example.com',
                'Jean Dupont',
                'Tartuffe',
                $this->anything(),
                'uuid-1234',
                $this->validPlaces(),
            );

        $result = $this->service->creerReservation(1, $this->validFormData(), $this->validPlaces());

        $this->assertSame('uuid-1234', $result->id);
        $this->assertSame(Reservation::STATUT_RESERVE, $result->statut);
    }

    public function testCreerReservationPassesCorrectFormDataToModel(): void
    {
        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn($this->representationData());

        $this->representationRepo
            ->method('countReservations')
            ->willReturn(0);

        $this->emailService->method('sendConfirmationReservation')->willReturn(true);

        $capturedReservation = null;
        $this->reservationRepo
            ->method('save')
            ->willReturnCallback(function (Reservation $res) use (&$capturedReservation) {
                $capturedReservation = $res;
                return new Reservation('uuid-test', $res->representationId, $res->nom, $res->prenom,
                    $res->telephone, $res->email, $res->sourceDecouverte,
                    $res->handicapVisuelAuditif, $res->handicapMoteur, $res->statut);
            });

        $formData = [
            'nom'                     => 'Martin',
            'prenom'                  => 'Sophie',
            'email'                   => 'sophie@example.com',
            'telephone'               => '0612345678',
            'source_decouverte'       => 'Affiche',
            'handicap_visuel_auditif' => '1',
            'handicap_moteur'         => '',
        ];

        $this->service->creerReservation(1, $formData, $this->validPlaces());

        $this->assertNotNull($capturedReservation);
        $this->assertSame('Martin', $capturedReservation->nom);
        $this->assertSame('Sophie', $capturedReservation->prenom);
        $this->assertSame('sophie@example.com', $capturedReservation->email);
        $this->assertSame('0612345678', $capturedReservation->telephone);
        $this->assertSame('Affiche', $capturedReservation->sourceDecouverte);
        $this->assertTrue($capturedReservation->handicapVisuelAuditif);
        $this->assertFalse($capturedReservation->handicapMoteur);
        $this->assertSame(Reservation::STATUT_RESERVE, $capturedReservation->statut);
    }

    // ─── annulerReservation – cas d'erreur ───────────────────────────────────

    public function testAnnulerReservationThrowsWhenNotFound(): void
    {
        $this->reservationRepo
            ->method('findById')
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Réservation introuvable.');

        $this->service->annulerReservation('uuid-non-existant');
    }

    public function testAnnulerReservationThrowsWhenAlreadyCancelled(): void
    {
        $reservation = new Reservation(
            'uuid-1',
            1,
            'Dupont',
            'Jean',
            null,
            'jean@example.com',
            null,
            false,
            false,
            Reservation::STATUT_ANNULE,
        );

        $this->reservationRepo
            ->method('findById')
            ->willReturn($reservation);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cette réservation est déjà annulée.');

        $this->service->annulerReservation('uuid-1');
    }

    // ─── annulerReservation – cas nominal ────────────────────────────────────

    public function testAnnulerReservationUpdatesStatutAndSendsEmail(): void
    {
        $reservation = new Reservation(
            'uuid-2',
            5,
            'Bernard',
            'Claire',
            null,
            'claire@example.com',
            null,
            false,
            false,
            Reservation::STATUT_RESERVE,
        );

        $this->reservationRepo
            ->method('findById')
            ->willReturn($reservation);

        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn($this->representationData());

        $this->reservationRepo
            ->expects($this->once())
            ->method('updateStatut')
            ->with('uuid-2', Reservation::STATUT_ANNULE);

        $this->emailService
            ->expects($this->once())
            ->method('sendAnnulationReservation')
            ->with(
                'claire@example.com',
                'Claire Bernard',
                'Tartuffe',
                $this->anything(),
            );

        $this->service->annulerReservation('uuid-2');
    }

    public function testAnnulerReservationStillUpdatesStatutWhenRepresentationMissing(): void
    {
        $reservation = new Reservation(
            'uuid-3',
            42,
            'Durant',
            'Paul',
            null,
            'paul@example.com',
            null,
            false,
            false,
            Reservation::STATUT_RESERVE,
        );

        $this->reservationRepo
            ->method('findById')
            ->willReturn($reservation);

        $this->representationRepo
            ->method('findByIdWithDetails')
            ->willReturn(null);

        // Status still updated even when representation is not found
        $this->reservationRepo
            ->expects($this->once())
            ->method('updateStatut')
            ->with('uuid-3', Reservation::STATUT_ANNULE);

        // No email sent when representation is not found
        $this->emailService
            ->expects($this->never())
            ->method('sendAnnulationReservation');

        $this->service->annulerReservation('uuid-3');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function representationData(array $overrides = []): array
    {
        return array_merge([
            'id'                      => 1,
            'piece_id'                => 1,
            'piece_titre'             => 'Tartuffe',
            'lieu_id'                 => 1,
            'lieu_nom'                => 'Salle des fêtes',
            'date_debut'              => '2030-06-15 20:00:00',
            'max_spectateurs'         => 100,
            'date_limite_reservation' => null,
            'gratuit'                 => false,
            'annulee'                 => false,
        ], $overrides);
    }

    private function validFormData(): array
    {
        return [
            'nom'    => 'Dupont',
            'prenom' => 'Jean',
            'email'  => 'jean@example.com',
        ];
    }

    private function validPlaces(): array
    {
        return [
            ['categorie' => 'Adulte', 'quantite' => 2, 'prix_unitaire' => 12.0],
        ];
    }
}
