<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProviderRequirement extends Model
{
    use HasFactory;

    protected $table = 'provider_requirements';

    protected $fillable = [
        'provider_id',
        'handyman_id',
        'key',
        'file',
        'status',
        'remarks',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'handyman_id' => 'integer',
    ];

    protected $appends = [
        'file_url',
    ];

    /**
     * Get the full public URL of the uploaded requirement file.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (empty($this->file)) {
            return null;
        }

        if (filter_var($this->file, FILTER_VALIDATE_URL)) {
            return $this->file;
        }

        return Storage::disk('public')->url($this->file);
    }

    /**
     * The provider this requirement belongs to.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * The optional handyman this requirement is associated with.
     */
    public function handyman()
    {
        return $this->belongsTo(User::class, 'handyman_id');
    }
}
