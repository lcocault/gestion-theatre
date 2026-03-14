<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service d'envoi des emails.
 */
class EmailService
{
    public function sendConfirmationReservation(
        string $toEmail,
        string $toName,
        string $representationTitre,
        string $representationDate,
        string $reservationId,
        array $places,
    ): bool {
        $subject = 'Confirmation de votre réservation – ' . APP_NAME;
        $annulationUrl = APP_URL . '/reservation/annuler/' . urlencode($reservationId);

        $body = "Bonjour {$toName},\n\n";
        $body .= "Votre réservation pour « {$representationTitre} » le {$representationDate} a bien été enregistrée.\n\n";
        $body .= "Référence de réservation : {$reservationId}\n\n";
        $body .= "Détail des places :\n";
        foreach ($places as $place) {
            $body .= "  - {$place['categorie']} : {$place['quantite']} place(s) × {$place['prix_unitaire']} €\n";
        }
        $body .= "\nPour annuler votre réservation, cliquez sur ce lien :\n{$annulationUrl}\n\n";
        $body .= "À bientôt,\n" . APP_NAME;

        return $this->sendMail($toEmail, $subject, $body);
    }

    public function sendAnnulationReservation(
        string $toEmail,
        string $toName,
        string $representationTitre,
        string $representationDate,
    ): bool {
        $subject = 'Annulation de votre réservation – ' . APP_NAME;

        $body = "Bonjour {$toName},\n\n";
        $body .= "Votre réservation pour « {$representationTitre} » le {$representationDate} a bien été annulée.\n\n";
        $body .= "À bientôt,\n" . APP_NAME;

        return $this->sendMail($toEmail, $subject, $body);
    }

    public function sendAnnulationRepresentation(
        string $toEmail,
        string $toName,
        string $representationTitre,
        string $representationDate,
        ?string $replacementInfo = null,
    ): bool {
        $subject = 'Annulation de la représentation – ' . APP_NAME;

        $body = "Bonjour {$toName},\n\n";
        $body .= "Nous vous informons que la représentation de « {$representationTitre} »";
        $body .= " prévue le {$representationDate} a été annulée.\n\n";

        if ($replacementInfo !== null) {
            $body .= "Une représentation de remplacement est proposée :\n{$replacementInfo}\n\n";
        }

        $body .= "Votre réservation est automatiquement annulée.\n\n";
        $body .= "Nous nous excusons pour la gêne occasionnée.\n\n";
        $body .= "Cordialement,\n" . APP_NAME;

        return $this->sendMail($toEmail, $subject, $body);
    }

    private function sendMail(string $to, string $subject, string $body): bool
    {
        $headers = [
            'From'         => MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
            'Reply-To'     => MAIL_FROM,
            'Content-Type' => 'text/plain; charset=UTF-8',
            'MIME-Version' => '1.0',
        ];

        $headerStr = implode("\r\n", array_map(
            fn($k, $v) => "{$k}: {$v}",
            array_keys($headers),
            array_values($headers)
        ));

        return mail($to, $subject, $body, $headerStr);
    }
}
