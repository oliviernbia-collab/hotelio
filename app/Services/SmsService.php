<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoie un SMS. En production, brancher sur Orange API, Africa's Talking, etc.
     * En développement, le message est loggé dans storage/logs/laravel.log
     */
    public function send(string $phone, string $message): bool
    {
        $driver = config('sms.driver', 'log');

        return match ($driver) {
            'log'     => $this->sendLog($phone, $message),
            'orange'  => $this->sendOrange($phone, $message),
            'africastalking' => $this->sendAfricasTalking($phone, $message),
            default   => $this->sendLog($phone, $message),
        };
    }

    public function sendReservationConfirmed(string $phone, string $code, string $hotelName): bool
    {
        return $this->send($phone,
            "Hôtelio ✓ Réservation #{$code} confirmée à {$hotelName}. Conservez ce code pour votre check-in."
        );
    }

    public function sendPaymentOtp(string $phone, string $otp): bool
    {
        return $this->send($phone,
            "Hôtelio — Code de confirmation : {$otp}. Valable 5 minutes. Ne le partagez pas."
        );
    }

    public function sendCheckinReminder(string $phone, string $hotelName, string $date): bool
    {
        return $this->send($phone,
            "Hôtelio — Rappel : votre check-in à {$hotelName} est prévu le {$date}. À bientôt !"
        );
    }

    // ── Drivers ──────────────────────────────────────────────────────────────

    private function sendLog(string $phone, string $message): bool
    {
        Log::channel('stack')->info('[SMS]', [
            'to'      => $phone,
            'message' => $message,
            'sent_at' => now()->toDateTimeString(),
        ]);
        return true;
    }

    private function sendOrange(string $phone, string $message): bool
    {
        // Brancher sur l'API Orange SMS CI
        // POST https://api.orange.com/smsmessaging/v1/outbound/.../requests
        // Authorization: Bearer {ORANGE_API_TOKEN}
        Log::warning('[SMS Orange] driver non configuré — message loggé', ['to' => $phone]);
        return $this->sendLog($phone, $message);
    }

    private function sendAfricasTalking(string $phone, string $message): bool
    {
        // Brancher sur Africa's Talking API
        // https://africastalking.com/sms
        Log::warning('[SMS AfricasTalking] driver non configuré — message loggé', ['to' => $phone]);
        return $this->sendLog($phone, $message);
    }
}
