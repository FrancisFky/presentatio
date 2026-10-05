<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/** Formulaire ou publication à télécharger */
class Document extends Model
{
    use HasTranslations, HasPublicationStatus;

    protected $guarded = [];

    public const EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'odt', 'zip', 'jpg', 'jpeg', 'png'];

    public function extension(): string
    {
        return strtolower(pathinfo($this->file_name ?: $this->file_path, PATHINFO_EXTENSION));
    }

    public function humanSize(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        // Sans l'extension intl (absente de l'hébergement) : Number::fileSize() planterait
        $units = app()->getLocale() === 'fr' ? ['o', 'Ko', 'Mo', 'Go'] : ['B', 'KB', 'MB', 'GB'];
        $size = (float) $this->file_size;
        $unit = 0;
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        $separator = app()->getLocale() === 'fr' ? ',' : '.';

        return number_format($size, $unit === 0 ? 0 : 1, $separator, ' ') . ' ' . $units[$unit];
    }

    public function icon(): string
    {
        return match ($this->extension()) {
            'pdf' => 'ph-file-pdf',
            'doc', 'docx', 'odt' => 'ph-file-doc',
            'xls', 'xlsx' => 'ph-file-xls',
            'zip' => 'ph-file-zip',
            'jpg', 'jpeg', 'png' => 'ph-file-image',
            default => 'ph-file',
        };
    }
}
