<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JekoClient
{
    /**
     * Normalise un libellé de moyen de paiement (web ou mobile, FR ou EN) vers le
     * code attendu par l'API Jèko. Retourne null pour un moyen non géré par Jèko
     * (Espèces, Virement) — ces paiements restent confirmés manuellement.
     */
    public static function mapPaymentMethod(?string $method): ?string
    {
        if (! $method) {
            return null;
        }

        $normalized = strtolower($method);

        return match (true) {
            str_contains($normalized, 'orange') => 'orange',
            str_contains($normalized, 'wave') => 'wave',
            str_contains($normalized, 'mtn') => 'mtn',
            str_contains($normalized, 'moov') => 'moov',
            str_contains($normalized, 'djamo') => 'djamo',
            default => null,
        };
    }

    public static function createPaymentRequest(array $data): array
    {
        $response = Http::withHeaders([
            'X-API-KEY' => config('services.jeko.api_key'),
            'X-API-KEY-ID' => config('services.jeko.api_key_id'),
        ])->post(config('services.jeko.base_url') . '/partner_api/payment_requests', $data);

        return $response->json() ?? [];
    }

    public static function getPaymentRequest(string $id): array
    {
        $response = Http::withHeaders([
            'X-API-KEY' => config('services.jeko.api_key'),
            'X-API-KEY-ID' => config('services.jeko.api_key_id'),
        ])->get(config('services.jeko.base_url') . "/partner_api/payment_requests/{$id}");

        return $response->json() ?? [];
    }

    /**
     * Crée un contact bénéficiaire (mobile money ou bancaire) pour les virements sortants.
     * `paymentMethod` : wave|orange_money|mtn|moov|djamo|bank.
     */
    public static function createContact(array $data): array
    {
        $response = Http::withHeaders([
            'X-API-KEY' => config('services.jeko.api_key'),
            'X-API-KEY-ID' => config('services.jeko.api_key_id'),
        ])->post(config('services.jeko.base_url') . '/partner_api/contacts', $data);

        return $response->json() ?? [];
    }

    public static function deleteContact(string $contactId): void
    {
        Http::withHeaders([
            'X-API-KEY' => config('services.jeko.api_key'),
            'X-API-KEY-ID' => config('services.jeko.api_key_id'),
        ])->delete(config('services.jeko.base_url') . "/partner_api/contacts/{$contactId}");
    }

    /**
     * Crée un virement sortant (reversement) vers un contact bénéficiaire déjà enregistré.
     * Réponse de succès : contient `status` (pending|success). Réponse d'erreur : pas de
     * `status`, contient `id` (code d'erreur) et `message` — voir la doc transfers/failure-reasons.
     */
    public static function createTransfer(array $data): array
    {
        $response = Http::withHeaders([
            'X-API-KEY' => config('services.jeko.api_key'),
            'X-API-KEY-ID' => config('services.jeko.api_key_id'),
        ])->post(config('services.jeko.base_url') . '/partner_api/transfers', $data);

        return $response->json() ?? [];
    }

    /**
     * Vérifie la signature HMAC-SHA256 d'un webhook Jèko (en-tête Jeko-Signature)
     * sur le corps BRUT de la requête, en comparaison à temps constant.
     */
    public static function verifyWebhookSignature(string $rawBody, ?string $signature): bool
    {
        if (! $signature) {
            return false;
        }

        $secret = config('services.jeko.webhook_secret');
        if (! $secret) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, $signature);
    }
}
