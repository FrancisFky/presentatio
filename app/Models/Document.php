<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationStatus;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

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
        return $this->file_size ? Number::fileSize($this->file_size, precision: 1) : null;
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
