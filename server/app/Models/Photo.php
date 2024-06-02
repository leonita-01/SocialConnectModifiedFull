<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $table = 'photos';

    protected $fillable = [
        'image_path',
        // Add more fillable attributes as needed
    ];

    // Optional: Define relationships if applicable
    // For example, if a photo belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional: Define accessors and mutators
    // For example, to get the full URL of the photo's image
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
