<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'email',
        'password',
    ];

    // protected $guarded =[];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // Accessors and Mutators

    protected function name(): Attribute
    {
        return Attribute::make(
            // get: fn($value) => strtoupper($value)
            get: fn($value) => Str::upper($value)
        );

    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcrypt($value)
        );

    }

    protected function isAdmin(): Attribute
    {
        $admins = ['hirushamalith558@gmail.com'];
        return Attribute::make(
            get: fn() => in_array($this->email, $admins)
        );

    }

    // ticket belongs to the user so single user can create multiple tickets which means one to many relationships
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

}
