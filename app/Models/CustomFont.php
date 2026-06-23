<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CustomFont extends Model
{
    protected $fillable = ['name', 'filename'];

    /**
     * Absolute filesystem path DomPDF can load via url() in @font-face.
     * Lives inside DomPDF chroot (base_path()) because storage/app/public
     * is under the project root.
     */
    public function diskPath(): string
    {
        return Storage::disk('public')->path("fonts/custom/{$this->filename}");
    }

    /**
     * Public browser URL for the @font-face src in the editor.
     */
    public function browserUrl(): string
    {
        return Storage::disk('public')->url("fonts/custom/{$this->filename}");
    }
}
