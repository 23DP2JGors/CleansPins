<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasutijums extends Model
{
    use HasFactory;

    protected $table = 'pasutijumi';
    protected $primaryKey = 'pasutijuma_id';

    // statusu iespējamās vērtības (lauks "statuss")
    public const STATUS_JAUNS = 'jauns';
    public const STATUS_APSTRADE = 'apstrādē';
    public const STATUS_NOSUTITS = 'nosūtīts';
    public const STATUS_PIEGADATS = 'piegādāts';

    protected $fillable = [
        'lietotaja_id',
        'vards',
        'uzvards',
        'talrunis',
        'datums',
        'statuss',
        'piegades_adrese',
        'pakomata_id',
        'pakomata_pakalpojums',
        'pakomata_nosaukums',
        'pakomata_adrese',
        'kopsumma',
    ];

    protected function casts(): array
    {
        return [
            'datums' => 'datetime',
            'kopsumma' => 'decimal:2',
        ];
    }

    // --- Attiecības ---

    public function lietotajs(): BelongsTo
    {
        return $this->belongsTo(Lietotajs::class, 'lietotaja_id', 'lietotaja_id');
    }

    public function pakomats(): BelongsTo
    {
        return $this->belongsTo(Pakomats::class, 'pakomata_id', 'pakomata_id');
    }

    public function rindas(): HasMany
    {
        return $this->hasMany(PasutijumaRinda::class, 'pasutijuma_id', 'pasutijuma_id');
    }

    // --- Palīgfunkcijas ---

    public function parreikinatKopsummu(): void
    {
        $this->kopsumma = $this->rindas()
            ->get()
            ->sum(fn (PasutijumaRinda $rinda) => $rinda->daudzums * $rinda->cena_bridi);
    }
}
