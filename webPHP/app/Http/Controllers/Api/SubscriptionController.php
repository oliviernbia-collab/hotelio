<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\JekoClient;
use Illuminate\Http\Request;

/**
 * Mirror de DashboardController (sections abonnement) — même flux Jèko que le web
 * (chooseSubscriptionPlan/paySubscription/subscriptions/recordSubscriptionPayment) : le
 * paiement passe toujours par une vraie demande Jèko, l'activation reste déclenchée
 * uniquement par le webhook partagé (JekoWebhookController), jamais par cet endpoint
 * directement — cf. backendMobile/src/controllers/subscriptionController.js::pay qui suit
 * exactement le même principe sur la même table `subscriptions`.
 */
class SubscriptionController extends Controller
{
    public function plans()
    {
        return response()->json(
            SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get()
        );
    }

    public function mine(Request $request)
    {
        return response()->json($request->user()->currentSubscription());
    }

    public function choose(Request $request)
    {
        if ($request->user()->currentSubscription()) {
            return response()->json(['message' => 'Vous avez déjà un abonnement.'], 422);
        }

        $data = $request->validate(['plan' => 'required|string|in:starter,business,premium']);
        $plan = SubscriptionPlan::where('slug', $data['plan'])->firstOrFail();

        $subscription = Subscription::create([
            'user_id'               => $request->user()->id,
            'subscription_plan_id'  => $plan->id,
            'status'                => 'pending_payment',
        ]);

        return response()->json($subscription, 201);
    }

    public function pay(Request $request)
    {
        $subscription = $request->user()->currentSubscription();
        if (! $subscription) {
            return response()->json(['message' => 'Aucun abonnement trouvé sur votre compte.'], 422);
        }

        $data = $request->validate(['payment_method' => 'required|string|max:80']);
        $jekoMethod = JekoClient::mapPaymentMethod($data['payment_method']);
        if (! $jekoMethod) {
            return response()->json(['message' => 'Moyen de paiement non pris en charge pour le paiement en ligne.'], 422);
        }

        $reference = 'HOTELIO-SUB-' . $subscription->id . '-' . now()->timestamp;

        $result = JekoClient::createPaymentRequest([
            'storeId'     => config('services.jeko.store_id'),
            'amountCents' => (int) round($subscription->plan->price_monthly * 100),
            'currency'    => 'XOF',
            'reference'   => $reference,
            'paymentDetails' => [
                'type' => 'redirect',
                'data' => [
                    'paymentMethod' => $jekoMethod,
                    'successUrl'    => route('subscription.payment.callback.mobile', ['result' => 'success']),
                    'errorUrl'      => route('subscription.payment.callback.mobile', ['result' => 'error']),
                ],
            ],
        ]);

        if (empty($result['redirectUrl'])) {
            return response()->json(['message' => $result['message'] ?? 'Impossible de lancer le paiement pour le moment. Réessayez ou choisissez un autre moyen de paiement.'], 422);
        }

        $subscription->update([
            'jeko_reference'          => $reference,
            'jeko_payment_request_id' => $result['id'] ?? null,
        ]);

        return response()->json(['redirectUrl' => $result['redirectUrl']]);
    }

    /** ─── Admin ──────────────────────────────────────────────────────────── */

    public function adminIndex(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function adminRecordPayment(Request $request, Subscription $subscription)
    {
        $amount = $request->integer('amount') ?: $subscription->plan->price_monthly;
        app(DashboardController::class)->extendSubscriptionPeriod(
            $subscription, $amount, $request->get('payment_method'), $request->user()->id
        );

        return response()->json($subscription->fresh());
    }
}
