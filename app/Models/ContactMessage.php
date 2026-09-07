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

    /** @param  Builder<static>  $query */
    public function scopePendientes(Builder $query): void
    {
        $query->whereIn('status', [ContactMessageStatus::Nuevo, ContactMessageStatus::Leido]);
    }

    /**
     * Deja constancia de quién cerró el mensaje y cuándo. Va en un evento del
     * modelo y no en el formulario para que también aplique a los cambios de
     * estado hechos en lote desde el listado.
     */
    protected static function booted(): void
    {
        static::saving(function (self $message): void {
            if (! $message->isDirty('status')) {
                return;
            }

            if ($message->status->isPending()) {
                $message->handled_by = null;
                $message->handled_at = null;

                return;
            }

            $message->handled_by ??= auth()->id();
            $message->handled_at ??= now();
        });
    }

    /** Enlace de WhatsApp al remitente, si dejó teléfono. */
    public function whatsappUrl(): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $this->phone);

        if (blank($digits)) {
            return null;
        }

        // Sin prefijo internacional se asume España.
        $number = str_starts_with($digits, '34') ? $digits : '34'.ltrim($digits, '0');

        return 'https://wa.me/'.$number;
    }
}
