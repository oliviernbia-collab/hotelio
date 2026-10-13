<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Page complète des notifications */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /** JSON pour le dropdown topbar */
    public function fetch()
    {
        $user  = Auth::user();
        $items = $user->notifications()->latest()->take(10)->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'data'       => $n->data,
                'read'       => !is_null($n->read_at),
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $items,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    /** Marquer une notification comme lue */
    public function markRead(string $id)
    {
        $notif = Auth::user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        if ($notif->data['url'] ?? null) {
            return redirect($notif->data['url']);
        }

        return back();
    }

    /** Tout marquer comme lu */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /** Supprimer une notification */
    public function destroy(string $id)
    {
        Auth::user()->notifications()->findOrFail($id)->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Notification supprimée.');
    }

    /** Tout supprimer */
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'Toutes les notifications ont été supprimées.');
    }
}
