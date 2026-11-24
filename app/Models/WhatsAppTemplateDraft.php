<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplateDraft extends Model
{
    protected $table = 'whatsapp_template_drafts';

    protected $fillable = [
        'name', 'category', 'language', 'body', 'footer',
    ];

    public function varCount(): int
    {
        preg_match_all('/\{\{(\d+)\}\}/', (string) $this->body, $m);
        return $m[1] ? max(array_map('intval', $m[1])) : 0;
    }
}
