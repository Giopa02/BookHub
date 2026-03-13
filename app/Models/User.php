<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'role_id',
        'two_factor_code',
        'two_factor_expires_at',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_expires_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    /**
     * Génère un code 2FA et l'enregistre (expire dans 10 minutes)
     */
    public function generateTwoFactorCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        return $code;
    }

    /**
     * Réinitialise le code 2FA
     */
    public function resetTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);
    }
}