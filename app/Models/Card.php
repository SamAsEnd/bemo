<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'order'];

    protected $casts = [
        'column_id' => 'int'
    ];

    public function column()
    {
        return $this->belongsTo(Column::class)
            ->orderBy('order,id');
    }
}
