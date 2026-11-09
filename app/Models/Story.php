<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Story extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'media_path',
        'media_type',
        'instagram_link',
        'views',
        'pin_position',
        'expires_at',
    ];
    protected $dates = [
        'expires_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 