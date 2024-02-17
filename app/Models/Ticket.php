<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'attachement', 'user_id','status'];

    // how the user can access the ticket which means creating a relationship between ticket vs user 
    // it's belongs to single user

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
