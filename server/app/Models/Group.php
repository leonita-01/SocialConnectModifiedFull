<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $fillable = [
        'id',
        'name',
        'description',
        'owner_id'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
//    public function members()
//    {
//        return $this->hasMany(GroupMember::class);
//    }

}


?>
