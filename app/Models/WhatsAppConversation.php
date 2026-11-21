<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'wa_phone', 'display_name', 'client_id',
        'last_message_at', 'last_inbound_at', 'unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'last_inbound_at' => 'datetime',
        'unread_count'    => 'integer',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id')->orderBy('sent_at');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Whether a freeform (non-template) reply is still allowed.
     * Meta enforces a 24h customer-service window after the last inbound message.
     */
    public function canSendFreeform(): bool
    {
        return $this->last_inbound_at
            && $this->last_inbound_at->greaterThan(now()->subHours(24));
    }
}
