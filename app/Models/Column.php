<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_id
 * @property int order
 */
class Column extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'order'];

    protected $casts = [
        'user_id' => 'int'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class)
            ->orderBy('order');
    }
}
