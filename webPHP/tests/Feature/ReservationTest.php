<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private function prestataire(): User
    {
        return User::factory()->create([
            'role'     => 'prestataire',
            'password' => Hash::make('password'),
        ]);
    }

    private function makeHotel(User $user): Hotel
    {
        return Hotel::factory()->create(['user_id' => $user->id]);
    }

    // ── List ────────────────────────────────────────────────────────────────

    public function test_prestataire_can_view_reservations(): void
    {
        $user  = $this->prestataire();
        $hotel = $this->makeHotel($user);

        Reservation::factory()->count(3)->create(['hotel_id' => $hotel->id]);

        $this->actingAs($user)
            ->get('/reservations')
            ->assertStatus(200)
            ->assertSee('Réservations');
    }

    public function test_guest_cannot_view_reservations(): void
    {
        $this->get('/reservations')->assertRedirect('/login');
    }

    // ── Status update ─────────────────────────────────────────────────────

    public function test_prestataire_can_update_reservation_status(): void
    {
        $user        = $this->prestataire();
        $hotel       = $this->makeHotel($user);
        $client      = Client::factory()->create();
        $reservation = Reservation::factory()->create([
            'hotel_id'  => $hotel->id,
            'client_id' => $client->id,
            'status'    => 'en_attente',
        ]);

        $this->actingAs($user)
            ->patch("/reservations/{$reservation->id}/status", ['status' => 'confirmée'])
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'confirmée',
        ]);
    }

    // ── Check-in / Check-out ─────────────────────────────────────────────

    public function test_prestataire_can_checkin_reservation(): void
    {
        $user        = $this->prestataire();
        $hotel       = $this->makeHotel($user);
        $client      = Client::factory()->create();
        $reservation = Reservation::factory()->create([
            'hotel_id'  => $hotel->id,
            'client_id' => $client->id,
            'status'    => 'confirmée',
        ]);

        $this->actingAs($user)
            ->post("/reservations/{$reservation->id}/checkin")
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'arrivée',
        ]);
    }

    public function test_prestataire_can_checkout_reservation(): void
    {
        $user        = $this->prestataire();
        $hotel       = $this->makeHotel($user);
        $client      = Client::factory()->create();
        $reservation = Reservation::factory()->create([
            'hotel_id'  => $hotel->id,
            'client_id' => $client->id,
            'status'    => 'arrivée',
        ]);

        $this->actingAs($user)
            ->post("/reservations/{$reservation->id}/checkout")
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'départie',
        ]);
    }
}
