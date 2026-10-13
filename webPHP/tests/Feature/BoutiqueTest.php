<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BoutiqueTest extends TestCase
{
    use RefreshDatabase;

    private function client(): User
    {
        return User::factory()->create([
            'role'     => 'client',
            'password' => Hash::make('password'),
        ]);
    }

    private function makeProduct(array $overrides = []): Product
    {
        return Product::factory()->create(array_merge([
            'name'  => 'Savon artisanal',
            'price' => 5000,
            'stock' => 10,
        ], $overrides));
    }

    // ── Public boutique ──────────────────────────────────────────────────

    public function test_boutique_page_is_publicly_accessible(): void
    {
        $this->get('/boutique')->assertStatus(200);
    }

    public function test_product_detail_page_is_publicly_accessible(): void
    {
        $product = $this->makeProduct();
        $this->get("/produits/{$product->id}")->assertStatus(200)->assertSee($product->name);
    }

    // ── Panier ───────────────────────────────────────────────────────────

    public function test_guest_can_add_product_to_cart(): void
    {
        $product = $this->makeProduct();

        $this->post("/panier/add/{$product->id}")
            ->assertRedirect();

        $this->assertNotEmpty(session('panier'));
    }

    public function test_cart_quantity_increases_on_repeated_add(): void
    {
        $product = $this->makeProduct();

        $this->post("/panier/add/{$product->id}");
        $this->post("/panier/add/{$product->id}");

        $panier = session('panier');
        $this->assertEquals(2, $panier[$product->id] ?? 0);
    }

    public function test_cart_quantity_decreases_on_decrease(): void
    {
        $product = $this->makeProduct();

        $this->post("/panier/add/{$product->id}");
        $this->post("/panier/add/{$product->id}");
        $this->post("/panier/decrease/{$product->id}");

        $panier = session('panier');
        $this->assertEquals(1, $panier[$product->id] ?? 0);
    }

    // ── Checkout ─────────────────────────────────────────────────────────

    public function test_checkout_page_shows_empty_state(): void
    {
        $user = $this->client();
        $this->actingAs($user)->get('/commande/create')->assertStatus(200);
    }

    public function test_authenticated_user_can_access_checkout_with_cart(): void
    {
        $user    = $this->client();
        $product = $this->makeProduct();

        $this->actingAs($user)
            ->withSession(['panier' => [$product->id => 1]])
            ->get('/commande/create')
            ->assertStatus(200);
    }

    // ── Buy-now order ────────────────────────────────────────────────────

    public function test_buy_now_creates_commande_with_payment_reference(): void
    {
        $user    = $this->client();
        $product = $this->makeProduct(['stock' => 5]);

        $response = $this->actingAs($user)->post('/commande/acheter', [
            'product_id'     => $product->id,
            'qty'            => 1,
            'payment_method' => 'orange_money',
            'phone_number'   => '07 00 00 00 00',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('commandes', [
            'user_id'           => $user->id,
            'mode_paiement'     => 'orange_money',
            'payment_reference' => '+225 07 00 00 00 00',
        ]);
    }

    public function test_buy_now_masks_card_number(): void
    {
        $user    = $this->client();
        $product = $this->makeProduct(['stock' => 5]);

        $this->actingAs($user)->post('/commande/acheter', [
            'product_id'     => $product->id,
            'qty'            => 1,
            'payment_method' => 'carte_bancaire',
            'card_holder'    => 'Jean Dupont',
            'card_number'    => '4111 1111 1111 1234',
            'card_expiry'    => '12/28',
            'card_cvv'       => '123',
        ]);

        $this->assertDatabaseHas('commandes', [
            'user_id'           => $user->id,
            'payment_reference' => 'JEAN DUPONT · **** **** **** 1234',
        ]);
    }

    public function test_buy_now_requires_payment_method(): void
    {
        $user    = $this->client();
        $product = $this->makeProduct();

        $this->actingAs($user)->post('/commande/acheter', [
            'product_id' => $product->id,
            'qty'        => 1,
        ])->assertSessionHasErrors(['payment_method']);
    }

    // ── Cart checkout order ──────────────────────────────────────────────

    public function test_store_creates_commande_from_cart(): void
    {
        $user    = $this->client();
        $product = $this->makeProduct(['stock' => 10]);

        $this->actingAs($user)
            ->withSession(['panier' => [$product->id => 2]])
            ->post('/commande/store', [
                'payment_method' => 'wave',
                'phone_number'   => '01 02 03 04 05',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('commandes', [
            'user_id'       => $user->id,
            'mode_paiement' => 'wave',
        ]);
    }
}
