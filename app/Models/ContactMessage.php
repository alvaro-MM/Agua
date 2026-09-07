<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'ip_address',
        'status',
        'internal_notes',
        'handled_by',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContactMessageStatus::class,
            'handled_at' => 'datetime',
        ];
    }

    /** Quién lo atendió. */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /** @param  Builder<static>  $query */
    public function scopeNuevos(Builder $query): void
    {
        $query->where('status', ContactMessageStatus::Nuevo);
    }
}
