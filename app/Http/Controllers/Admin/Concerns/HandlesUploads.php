<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\Images;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Photos et pièces jointes des formulaires de l'admin : remplacer, retirer
 * (case « remove_image »), et effacer l'ancien fichier du disque.
 */
trait HandlesUploads
{
    /** Règles d'une photo envoyée depuis l'admin */
    protected function imageRules(): array
    {
        return ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:8192'];
    }

    protected function syncImage(Request $request, Model $model, string $folder, string $input = 'image', string $column = 'image_path', int $maxSide = 1600): void
    {
        if ($request->hasFile($input)) {
            $model->update([$column => Images::store($request->file($input), $folder, $maxSide, $model->{$column})]);
        } elseif ($request->boolean("remove_{$input}") && $model->{$column}) {
            Images::delete($model->{$column});
            $model->update([$column => null]);
        }
    }

    protected function syncFile(Request $request, Model $model, string $folder, string $input = 'attachment', string $column = 'attachment_path'): void
    {
        if ($request->hasFile($input)) {
            $old = $model->{$column};
            $model->update([$column => $this->storeFile($request->file($input), $folder)]);
            $this->deleteFile($old);
        } elseif ($request->boolean("remove_{$input}") && $model->{$column}) {
            $this->deleteFile($model->{$column});
            $model->update([$column => null]);
        }
    }

    /** Fichier rangé par mois, sous un nom aléatoire mais avec son extension d'origine */
    protected function storeFile(UploadedFile $file, string $folder): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'document';

        return $file->storeAs($folder . '/' . date('Y/m'), Str::limit($name, 60, '') . '-' . Str::lower(Str::random(8)) . '.' . $extension, 'public');
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
