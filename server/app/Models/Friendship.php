<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING = 'pending';

    public function setStatusAttribute($value)
    {
        if (in_array($value, [self::STATUS_ACCEPTED, self::STATUS_REJECTED, self::STATUS_PENDING])) {
            $this->attributes['status'] = $value;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function getRelatedUserAttribute()
    {
        $userId = auth()->id();
        return $this->user_id === $userId ? $this->friend : $this->user;
    }

}
