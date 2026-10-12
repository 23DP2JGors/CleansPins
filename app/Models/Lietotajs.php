<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Lietotajs extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'lietotaji';
    protected $primaryKey = 'lietotaja_id';

    protected $fillable = [
        'vards',
        'uzvards',
        'e_pasts',
        'parole',
        'loma', // 0 - pircējs, 1 - administrators
    ];

    protected $hidden = [
        'parole',
    ];

    protected function casts(): array
    {
        return [
            'parole' => 'hashed',
            'loma' => 'integer',
        ];
    }

    // --- Auth: lietotāji lieto "e_pasts" / "parole", nevis Laravel noklusējuma "email" / "password" ---

    public function getAuthPassword(): string
    {
        return $this->parole;
    }

    public function getAuthPasswordName(): string
    {
        return 'parole';
    }

    public function isAdministrators(): bool
    {
        return (int) $this->loma === 1;
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
}
