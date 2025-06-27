<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'bio',
        'profile_image',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        // 'password' => 'hashed',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'username' => $this->username,
            'is_verified' => $this->is_verified,
        ];
    }

    // API response format - Fixed formatting
    // API response format - SIMPLIFIED VERSION
    public function toApiArray()
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'username' => $this->username ?? null,
            'email' => $this->email ?? null,
            'role' => $this->role ?? null,
            'bio' => $this->bio ?? null,
            'profile_image' => $this->profile_image ?? null,
            'is_verified' => $this->is_verified ?? false,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
        ];
    }
}