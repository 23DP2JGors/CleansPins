<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Lietotajs extends Authenticatable
{
    use HasFactory, Notifiable, MustVerifyEmailTrait;

    protected $table = 'lietotaji';
    protected $primaryKey = 'lietotaja_id';

    protected $fillable = [
        'vards',
        'uzvards',
        'name',
        'email',
        'e_pasts',
        'parole',
        'password',
        'loma',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'parole',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'parole' => 'hashed',
            'password' => 'hashed',
            'loma' => 'integer',
            'email_verified_at' => 'datetime',
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'e_pasts';
    }

    public function getEmailForPasswordReset(): string
    {
        return (string) ($this->email ?? $this->e_pasts ?? '');
    }

    public function getAuthPassword(): string
    {
        return (string) ($this->parole ?? $this->password ?? '');
    }

    public function getAuthPasswordName(): string
    {
        return 'parole';
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['name'] ?? $this->attributes['vards'] ?? '');
    }

    public function setNameAttribute($value): void
    {
        $value = trim((string) $value);
        $this->attributes['name'] = $value;
        $this->attributes['vards'] = $value;
        $this->attributes['uzvards'] = '';
    }

    public function getEmailAttribute(): string
    {
        return (string) ($this->attributes['email'] ?? $this->attributes['e_pasts'] ?? '');
    }

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $value;
        $this->attributes['e_pasts'] = $value;
    }

    public function getPasswordAttribute(): string
    {
        return (string) ($this->attributes['password'] ?? $this->attributes['parole'] ?? '');
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = $value;
        $this->attributes['parole'] = $value;
    }

    public function isAdministrators(): bool
    {
        return (int) $this->loma === 1;
    }

    public function getIdAttribute(): mixed
    {
        return $this->attributes[$this->getKeyName()] ?? null;
    }

    public function setIdAttribute($value): void
    {
        $this->attributes[$this->getKeyName()] = $value;
    }

    // --- Attiecības ---

    public function grozaRindas(): HasMany
    {
        return $this->hasMany(GrozaRinda::class, 'lietotaja_id', 'lietotaja_id');
    }

    public function pasutijumi(): HasMany
    {
        return $this->hasMany(Pasutijums::class, 'lietotaja_id', 'lietotaja_id');
    }

    public function dzestSavosPasutijumus(): void
    {
        $this->deleteOwnOrders();
    }

    public function deleteOwnOrders(): void
    {
        $this->pasutijumi()->with('rindas')->get()->each(function (Pasutijums $pasutijums): void {
            $pasutijums->rindas()->delete();
            $pasutijums->delete();
        });
    }
}
