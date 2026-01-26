<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
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
        'email_verified_at',
        'password',
        'role',
        'street',
        'number',
        'zipcode',
        'city',
        'phonenumber',
        'bankaccountnumber',
        'date_of_birth',
        'blocked_at',
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
        return $this->role === 'admin' && !$this->isBlocked();
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
        if ($value && !empty($value)) {
            try{
                return decrypt($value);
            } catch (\Exception $e) {
                return null;
            }
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
            'blocked_at' => 'datetime',
        ];
    }

    /**
     * Check if the user is blocked
     */
    public function isBlocked(): bool
    {
        return $this->blocked_at !== null;
    }

    public function coinOrders(): HasMany
    {
        return $this->hasMany(CoinOrder::class);
    }

    public function roomReservations(): HasMany
    {
        return $this->hasMany(RoomReservation::class);
    }

    public function solidarityFundAuthorizations(): HasMany
    {
        return $this->hasMany(SolidarityFundAuthorization::class);
    }

    public function zaaierAuthorizations(): HasMany
    {
        return $this->hasMany(ZaaierAuthorization::class);
    }

    public function privacyConsents(): HasMany
    {
        return $this->hasMany(PrivacyConsent::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function scipioRegistrations(): HasMany
    {
        return $this->hasMany(ScipioRegistration::class, 'email', 'email');
    }
}
