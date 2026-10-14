<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;

/** Mirror en lecture seule de settings/index.blade.php (web) — aucun réglage modifiable. */
class SettingController extends Controller
{
    public function index()
    {
        return response()->json([
            'platform' => [
                'name'            => 'Hôtelio',
                'laravel_version' => app()->version(),
                'php_version'     => phpversion(),
                'environment'     => app()->environment(),
                'debug'           => (bool) config('app.debug'),
            ],
            'preferences' => [
                'default_locale' => 'fr',
                'timezone'       => 'Africa/Abidjan',
                'currency'       => 'XOF',
                'country'        => "Côte d'Ivoire",
                'date_format'    => 'dd/mm/yyyy',
            ],
            'database' => [
                'connection'   => config('database.default'),
                'hotels'       => Hotel::count(),
                'reservations' => Reservation::count(),
                'users'        => User::count(),
                'reviews'      => Review::count(),
            ],
            'mail' => [
                'mailer'       => config('mail.default'),
                'from_address' => config('mail.from.address'),
                'from_name'    => config('mail.from.name'),
            ],
            'security' => [
                'https'   => true,
                'csrf'    => true,
                'hashing' => 'bcrypt',
            ],
        ]);
    }
}
