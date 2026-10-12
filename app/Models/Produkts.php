<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produkts extends Model
{
    use HasFactory;

    protected $table = 'produkti';
    protected $primaryKey = 'produkta_id';

    protected $fillable = [
        'kategorijas_id',
        'nosaukums',
        'apraksts',
        'cena',
        'attels',
        'krajums',
    ];

    protected function casts(): array
    {
        return [
            'cena' => 'decimal:2',
            'krajums' => 'integer',
        ];
    }

    // --- Attiecības ---

    public function kategorija(): BelongsTo
    {
        return $this->belongsTo(Kategorija::class, 'kategorijas_id', 'kategorijas_id');
    }

    public function zonas(): BelongsToMany
    {
        return $this->belongsToMany(
            Zona::class,
            'produktu_zonas',
            'produkta_id',
            'zonas_id',
            'produkta_id',
            'zonas_id'
        )->withPivot('produktu_zonas_id');
    }

    public function problemas(): BelongsToMany
    {
        return $this->belongsToMany(
            Problema::class,
            'produktu_problemas',
            'produkta_id',
            'problemas_id',
            'produkta_id',
            'problemas_id'
        )->withPivot('produktu_problemas_id');
    }

    public function grozaRindas(): HasMany
    {
        return $this->hasMany(GrozaRinda::class, 'produkta_id', 'produkta_id');
    }

    public function pasutijumaRindas(): HasMany
    {
        return $this->hasMany(PasutijumaRinda::class, 'produkta_id', 'produkta_id');
    }
}
