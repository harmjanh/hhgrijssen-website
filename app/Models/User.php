<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'street',
        'number',
        'zipcode',
        'city',
        'phonenumber',
        'bankaccountnumber',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'bankaccountnumber',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' || str_ends_with($this->email, '@hhgrijssen.nl');
    }

    /**
     * Check if the user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Set the bank account number with encryption
     *
     * @param  string  $value
     * @return void
     */
    public function setBankaccountnumberAttribute($value)
    {
        if ($value) {
            $this->attributes['bankaccountnumber'] = encrypt($value);
        } else {
            $this->attributes['bankaccountnumber'] = null;
        }
    }

    /**
     * Get the decrypted bank account number
     *
     * @param  string  $value
     * @return string|null
     */
    public function getBankaccountnumberAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }

        return null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
