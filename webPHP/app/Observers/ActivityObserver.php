<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    private static array $labels = [
        'Hotel'       => 'hôtel',
        'Room'        => 'chambre',
        'Reservation' => 'réservation',
        'Staff'       => 'membre du personnel',
        'Client'      => 'client',
        'Expense'     => 'charge',
        'Product'     => 'produit boutique',
        'Commande'    => 'commande boutique',
        'User'        => 'utilisateur',
        'Review'      => 'avis',
        'Ticket'      => 'ticket',
    ];

    private function label(Model $model): string
    {
        $type = class_basename($model);
        $name = self::$labels[$type] ?? strtolower($type);
        $title = $model->name ?? $model->first_name ?? $model->code ?? "#$model->id";
        return "$name « $title »";
    }

    public function created(Model $model): void
    {
        ActivityLog::record('created', 'Création d\'un ' . $this->label($model), $model);
    }

    public function updated(Model $model): void
    {
        ActivityLog::record('updated', 'Modification d\'un ' . $this->label($model), $model);
    }

    public function deleted(Model $model): void
    {
        ActivityLog::record('deleted', 'Suppression d\'un ' . $this->label($model), $model);
    }
}
