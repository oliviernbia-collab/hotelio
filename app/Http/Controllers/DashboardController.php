<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Hotel;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Pool;
use App\Models\Product;
use App\Models\Review;
use App\Mail\ReservationConfirmed;
use App\Notifications\ReservationCreatedNotification;
use App\Notifications\ReservationConfirmedNotification;
use App\Notifications\ReservationCancelledNotification;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Ticket;
use App\Models\User;
use App\Services\CloudinaryUploader;
use App\Services\JekoClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function index()
    {
        $hotels       = Hotel::withCount(['rooms', 'reservations'])->get();
        $reservations = Reservation::with(['client:id,first_name,last_name', 'hotel:id,short_name'])
            ->latest('created_at')->limit(8)->get();
        $clients      = Client::latest()->limit(8)->get();

        return view('dashboard', compact('hotels', 'reservations', 'clients'));
    }

    public function admin()
    {
        $totalUsers        = User::count();
        $totalHotels       = Hotel::count();
        $totalReservations = Reservation::count();
        $totalRevenue      = Reservation::where('paid', true)->sum('total');
        $totalClients      = Client::count();
        $pendingReviews    = Review::where('status', 'pending')->count();

        $recentReservations = Reservation::with(['hotel', 'client'])
            ->latest()->limit(8)->get();

        $recentUsers = User::latest()->limit(6)->get();

        $topHotels = Hotel::orderByDesc('revenue')->take(5)->get();

        $reservationsByStatus = [
            'confirmée'  => Reservation::where('status', 'confirmée')->count(),
            'en_attente' => Reservation::where('status', 'en_attente')->count(),
            'annulée'    => Reservation::where('status', 'annulée')->count(),
        ];

        return view('admin.index', compact(
            'totalUsers', 'totalHotels', 'totalReservations', 'totalRevenue',
            'totalClients', 'pendingReviews', 'recentReservations', 'recentUsers',
            'topHotels', 'reservationsByStatus'
        ));
    }

    public function prestataire()
    {
        $user = Auth::user();

        $hotelIds = $user->getHotelIds();
        $myHotels = Hotel::whereIn('id', $hotelIds)->get();

        $totalRooms        = Room::whereIn('hotel_id', $hotelIds)->count();
        $availableRooms    = Room::whereIn('hotel_id', $hotelIds)->where('status', 'disponible')->count();
        $activeReservations = Reservation::whereIn('hotel_id', $hotelIds)->where('status', 'confirmée')->count();
        $totalRevenue      = Reservation::whereIn('hotel_id', $hotelIds)->where('paid', true)->sum('total');
        $totalClients      = Reservation::whereIn('hotel_id', $hotelIds)->distinct('client_id')->count('client_id');

        $recentReservations = Reservation::with(['hotel', 'client'])
            ->whereIn('hotel_id', $hotelIds)
            ->latest()->limit(8)->get();

        $todayCheckins  = Reservation::whereIn('hotel_id', $hotelIds)
            ->whereDate('checkin', today())->count();
        $todayCheckouts = Reservation::whereIn('hotel_id', $hotelIds)
            ->whereDate('checkout', today())->count();

        $subscription = $user->currentSubscription();
        $plans = $subscription ? collect() : \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();

        return view('prestataire.index', compact(
            'myHotels', 'totalRooms', 'availableRooms', 'activeReservations',
            'totalRevenue', 'totalClients', 'recentReservations',
            'todayCheckins', 'todayCheckouts', 'subscription', 'plans'
        ));
    }

    public function client()
    {
        $user   = Auth::user();
        $client = Client::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $hotels = Hotel::orderByDesc('rating')->get();

        $clientReservations = $client
            ? Reservation::where('client_id', $client->id)
                ->with(['hotel'])
                ->latest()->limit(5)->get()
            : collect();

        $totalSpend = $client
            ? Reservation::where('client_id', $client->id)->where('paid', true)->sum('total')
            : 0;

        $loyaltyPoints = (int) ($totalSpend / 1000);

        $favoriteIds = $user->favoriteHotels()->pluck('hotel_id');

        return view('client.index', compact('hotels', 'clientReservations', 'client', 'totalSpend', 'loyaltyPoints', 'favoriteIds'));
    }

    public function hotels(Request $request)
    {
        $user   = Auth::user();
        $filter = $request->query('filter');
        $scope  = $request->query('scope', 'mine'); // 'mine' = mes hôtels seulement, 'tous' = tous les hôtels
        $vue    = $request->query('vue');            // 'mes' | 'autres' (uniquement pour scope=tous)

        if ($scope === 'tous') {
            $query = Hotel::query();
            if ($vue === 'mes') {
                $query->where('user_id', $user->id);
            } elseif ($vue === 'autres') {
                $query->where('user_id', '!=', $user->id);
            }
        } elseif ($user->role === 'prestataire') {
            $query = Hotel::where('user_id', $user->id);
        } else {
            $query = Hotel::query();
        }

        $hotels = $query->withCount(['rooms', 'reservations'])
            ->when($filter, fn ($q) => $q->where('category', $filter))
            ->get();

        return view('hotels.index', compact('hotels', 'filter', 'scope', 'vue'));
    }

    public function hotel(Hotel $hotel)
    {
        $rooms = Room::where('hotel_id', $hotel->id)->get();
        $reservations = $hotel->reservations()
            ->with(['client:id,first_name,last_name,phone'])
            ->where('checkout', '>=', now())
            ->latest()
            ->limit(6)
            ->get();

        $reviews = Review::where('hotel_id', $hotel->id)
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->get();

        $userReview = Auth::check()
            ? Review::where('hotel_id', $hotel->id)->where('user_id', Auth::id())->first()
            : null;

        return view('hotels.show', compact('hotel', 'rooms', 'reservations', 'reviews', 'userReview'));
    }

    public function reservations(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['prestataire', 'employe'])) {
            $hotelIds = $user->getHotelIds();
            $reservations = Reservation::with(['hotel', 'client', 'room'])
                ->whereIn('hotel_id', $hotelIds)
                ->latest('created_at')
                ->paginate(12);
        } elseif ($user->role === 'client') {
            $client = Client::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            $baseQuery = Reservation::with(['hotel', 'client', 'room'])
                ->when($client, fn ($q) => $q->where('client_id', $client->id))
                ->when(!$client,  fn ($q) => $q->whereRaw('0=1'));
            $reservations = $baseQuery->latest('created_at')->paginate(12);
        } else {
            $reservations = Reservation::with(['hotel', 'client', 'room'])
                ->latest('created_at')
                ->paginate(12);
        }

        return view('reservations.index', compact('reservations'));
    }

    public function rooms(Request $request)
    {
        $user    = Auth::user();
        $hotelId = $request->query('hotel_id');

        if (in_array($user->role, ['prestataire', 'employe']) && ! $hotelId) {
            $hotelId = $user->getHotelIds()->first();
        }
        $hotel = $hotelId ? Hotel::find($hotelId) : Hotel::first();

        $rooms = Room::with('hotel')
            ->when($hotelId, fn ($query) => $query->where('hotel_id', $hotelId))
            ->latest()
            ->paginate(12);

        return view('rooms.index', compact('hotel', 'rooms', 'hotelId'));
    }

    public function storeRoom(Request $request)
    {
        $data = $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'number'      => 'required|string|max:20',
            'floor'       => 'required|integer|min:0',
            'type'        => 'required|string|max:50',
            'status'      => 'required|string|max:50',
            'price'       => 'required|integer|min:0',
            'capacity'    => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = CloudinaryUploader::upload($request->file('image'), 'rooms');
        }

        Room::create($data);

        return redirect()->route('rooms.index', ['hotel_id' => $data['hotel_id']])
            ->with('success', 'Chambre ajoutée.');
    }

    public function updateRoomStatus(Request $request, \App\Models\Room $room)
    {
        $room->update($request->validate(['status' => 'required|string|max:50']));

        return redirect()->route('rooms.index', ['hotel_id' => $room->hotel_id])
            ->with('success', 'Statut mis à jour.');
    }

    public function updateRoomImage(Request $request, \App\Models\Room $room)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        CloudinaryUploader::destroy($room->image);

        $room->update(['image' => CloudinaryUploader::upload($request->file('image'), 'rooms')]);

        return redirect()->route('rooms.index', ['hotel_id' => $room->hotel_id])
            ->with('success', 'Image de la chambre mise à jour.');
    }

    public function destroyRoom(\App\Models\Room $room)
    {
        $hotelId = $room->hotel_id;
        CloudinaryUploader::destroy($room->image);
        $room->delete();

        return redirect()->route('rooms.index', ['hotel_id' => $hotelId])
            ->with('success', 'Chambre supprimée.');
    }

    public function menu(Request $request)
    {
        $user      = Auth::user();
        $hotelIds  = $user->getHotelIds();
        $type      = $request->query('type');
        $menuItems = MenuItem::with('hotel')
            ->when($hotelIds->isNotEmpty(), fn ($q) => $q->whereIn('hotel_id', $hotelIds))
            ->when($type, fn ($q) => $q->where('item_type', $type))
            ->latest()->paginate(12);
        $hotels = Hotel::whereIn('id', $hotelIds)->get();

        return view('menu.index', compact('menuItems', 'type', 'hotels'));
    }

    public function storeMenuItem(Request $request)
    {
        MenuItem::create($request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'name'        => 'required|string|max:255',
            'item_type'   => 'required|string|max:50',
            'category'    => 'nullable|string|max:100',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'emoji'       => 'nullable|string|max:4',
            'description' => 'nullable|string|max:500',
            'available'   => 'boolean',
        ]));

        return redirect()->route('menu.index')->with('success', 'Article du menu ajouté.');
    }

    public function staff(Request $request)
    {
        $user     = Auth::user();
        $hotelIds = $user->getHotelIds();
        $staff    = Staff::with('hotel')
            ->when($hotelIds->isNotEmpty(), fn ($q) => $q->whereIn('hotel_id', $hotelIds))
            ->latest()->paginate(12);
        $hotels   = Hotel::whereIn('id', $hotelIds)->get();
        return view('staff.index', compact('staff', 'hotels'));
    }

    // ── Planning du personnel ─────────────────────────────────────────────────
    public function staffSchedule(Request $request)
    {
        $user     = Auth::user();
        $hotelIds = $user->getHotelIds();

        $week     = $request->integer('week', now()->weekOfYear);
        $year     = $request->integer('year', now()->year);
        $hotelId  = $request->integer('hotel_id', $hotelIds->first());

        $startOfWeek = now()->setISODate($year, $week)->startOfWeek();
        $endOfWeek   = $startOfWeek->copy()->endOfWeek();

        $staffList = Staff::whereIn('hotel_id', $hotelIds)
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->orderBy('last_name')->get();

        $schedules = \App\Models\StaffSchedule::with('staff')
            ->whereIn('hotel_id', $hotelIds)
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get()
            ->groupBy(fn ($s) => $s->staff_id . '_' . $s->date->format('Y-m-d'));

        $hotels  = Hotel::whereIn('id', $hotelIds)->get();
        $days    = collect(range(0, 6))->map(fn ($i) => $startOfWeek->copy()->addDays($i));

        return view('staff.schedule', compact(
            'staffList', 'schedules', 'days', 'hotels',
            'week', 'year', 'hotelId', 'startOfWeek'
        ));
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'staff_id'   => 'required|exists:staff,id',
            'hotel_id'   => 'required|exists:hotels,id',
            'date'       => 'required|date',
            'shift'      => 'required|in:matin,apres_midi,nuit,repos',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i',
            'poste'      => 'nullable|string|max:100',
            'notes'      => 'nullable|string|max:500',
        ]);

        \App\Models\StaffSchedule::updateOrCreate(
            ['staff_id' => $data['staff_id'], 'date' => $data['date']],
            $data
        );

        return back()->with('success', 'Planning mis à jour.');
    }

    public function storeStaff(Request $request)
    {
        $data = $request->validate([
            'hotel_id'      => 'required|exists:hotels,id',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'role'          => 'required|string|max:100',
            'phone'         => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:255',
            'hired_at'      => 'nullable|date',
            'status'        => 'required|string|max:50',
            'salary'        => 'required|integer|min:0',
            'photo'         => 'nullable|image|max:2048',
            'create_account'=> 'nullable|boolean',
            'account_email' => 'nullable|email|max:255|unique:users,email',
            'account_pass'  => 'nullable|string|min:6',
            'permissions'   => 'nullable|array',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = CloudinaryUploader::upload($request->file('photo'), 'staff');
        }

        // Créer le compte utilisateur si demandé
        if ($request->boolean('create_account') && $request->filled('account_email')) {
            $user = User::create([
                'name'        => $data['first_name'] . ' ' . $data['last_name'],
                'email'       => $request->account_email,
                'password'    => bcrypt($request->account_pass ?: 'password123'),
                'role'        => 'employe',
                'status'      => 'actif',
                'permissions' => $request->input('permissions', []),
            ]);
            $data['user_id'] = $user->id;
        }

        $staff = Staff::create(\Arr::except($data, ['create_account', 'account_email', 'account_pass', 'permissions']));

        // Lier le staff_id sur le user si créé
        if (isset($user)) {
            $user->update(['staff_id' => $staff->id]);
        }

        return redirect()->route('staff.index')->with('success', 'Personnel ajouté' . (isset($user) ? ' avec compte de connexion.' : '.'));
    }

    public function updateStaff(Request $request, \App\Models\Staff $staff)
    {
        $data = $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'role'        => 'required|string|max:100',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:255',
            'hired_at'    => 'nullable|date',
            'status'      => 'required|string|max:50',
            'salary'      => 'required|integer|min:0',
            'photo'       => 'nullable|image|max:2048',
            'permissions' => 'nullable|array',
        ]);

        if ($request->hasFile('photo')) {
            CloudinaryUploader::destroy($staff->photo);
            $data['photo'] = CloudinaryUploader::upload($request->file('photo'), 'staff');
        }

        $staff->update(\Arr::except($data, ['permissions', 'create_account', 'account_email', 'account_pass']));

        if ($staff->user_id && $staff->user) {
            // Mettre à jour les permissions du compte existant
            $staff->user->update([
                'permissions' => $request->input('permissions', []),
            ]);
        } elseif ($request->boolean('create_account') && $request->filled('account_email')) {
            // Créer un compte depuis la modale de modification
            $request->validate(['account_email' => 'email|max:255|unique:users,email']);
            $user = User::create([
                'name'        => $staff->first_name . ' ' . $staff->last_name,
                'email'       => $request->account_email,
                'password'    => bcrypt($request->account_pass ?: 'password123'),
                'role'        => 'employe',
                'status'      => 'actif',
                'permissions' => $request->input('permissions', []),
                'staff_id'    => $staff->id,
            ]);
            $staff->update(['user_id' => $user->id]);
        }

        return redirect()->route('staff.index')->with('success', 'Personnel mis à jour.');
    }

    public function destroyStaff(\App\Models\Staff $staff)
    {
        CloudinaryUploader::destroy($staff->photo);
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Personnel supprimé.');
    }

    public function expenses(Request $request)
    {
        $user     = Auth::user();
        $hotelIds = $user->getHotelIds();
        $expenses = Expense::with('hotel')
            ->when($hotelIds->isNotEmpty(), fn ($q) => $q->whereIn('hotel_id', $hotelIds))
            ->latest()->paginate(12);
        $hotels   = Hotel::whereIn('id', $hotelIds)->get();
        return view('expenses.index', compact('expenses', 'hotels'));
    }

    public function storeExpense(Request $request)
    {
        Expense::create($request->validate([
            'hotel_id'     => 'required|exists:hotels,id',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|integer|min:0',
            'expense_type' => 'nullable|string|max:100',
            'occurred_at'  => 'nullable|date',
            'notes'        => 'nullable|string|max:500',
        ]));

        return redirect()->route('expenses.index')->with('success', 'Charge enregistrée.');
    }

    public function pools(Request $request)
    {
        $user     = Auth::user();
        $hotelIds = $user->getHotelIds();
        $pools    = Pool::with('hotel')
            ->when($hotelIds->isNotEmpty(), fn ($q) => $q->whereIn('hotel_id', $hotelIds))
            ->latest()->paginate(12);
        $hotels   = Hotel::whereIn('id', $hotelIds)->get();
        return view('pools.index', compact('pools', 'hotels'));
    }

    public function storePool(Request $request)
    {
        Pool::create($request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'name'        => 'required|string|max:255',
            'status'      => 'required|string|max:50',
            'area'        => 'nullable|integer|min:0',
            'depth'       => 'nullable|numeric|min:0',
            'temperature' => 'nullable|numeric',
            'description' => 'nullable|string|max:500',
        ]));

        return redirect()->route('pools.index')->with('success', 'Piscine ajoutée.');
    }

    public function tickets(Request $request)
    {
        $user     = Auth::user();
        $hotelIds = $user->getHotelIds();

        $baseQ = Ticket::when($hotelIds->isNotEmpty(), fn ($q) => $q->whereIn('hotel_id', $hotelIds));

        $tickets     = (clone $baseQ)->with(['hotel', 'client'])->latest()->paginate(15);
        $statTotal   = (clone $baseQ)->count();
        $statOuvert  = (clone $baseQ)->where('status', 'ouvert')->count();
        $statEnCours = (clone $baseQ)->where('status', 'en cours')->count();
        $statFerme   = (clone $baseQ)->where('status', 'fermé')->count();
        $statUrgent  = (clone $baseQ)->where('priority', 'urgente')->count();

        $hotels  = Hotel::whereIn('id', $hotelIds)->get();
        $clients = Client::orderBy('first_name')->orderBy('last_name')->get();

        return view('tickets.index', compact(
            'tickets', 'hotels', 'clients',
            'statTotal', 'statOuvert', 'statEnCours', 'statFerme', 'statUrgent'
        ));
    }

    public function storeTicket(Request $request)
    {
        $data = $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'client_id'   => 'nullable|exists:clients,id',
            'title'       => 'required|string|max:255',
            'ticket_type' => 'required|string|max:100',
            'status'      => 'required|string|max:50',
            'priority'    => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'opened_at'   => 'nullable|date',
            'assigned_to' => 'nullable|string|max:100',
        ]);
        $data['opened_at'] = $data['opened_at'] ?? now();
        Ticket::create($data);
        return redirect()->route('tickets.index')->with('success', 'Ticket créé.');
    }

    public function updateTicket(Request $request, Ticket $ticket)
    {
        $ticket->update($request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'client_id'   => 'nullable|exists:clients,id',
            'title'       => 'required|string|max:255',
            'ticket_type' => 'required|string|max:100',
            'status'      => 'required|string|max:50',
            'priority'    => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'assigned_to' => 'nullable|string|max:100',
            'closed_at'   => 'nullable|date',
        ]));
        return redirect()->route('tickets.index')->with('success', 'Ticket mis à jour.');
    }

    public function destroyTicket(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket supprimé.');
    }

    public function orders(Request $request)
    {
        $user     = Auth::user();
        $hotelIds = $user->getHotelIds();

        $baseQ = Order::when($hotelIds->isNotEmpty(), fn ($q) => $q->whereIn('hotel_id', $hotelIds));

        $orders    = (clone $baseQ)->with(['hotel', 'client', 'menuItem'])->latest()->paginate(12);
        $statCA    = (clone $baseQ)->sum('total');
        $statTotal = (clone $baseQ)->count();
        $statPrep  = (clone $baseQ)->where('status', 'en préparation')->count();
        $statPret  = (clone $baseQ)->where('status', 'prêt')->count();
        $statLivre = (clone $baseQ)->where('status', 'livré')->count();
        $statAnnul = (clone $baseQ)->where('status', 'annulé')->count();

        $hotels    = Hotel::whereIn('id', $hotelIds)->get();
        $menuItems = MenuItem::whereIn('hotel_id', $hotelIds)->get();
        $clients   = Client::orderBy('first_name')->orderBy('last_name')->get();

        return view('orders.index', compact(
            'orders', 'hotels', 'menuItems', 'clients',
            'statCA', 'statTotal', 'statPrep', 'statPret', 'statLivre', 'statAnnul'
        ));
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'hotel_id'     => 'required|exists:hotels,id',
            'client_id'    => 'nullable|exists:clients,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity'     => 'required|integer|min:1',
            'status'       => 'required|string|max:100',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $menuItem      = MenuItem::find($data['menu_item_id']);
        $data['total'] = $menuItem ? $menuItem->price * $data['quantity'] : 0;

        Order::create($data);

        return redirect()->route('orders.index')->with('success', 'Commande enregistrée.');
    }

    public function updateOrder(Request $request, Order $order)
    {
        $data = $request->validate([
            'hotel_id'     => 'required|exists:hotels,id',
            'client_id'    => 'nullable|exists:clients,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity'     => 'required|integer|min:1',
            'status'       => 'required|string|max:100',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $menuItem      = MenuItem::find($data['menu_item_id']);
        $data['total'] = $menuItem ? $menuItem->price * $data['quantity'] : $order->total;

        $order->update($data);

        return redirect()->route('orders.index')->with('success', 'Commande mise à jour.');
    }

    public function destroyOrder(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Commande supprimée.');
    }

    public function clients(Request $request)
    {
        $user     = Auth::user();
        $hotelIds = $user->getHotelIds();
        // Clients liés aux réservations de ces hôtels
        $clientIds = $hotelIds->isNotEmpty()
            ? Reservation::whereIn('hotel_id', $hotelIds)->distinct()->pluck('client_id')
            : null;
        $clients = Client::when($clientIds, fn ($q) => $q->whereIn('id', $clientIds))
            ->latest()->paginate(12);
        return view('clients.index', compact('clients'));
    }

    public function boutique(Request $request)
    {
        $vue  = $request->query('vue', 'tous');
        $user = Auth::user();

        $query = Product::latest();
        if ($vue === 'mes') {
            $query->where('user_id', $user->id);
        }

        $products = $query->paginate(12)->withQueryString();
        return view('boutique.index', compact('products', 'vue'));
    }

    public function calendar()
    {
        $user     = Auth::user();
        $hotelIds = in_array($user->role, ['prestataire', 'employe'])
            ? $user->getHotelIds()
            : Hotel::pluck('id');

        $reservations = Reservation::whereIn('hotel_id', $hotelIds)
            ->where('checkout', '>=', now()->subDays(7))
            ->with(['hotel', 'client'])
            ->get();

        $events = $reservations->map(fn ($r) => [
            'date'  => $r->checkin->format('Y-m-d'),
            'end'   => $r->checkout->format('Y-m-d'),
            'title' => ($r->client->first_name ?? 'Client') . ' — ' . ($r->hotel->short_name ?? ''),
            'color' => match ($r->status) {
                'confirmée'  => '#009A44',
                'annulée'    => '#e63946',
                'en_attente' => '#F77F00',
                default      => '#5A67D8',
            },
        ])->toArray();

        return view('calendar.index', compact('events'));
    }

    public function reports(Request $request)
    {
        $period = $request->get('period', 'month'); // day | week | month | year | all

        $query = Reservation::query();

        $dateFrom = match($period) {
            'day'   => now()->startOfDay(),
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year'  => now()->startOfYear(),
            default => null,
        };

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        $totalRevenue      = (clone $query)->where('paid', true)->sum('total');
        $totalCommissions  = (clone $query)->where('paid', true)->sum('commission_amount');
        $totalReservations = (clone $query)->count();
        $confirmedCount    = (clone $query)->where('status', 'confirmée')->count();
        $cancelledCount    = (clone $query)->where('status', 'annulée')->count();

        // Statistiques par nationalité
        $byNationality = (clone $query)
            ->select('nationality', DB::raw('count(*) as total'), DB::raw('sum(total) as revenue'))
            ->whereNotNull('nationality')
            ->groupBy('nationality')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Statistiques par type de chambre (via room field)
        $byRoomType = Room::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        $hotels    = Hotel::orderByDesc('revenue')->take(6)->get();
        $avgRating = Hotel::avg('rating');

        // Export CSV demandé ?
        if ($request->get('export') === 'excel') {
            return $this->exportReservationsExcel($period, $dateFrom);
        }

        return view('reports.index', compact(
            'hotels', 'totalRevenue', 'totalCommissions', 'totalReservations', 'avgRating',
            'confirmedCount', 'cancelledCount', 'byNationality', 'byRoomType', 'period'
        ));
    }

    private function exportReservationsExcel(string $period, ?\Carbon\Carbon $dateFrom)
    {
        $query = Reservation::with(['hotel', 'client']);
        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        $reservations = $query->latest()->get();

        $filename = 'rapport_reservations_' . $period . '_' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($reservations) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Code', 'Client', 'Nationalité', 'Hôtel', 'Chambre', 'Arrivée', 'Départ', 'Nuits', 'Total (FCFA)', 'Statut', 'Payé', 'Canal', 'Mode paiement', 'Date création'], ';');

            foreach ($reservations as $r) {
                fputcsv($handle, [
                    $r->code,
                    optional($r->client)->first_name . ' ' . optional($r->client)->last_name,
                    $r->nationality ?? '',
                    optional($r->hotel)->name ?? '',
                    $r->room,
                    $r->checkin?->format('d/m/Y') ?? '',
                    $r->checkout?->format('d/m/Y') ?? '',
                    $r->nights,
                    $r->total,
                    $r->status,
                    $r->paid ? 'Oui' : 'Non',
                    $r->channel ?? '',
                    $r->payment_method ?? '',
                    $r->created_at?->format('d/m/Y H:i') ?? '',
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function settings()
    {
        return view('settings.index');
    }

    public function subscriptions(Request $request)
    {
        $query = \App\Models\Subscription::with(['user', 'plan']);
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $subscriptions = $query->latest()->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function payouts()
    {
        $prestataires = \App\Models\User::where('role', 'prestataire')
            ->get()
            ->map(function ($user) {
                $user->owed         = $user->totalOwed();
                $user->alreadyPaid  = (int) $user->payouts()->sum('amount');
                $user->balance      = $user->owed - $user->alreadyPaid;
                $user->lastPayout   = $user->payouts()->latest('paid_at')->first();
                return $user;
            })
            ->sortByDesc('balance')
            ->values();

        $failedPayouts = Reservation::with(['hotel', 'client'])
            ->where('payout_status', 'error')
            ->latest()
            ->get();

        return view('admin.payouts.index', compact('prestataires', 'failedPayouts'));
    }

    /** Relance un virement automatique Jèko qui a échoué (contact invalide, solde insuffisant...). */
    public function retryPayout(Reservation $reservation)
    {
        $reservation->update(['payout_transfer_id' => null, 'payout_status' => null, 'payout_failure_reason' => null]);
        $this->triggerOwnerPayout($reservation);

        $reservation->refresh();
        if ($reservation->payout_status === 'error') {
            return back()->with('error', 'Le virement a de nouveau échoué : ' . ($reservation->payout_failure_reason ?? 'raison inconnue'));
        }

        return back()->with('success', 'Virement relancé.');
    }

    public function recordPayout(Request $request, \App\Models\User $user)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'notes'  => 'nullable|string|max:255',
        ]);

        if ($data['amount'] > $user->payoutBalance()) {
            return back()->with('error', 'Le montant dépasse le solde dû (' . number_format($user->payoutBalance(), 0, ',', ' ') . ' FCFA).');
        }

        \App\Models\Payout::create([
            'user_id'     => $user->id,
            'amount'      => $data['amount'],
            'notes'       => $data['notes'] ?? null,
            'paid_at'     => now(),
            'recorded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Versement de ' . number_format($data['amount'], 0, ',', ' ') . ' FCFA enregistré pour ' . $user->name . '.');
    }

    public function recordSubscriptionPayment(Request $request, \App\Models\Subscription $subscription)
    {
        $amount = $request->integer('amount') ?: $subscription->plan->price_monthly;
        $this->extendSubscriptionPeriod($subscription, $amount, $request->get('payment_method'), Auth::id());

        return back()->with('success', 'Paiement enregistré, abonnement actif jusqu\'au ' . $subscription->fresh()->current_period_end->format('d/m/Y') . '.');
    }

    /** Prolonge l'abonnement d'un mois et journalise le paiement — manuel (admin) ou automatique (webhook Jèko). */
    private function extendSubscriptionPeriod(\App\Models\Subscription $subscription, int $amount, ?string $paymentMethod, ?int $recordedBy): void
    {
        $periodStart = ($subscription->current_period_end && $subscription->current_period_end->isFuture())
            ? $subscription->current_period_end
            : now()->toDateString();
        $periodEnd = \Carbon\Carbon::parse($periodStart)->addMonth();

        \App\Models\SubscriptionPayment::create([
            'subscription_id' => $subscription->id,
            'amount'          => $amount,
            'period_start'    => $periodStart,
            'period_end'      => $periodEnd,
            'payment_method'  => $paymentMethod,
            'recorded_by'     => $recordedBy,
            'paid_at'         => now(),
        ]);

        $subscription->update([
            'status'              => 'active',
            'started_at'          => $subscription->started_at ?? $periodStart,
            'current_period_end'  => $periodEnd,
        ]);
    }

    /** Active l'abonnement automatiquement après confirmation du paiement Jèko (webhook). */
    public function activateSubscriptionFromPayment(\App\Models\Subscription $subscription): void
    {
        $this->extendSubscriptionPeriod($subscription, $subscription->plan->price_monthly, 'jeko', null);
    }

    /**
     * Le prestataire paie lui-même son abonnement via Jèko (redirection, comme pour une
     * réservation). L'activation se fait automatiquement au retour du webhook — voir
     * activateSubscriptionFromPayment() et JekoWebhookController.
     */

    /**
     * Permet à un prestataire sans abonnement (compte créé avant ce système, ou validé par
     * un admin sans choix de palier) de choisir un palier a posteriori.
     */
    public function chooseSubscriptionPlan(Request $request)
    {
        if (Auth::user()->currentSubscription()) {
            return back()->with('error', 'Vous avez déjà un abonnement.');
        }

        $data = $request->validate(['plan' => 'required|string|in:starter,business,premium']);
        $plan = \App\Models\SubscriptionPlan::where('slug', $data['plan'])->firstOrFail();

        \App\Models\Subscription::create([
            'user_id' => Auth::id(),
            'subscription_plan_id' => $plan->id,
            'status' => 'pending_payment',
        ]);

        return back()->with('success', 'Palier ' . $plan->name . ' sélectionné — réglez le paiement pour activer votre abonnement.');
    }

    public function paySubscription(Request $request)
    {
        $subscription = Auth::user()->currentSubscription();
        if (! $subscription) {
            return back()->with('error', "Aucun abonnement trouvé sur votre compte.");
        }

        $data = $request->validate(['payment_method' => 'required|string|max:80']);
        $jekoMethod = JekoClient::mapPaymentMethod($data['payment_method']);
        if (! $jekoMethod) {
            return back()->with('error', 'Moyen de paiement non pris en charge pour le paiement en ligne.');
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
                    'successUrl'    => route('subscription.payment.callback', ['result' => 'success']),
                    'errorUrl'      => route('subscription.payment.callback', ['result' => 'error']),
                ],
            ],
        ]);

        if (empty($result['redirectUrl'])) {
            return back()->with('error', $result['message'] ?? 'Impossible de lancer le paiement pour le moment. Réessayez ou choisissez un autre moyen de paiement.');
        }

        $subscription->update([
            'jeko_reference'          => $reference,
            'jeko_payment_request_id' => $result['id'] ?? null,
        ]);

        return redirect()->away($result['redirectUrl']);
    }

    /** Retour du prestataire depuis la page de paiement Jèko pour son abonnement. */
    public function subscriptionPaymentCallback(Request $request)
    {
        $subscription = Auth::user()->currentSubscription();

        if ($subscription && $subscription->status !== 'active' && $subscription->jeko_payment_request_id) {
            $result = JekoClient::getPaymentRequest($subscription->jeko_payment_request_id);
            if (($result['status'] ?? null) === 'success') {
                $this->activateSubscriptionFromPayment($subscription);
            }
        }

        if ($subscription && $subscription->status === 'active') {
            return redirect()->route('prestataire.index')->with('success', 'Paiement confirmé, votre abonnement est actif.');
        }

        return redirect()->route('prestataire.index')->with('error', $request->query('result') === 'error'
            ? 'Le paiement a échoué ou a été annulé. Réessayez.'
            : 'Paiement en cours de confirmation. Si le statut ne change pas sous peu, contactez le support.');
    }

    /**
     * Enregistre (ou remplace) les coordonnées de paiement du prestataire pour les
     * reversements automatiques (mobile money ou RIB bancaire), et crée le contact
     * bénéficiaire correspondant chez Jèko.
     */
    public function updatePayoutInfo(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'payout_method' => 'required|string|in:wave,orange_money,mtn,moov,djamo,bank',
            'phone_number'  => 'required_unless:payout_method,bank|nullable|string|max:20',
            'bank_name'     => 'required_if:payout_method,bank|nullable|string|max:100',
            'bank_code'     => 'required_if:payout_method,bank|nullable|string|max:20',
            'swift_code'    => 'required_if:payout_method,bank|nullable|string|max:20',
            'agency_code'   => 'required_if:payout_method,bank|nullable|string|max:20',
            'account_number'=> 'required_if:payout_method,bank|nullable|string|max:40',
            'rib_key'       => 'required_if:payout_method,bank|nullable|string|max:10',
        ]);

        $isBank = $data['payout_method'] === 'bank';
        $identifier = $isBank
            ? [
                'bankName'      => $data['bank_name'],
                'bankCode'      => $data['bank_code'],
                'swiftCode'     => $data['swift_code'],
                'agencyCode'    => $data['agency_code'],
                'accountNumber' => $data['account_number'],
                'key'           => $data['rib_key'],
            ]
            : ['number' => $data['phone_number']];

        $result = JekoClient::createContact([
            'name'          => $user->name,
            'paymentMethod' => $data['payout_method'],
            'identifier'    => $identifier,
        ]);

        if (empty($result['id'])) {
            return back()->with('error', $result['message'] ?? "Impossible d'enregistrer ces coordonnées de paiement pour le moment.");
        }

        $oldContactId = $user->jeko_contact_id;

        $user->update([
            'payout_method'     => $data['payout_method'],
            'payout_identifier' => $identifier,
            'jeko_contact_id'   => $result['id'],
        ]);

        if ($oldContactId) {
            try { JekoClient::deleteContact($oldContactId); } catch (\Throwable) {}
        }

        return back()->with('success', 'Coordonnées de paiement enregistrées.');
    }

    public function profile()
    {
        $user   = Auth::user();
        $client = Client::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        $myHotels = $user->role === 'prestataire'
            ? Hotel::where('user_id', $user->id)->get()
            : collect();

        return view('profile.index', compact('user', 'client', 'myHotels'));
    }

    public function storeHotel(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'short_name'  => 'required|string|max:50',
            'city'        => 'required|string|max:100',
            'quartier'    => 'required|string|max:100',
            'stars'       => 'required|integer|min:1|max:5',
            'rooms'       => 'required|integer|min:1',
            'category'    => 'required|string|max:100',
            'status'      => 'required|string|max:50',
            'rating'      => 'required|numeric|min:0|max:5',
            'price'       => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'services'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        if (! empty($data['services'])) {
            $data['services'] = array_map('trim', explode(',', $data['services']));
        }

        if ($request->hasFile('image')) {
            $data['image'] = CloudinaryUploader::upload($request->file('image'), 'hotels');
        }

        $subscription = Auth::user()->currentSubscription();
        if ($subscription && $subscription->plan && $subscription->plan->max_hotels !== null) {
            $currentCount = Hotel::where('user_id', Auth::id())->count();
            if ($currentCount >= $subscription->plan->max_hotels) {
                return back()->withInput()->with('error', "Quota d'hôtels atteint pour votre palier d'abonnement ({$subscription->plan->name}) — passez à un palier supérieur.");
            }
        }

        $data['user_id'] = Auth::id();

        $hotel = Hotel::create($data);

        return redirect()->route('rooms.index', ['hotel_id' => $hotel->id])
            ->with('success', 'Hôtel créé ! Ajoutez maintenant les chambres.');
    }

    public function editHotel(Hotel $hotel)
    {
        return view('hotels.edit', compact('hotel'));
    }

    public function updateHotel(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'short_name'  => 'required|string|max:50',
            'city'        => 'required|string|max:100',
            'quartier'    => 'required|string|max:100',
            'stars'       => 'required|integer|min:1|max:5',
            'rooms'       => 'required|integer|min:1',
            'category'    => 'required|string|max:100',
            'status'      => 'required|string|max:50',
            'rating'      => 'required|numeric|min:0|max:5',
            'price'       => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'services'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        if (! empty($data['services'])) {
            $data['services'] = array_map('trim', explode(',', $data['services']));
        }

        if ($request->hasFile('image')) {
            CloudinaryUploader::destroy($hotel->image);
            $data['image'] = CloudinaryUploader::upload($request->file('image'), 'hotels');
        }

        $hotel->update($data);

        return redirect()->route('hotels.show', $hotel)->with('success', 'Hôtel mis à jour avec succès.');
    }

    public function destroyHotel(Hotel $hotel)
    {
        CloudinaryUploader::destroy($hotel->image);
        $hotel->delete();

        return redirect()->route('hotels.index')->with('success', 'Hôtel supprimé avec succès.');
    }

    public function storeReservation(Request $request)
    {
        $user     = Auth::user();
        $isClient = $user->role === 'client';

        $data = $request->validate([
            'client_id'      => $isClient ? 'nullable' : 'required|exists:clients,id',
            'hotel_id'       => 'required|exists:hotels,id',
            'room_id'        => 'nullable|exists:rooms,id',
            'room'           => 'required_without:room_id|string|max:30',
            'checkin'        => 'required|date|after_or_equal:today',
            'checkout'       => 'required|date|after:checkin',
            'price'          => 'nullable|integer|min:0',
            'channel'        => 'required|string|max:60',
            'payment_method' => 'required|string|max:80',
            'nationality'    => 'required|string|max:120',
        ]);

        if ($isClient) {
            $client = Client::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if (! $client) {
                $client = Client::create([
                    'user_id'     => $user->id,
                    'code'        => 'CLI' . $user->id . time(),
                    'first_name'  => explode(' ', $user->name)[0] ?? $user->name,
                    'last_name'   => explode(' ', $user->name)[1] ?? '',
                    'email'       => $user->email,
                    'phone'       => '',
                    'city'        => '',
                    'quartier'    => '',
                    'nationality' => "Côte d'Ivoire",
                ]);
            }
            $data['client_id'] = $client->id;
        }

        $hotel = Hotel::findOrFail($data['hotel_id']);
        if (! empty($data['room_id'])) {
            $room = Room::find($data['room_id']);
            if ($room) {
                $data['room']  = $room->number;
                $data['price'] = $room->price;
            }
        }
        unset($data['room_id']);

        $data['price'] = $data['price'] ?? $hotel->price;
        $roomNumber    = $data['room'];

        $checkin  = Carbon::parse($data['checkin']);
        $checkout = Carbon::parse($data['checkout']);
        $data['nights'] = max(1, $checkin->diffInDays($checkout));
        $data['total']  = $data['price'] * $data['nights'];
        $data['code']   = 'RES' . Carbon::now()->format('YmdHis');
        $data['status'] = 'en_attente';
        $data['paid']   = false;

        if ($this->isRoomBooked($hotel->id, $roomNumber, $checkin, $checkout)) {
            return back()->withInput()->with('error', 'La chambre n\'est pas disponible aux dates sélectionnées.');
        }

        $reservation = Reservation::create($data);

        // Passer la chambre en "réservée" (cycle mémoire : Libre → Réservée)
        Room::where('hotel_id', $hotel->id)
            ->where('number', $roomNumber)
            ->where('status', Room::STATUS_LIBRE)
            ->update(['status' => Room::STATUS_RESERVEE]);

        // Notifier le prestataire de la nouvelle réservation
        $hotelOwner = $reservation->hotel->user;
        if ($hotelOwner) {
            try { $hotelOwner->notify(new ReservationCreatedNotification($reservation)); } catch (\Throwable) {}
        }

        return $this->initiatePayment($reservation, $data['payment_method']);
    }

    // ── Check-in : Réservée → Occupée ────────────────────────────────────────
    public function checkIn(Reservation $reservation)
    {
        if ($reservation->checkin_at) {
            return back()->with('error', 'Le check-in a déjà été effectué.');
        }

        $reservation->update([
            'checkin_at' => now(),
            'status'     => 'arrivée',
        ]);

        // Chambre : Réservée → Occupée
        Room::where('hotel_id', $reservation->hotel_id)
            ->where('number', $reservation->room)
            ->update(['status' => Room::STATUS_OCCUPEE]);

        return back()->with('success', 'Check-in effectué. Chambre marquée Occupée.');
    }

    // ── Check-out : Occupée → En nettoyage + fidélité ────────────────────────
    public function checkOut(Reservation $reservation)
    {
        if ($reservation->checkout_at) {
            return back()->with('error', 'Le check-out a déjà été effectué.');
        }

        $reservation->update([
            'checkout_at' => now(),
            'status'      => 'départie',
        ]);

        // Chambre : Occupée → En nettoyage
        Room::where('hotel_id', $reservation->hotel_id)
            ->where('number', $reservation->room)
            ->update(['status' => Room::STATUS_NETTOYAGE]);

        // Mise à jour stats client + points fidélité (10 pts par nuit)
        $client = $reservation->client;
        if ($client) {
            $pointsGagnes = $reservation->nights * 10;
            $client->increment('total_stays');
            $client->increment('total_spend', $reservation->total);
            $client->increment('points_fidelite', $pointsGagnes);
            $client->update(['last_visit' => now()]);
        }

        return back()->with('success', 'Check-out effectué. Chambre en nettoyage. Points fidélité crédités.');
    }

    // ── Fin nettoyage : En nettoyage → Libre ────────────────────────────────
    public function roomCleaned(Room $room)
    {
        if ($room->status !== Room::STATUS_NETTOYAGE) {
            return back()->with('error', 'Cette chambre n\'est pas en cours de nettoyage.');
        }

        $room->update(['status' => Room::STATUS_LIBRE]);

        return back()->with('success', 'Chambre marquée disponible.');
    }

    public function payment(Reservation $reservation)
    {
        if ($reservation->paid) {
            return redirect()->route('reservations.index')->with('info', 'Cette réservation a déjà été payée.');
        }

        return view('reservations.payment', compact('reservation'));
    }

    public function processPayment(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'payment_method' => 'required|string|max:80',
        ]);

        return $this->initiatePayment($reservation, $data['payment_method']);
    }

    /**
     * Lance le paiement d'une réservation : confirmation immédiate pour Espèces/Virement,
     * sinon redirection vers la page sécurisée Jèko. Appelé juste après la création d'une
     * réservation (pas d'étape de confirmation intermédiaire) et depuis la page de paiement
     * (reprise d'une réservation non payée, ex. depuis la liste des réservations).
     */
    private function initiatePayment(Reservation $reservation, string $paymentMethod)
    {
        if ($reservation->paid) {
            return redirect()->route('reservations.success', $reservation);
        }

        $reservation->update(['payment_method' => $paymentMethod]);

        $jekoMethod = JekoClient::mapPaymentMethod($paymentMethod);

        // Espèces / Virement : aucune passerelle ne les gère, confirmation immédiate (comportement historique)
        if (! $jekoMethod) {
            $this->markReservationPaid($reservation);

            return redirect()->route('reservations.success', $reservation)
                ->with('success', 'Paiement confirmé. Un email de confirmation vous a été envoyé.');
        }

        $reference = 'HOTELIO-RES-' . $reservation->id . '-' . now()->timestamp;

        $result = JekoClient::createPaymentRequest([
            'storeId'     => config('services.jeko.store_id'),
            'amountCents' => (int) round($reservation->total * 100),
            'currency'    => 'XOF',
            'reference'   => $reference,
            'paymentDetails' => [
                'type' => 'redirect',
                'data' => [
                    'paymentMethod' => $jekoMethod,
                    'successUrl'    => route('reservations.payment.callback', ['reservation' => $reservation, 'result' => 'success']),
                    'errorUrl'      => route('reservations.payment.callback', ['reservation' => $reservation, 'result' => 'error']),
                ],
            ],
        ]);

        if (empty($result['redirectUrl'])) {
            return redirect()->route('reservations.payment', $reservation)
                ->with('error', $result['message'] ?? 'Impossible de lancer le paiement pour le moment. Réessayez ou choisissez un autre moyen de paiement.');
        }

        $reservation->update([
            'jeko_reference'          => $reference,
            'jeko_payment_request_id' => $result['id'] ?? null,
        ]);

        return redirect()->away($result['redirectUrl']);
    }

    /**
     * Retour du client depuis la page de paiement Jèko (succès ou échec).
     * La redirection seule ne prouve rien : on interroge l'API pour un retour immédiat,
     * la confirmation définitive arrive via le webhook (voir JekoWebhookController).
     */
    public function paymentCallback(Request $request, Reservation $reservation)
    {
        if (! $reservation->paid && $reservation->jeko_payment_request_id) {
            $result = JekoClient::getPaymentRequest($reservation->jeko_payment_request_id);
            if (($result['status'] ?? null) === 'success') {
                $this->markReservationPaid($reservation);
            }
        }

        if ($reservation->paid) {
            return redirect()->route('reservations.success', $reservation)
                ->with('success', 'Paiement confirmé. Un email de confirmation vous a été envoyé.');
        }

        return redirect()->route('reservations.payment', $reservation)
            ->with('error', $request->query('result') === 'error'
                ? 'Le paiement a échoué ou a été annulé. Réessayez.'
                : 'Paiement en cours de confirmation. Si le statut ne change pas sous peu, contactez le support.');
    }

    /** Channels considérés comme "apportés par Hôtelio" (commissionnables) */
    private const COMMISSIONABLE_CHANNELS = ['Site web', 'App mobile'];
    private const DEFAULT_COMMISSION_RATE = 10.00;

    /** Calcule et enregistre la commission Hôtelio sur une réservation au moment du paiement */
    private function applyCommission(Reservation $reservation): void
    {
        if (! in_array($reservation->channel, self::COMMISSIONABLE_CHANNELS, true)) {
            return;
        }

        $owner = $reservation->hotel?->user;
        $rate = self::DEFAULT_COMMISSION_RATE;
        if ($owner) {
            $subscription = $owner->subscriptions()->where('status', 'active')->latest()->first();
            if ($subscription && $subscription->plan) {
                $rate = (float) $subscription->plan->commission_rate;
            }
        }

        $reservation->update([
            'commission_rate'   => $rate,
            'commission_amount' => (int) round($reservation->total * $rate / 100),
        ]);
    }

    /**
     * Confirme le paiement d'une réservation (appelé par le paiement manuel Espèces/Virement,
     * le retour client, et le webhook Jèko). Idempotent.
     */
    public function markReservationPaid(Reservation $reservation): void
    {
        if ($reservation->paid) {
            return;
        }

        $this->applyCommission($reservation);

        $reservation->update(['paid' => true, 'status' => 'confirmée']);

        // Mettre à jour les stats du client
        $client = $reservation->client;
        if ($client) {
            $client->increment('total_stays');
            $client->increment('total_spend', $reservation->total);
            $client->update(['last_visit' => today()]);
        }

        // Mettre à jour les stats de l'hôtel
        $reservation->hotel->increment('revenue', $reservation->total);
        $reservation->hotel->increment('checkins');

        // Email de confirmation (avec facture PDF en pièce jointe)
        $recipientUser = $client?->user;
        $recipientEmail = $recipientUser?->email ?? $client?->email;
        if ($recipientEmail) {
            try { Mail::to($recipientEmail)->send(new ReservationConfirmed($reservation)); } catch (\Throwable) {}
        }

        // Notification in-app au client (si un compte est lié)
        if ($recipientUser) {
            try { $recipientUser->notify(new ReservationConfirmedNotification($reservation)); } catch (\Throwable) {}
        }
    }

    /**
     * Reverse automatiquement la part du propriétaire (total − commission) vers son contact
     * Jèko (mobile money / banque), uniquement quand l'argent est réellement passé par le
     * compte Jèko de la plateforme (réservation commissionnable). Appelé depuis le webhook
     * Jèko après confirmation du paiement — jamais depuis le paiement Espèces/Virement
     * (l'hôtel a déjà l'argent en main dans ce cas, rien à reverser).
     */
    public function triggerOwnerPayout(Reservation $reservation): void
    {
        if ($reservation->payout_transfer_id) {
            return; // déjà déclenché (idempotence)
        }
        if (! in_array($reservation->channel, self::COMMISSIONABLE_CHANNELS, true)) {
            return;
        }

        $owner = $reservation->hotel?->user;
        if (! $owner || ! $owner->hasPayoutContact()) {
            return; // propriétaire sans coordonnées de paiement enregistrées
        }

        $netAmount = $reservation->total - ($reservation->commission_amount ?? 0);
        if ($netAmount < 5) {
            return; // sous le minimum Jèko (500 centimes = 5 XOF)
        }

        $reference = 'PAYOUT-RES-' . $reservation->id;

        $result = JekoClient::createTransfer([
            'storeId'     => config('services.jeko.store_id'),
            'contactId'   => $owner->jeko_contact_id,
            'amountCents' => $netAmount * 100,
            'currency'    => 'XOF',
            'description' => 'Reversement réservation ' . $reservation->code,
            'reference'   => $reference,
        ]);

        if (isset($result['status'])) {
            $reservation->update([
                'payout_transfer_id' => $result['id'] ?? null,
                'payout_status'      => $result['status'],
            ]);
        } else {
            Log::error('Échec du virement de reversement Jèko', ['reservation_id' => $reservation->id, 'result' => $result]);
            $reservation->update([
                'payout_status'         => 'error',
                'payout_failure_reason' => $result['message'] ?? ($result['id'] ?? 'Erreur inconnue'),
            ]);
        }
    }

    public function invoice(Reservation $reservation)
    {
        $pdf = Pdf::loadView('reservations.invoice', compact('reservation'));
        return $pdf->download('facture_' . $reservation->code . '.pdf');
    }

    public function success(Reservation $reservation)
    {
        return view('reservations.success', compact('reservation'));
    }

    public function updateReservationStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|string|in:confirmée,annulée,en_attente,arrivée,départie',
        ]);

        if ($request->status === 'annulée' && $reservation->status !== 'annulée' && $reservation->paid) {
            $this->reverseCommissionAndStats($reservation);
        }

        $reservation->update(['status' => $request->status]);

        // Notifier le client si annulation
        if ($request->status === 'annulée') {
            $clientUser = User::where('email', optional($reservation->client)->email)->first();
            if ($clientUser) {
                try { $clientUser->notify(new ReservationCancelledNotification($reservation)); } catch (\Throwable) {}
            }
        }

        return redirect()->back()->with('success', 'Statut de la réservation mis à jour.');
    }

    /**
     * Annule les effets comptables de markReservationPaid() quand une réservation payée est
     * annulée : la commission déjà calculée est remise à zéro et les statistiques (hôtel, client)
     * sont décrémentées. Le paiement lui-même (`paid`) n'est pas touché — un éventuel remboursement
     * reste géré manuellement par l'hôtel.
     */
    private function reverseCommissionAndStats(Reservation $reservation): void
    {
        $reservation->hotel?->decrement('revenue', min($reservation->total, $reservation->hotel->revenue));
        if ($reservation->hotel && $reservation->hotel->checkins > 0) {
            $reservation->hotel->decrement('checkins');
        }

        $client = $reservation->client;
        if ($client) {
            $client->decrement('total_spend', min($reservation->total, $client->total_spend));
            if ($client->total_stays > 0) {
                $client->decrement('total_stays');
            }
        }

        $reservation->update(['commission_rate' => null, 'commission_amount' => null]);
    }

    private function isRoomBooked(int $hotelId, string $roomNumber, Carbon $checkin, Carbon $checkout): bool
    {
        return Reservation::where('hotel_id', $hotelId)
            ->where('room', $roomNumber)
            ->whereNotIn('status', ['annulée'])
            ->where(function ($query) use ($checkin, $checkout) {
                $query->where('checkin', '<', $checkout)
                      ->where('checkout', '>', $checkin);
            })
            ->exists();
    }

    public function storeClient(Request $request)
    {
        Client::create($request->validate([
            'code'        => 'required|string|max:20|unique:clients,code',
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|max:30',
            'city'        => 'required|string|max:100',
            'quartier'    => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
        ]));

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'required|string|max:100',
            'emoji'       => 'nullable|string|max:4',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = CloudinaryUploader::upload($request->file('image'), 'products');
        }

        $data['user_id'] = Auth::id();

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produit ajouté.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Product $product, Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $product->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'required|string|max:100',
            'emoji'       => 'nullable|string|max:4',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            CloudinaryUploader::destroy($product->image);
            $data['image'] = CloudinaryUploader::upload($request->file('image'), 'products');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $product->user_id !== $user->id) {
            abort(403);
        }

        CloudinaryUploader::destroy($product->image);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé.');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role'  => 'required|string|max:80',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt('password123'),
            'role'     => $data['role'],
            'status'   => 'actif',
            'hotels'   => 'Toutes les propriétés',
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur ajouté.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'   => 'required|in:admin,prestataire,client',
            'status' => 'required|in:actif,inactif,suspendu,en_attente',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }
}
