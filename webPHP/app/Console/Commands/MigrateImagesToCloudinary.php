<?php

namespace App\Console\Commands;

use App\Models\Hotel;
use App\Models\Product;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToCloudinary extends Command
{
    protected $signature = 'images:migrate-to-cloudinary';

    protected $description = "Upload vers Cloudinary les images encore stockées localement (hôtels, chambres, personnel, produits, justificatifs) et met à jour les chemins en base.";

    public function handle(): int
    {
        $this->migrateModelField(Hotel::query(), 'image', 'hotels');
        $this->migrateModelField(Room::query(), 'image', 'rooms');
        $this->migrateModelField(Staff::query(), 'photo', 'staff');
        $this->migrateModelField(Product::query(), 'image', 'products');
        $this->migrateProofDocuments();

        $this->info('Migration terminée.');

        return self::SUCCESS;
    }

    private function migrateModelField(Builder $query, string $field, string $folder): void
    {
        $models = $query->whereNotNull($field)->where($field, 'not like', 'http%')->get();

        if ($models->isEmpty()) {
            $this->line("Aucune image locale à migrer pour {$folder}.");
            return;
        }

        $this->info("{$models->count()} image(s) à migrer pour {$folder}...");

        foreach ($models as $model) {
            $localPath = $model->{$field};

            if (! Storage::disk('public')->exists($localPath)) {
                $this->warn("  Fichier introuvable, ignoré : {$localPath}");
                continue;
            }

            try {
                $result = cloudinary()->uploadApi()->upload(
                    Storage::disk('public')->path($localPath),
                    ['folder' => "hotelio/{$folder}"]
                );
                $model->update([$field => $result['secure_url']]);
                $this->line("  OK {$localPath} -> {$result['secure_url']}");
            } catch (\Throwable $e) {
                $this->error("  ECHEC pour {$localPath} : {$e->getMessage()}");
            }
        }
    }

    private function migrateProofDocuments(): void
    {
        $users = User::whereNotNull('permissions')->get()->filter(function (User $user) {
            $proof = $user->permissions['proof_document'] ?? null;
            return $proof && ! str_starts_with($proof, 'http');
        });

        if ($users->isEmpty()) {
            $this->line('Aucun justificatif local à migrer.');
            return;
        }

        $this->info("{$users->count()} justificatif(s) à migrer...");

        foreach ($users as $user) {
            $localPath = $user->permissions['proof_document'];

            if (! Storage::disk('public')->exists($localPath)) {
                $this->warn("  Fichier introuvable, ignoré : {$localPath}");
                continue;
            }

            try {
                $result = cloudinary()->uploadApi()->upload(
                    Storage::disk('public')->path($localPath),
                    ['folder' => 'hotelio/proofs', 'resource_type' => 'auto']
                );
                $permissions = $user->permissions;
                $permissions['proof_document'] = $result['secure_url'];
                $user->forceFill(['permissions' => $permissions])->save();
                $this->line("  OK {$localPath} -> {$result['secure_url']}");
            } catch (\Throwable $e) {
                $this->error("  ECHEC pour {$localPath} : {$e->getMessage()}");
            }
        }
    }
}
