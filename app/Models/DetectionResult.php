<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DetectionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_image',
        'result_image',
        'model_used',
        'detections',
        'total_defects',
        'status',
        'processing_time',
    ];

    protected $casts = [
        'total_defects'   => 'integer',
        'processing_time' => 'float',
    ];

    // ── Accessors ──────────────────────────────────────────────────

    /**
     * URL publik gambar original
     */
    public function getOriginalImageUrlAttribute(): ?string
    {
        return $this->original_image
            ? Storage::disk('public')->url($this->original_image)
            : null;
    }

    /**
     * URL publik gambar hasil deteksi
     */
    public function getResultImageUrlAttribute(): ?string
    {
        return $this->result_image
            ? Storage::disk('public')->url($this->result_image)
            : null;
    }

    /**
     * Decode detections JSON → array
     */
    public function getDetectionsArrayAttribute(): array
    {
        return json_decode($this->detections ?? '[]', true) ?? [];
    }

    /**
     * Label status yang ramah
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'good'      => 'Good / No Defect',
            'defective' => 'Defective',
            default     => 'Unknown',
        };
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeDefective($query)
    {
        return $query->where('status', 'defective');
    }

    public function scopeGood($query)
    {
        return $query->where('status', 'good');
    }

    public function scopeByModel($query, string $model)
    {
        return $query->where('model_used', $model);
    }
}