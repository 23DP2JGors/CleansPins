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

    // statusu iespējamās vērtības (lauks "statuss", string(30) — jauna migrācija nav vajadzīga)
    public const STATUS_JAUNS = 'jauns';
    public const STATUS_APSTRADE = 'apstrādē';
    public const STATUS_NOSUTITS = 'nosūtīts';
    public const STATUS_PIEGADATS = 'piegādāts';
    public const STATUS_ATCELTS = 'atcelts'; // atcēla pircējs vai administrators

    public const STATUSI = [
        self::STATUS_JAUNS,
        self::STATUS_APSTRADE,
        self::STATUS_NOSUTITS,
        self::STATUS_PIEGADATS,
        self::STATUS_ATCELTS,
    ];

    // statusi, kuros pircējs pats vēl drīkst atcelt pasūtījumu
    public const ATCELAMI_STATUSI = [
        self::STATUS_JAUNS,
        self::STATUS_APSTRADE,
    ];

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

    /**
     * Atceļ pasūtījumu un atgriež preces krājumā (store() tās noņēma).
     * Jāsauc DB::transaction iekšā, pasūtījumam jābūt bloķētam (lockForUpdate).
     */
    public function atcelt(): void
    {
        foreach ($this->rindas()->get() as $rinda) {
            Produkts::whereKey($rinda->produkta_id)->increment('krajums', $rinda->daudzums);
        }

        $this->update(['statuss' => self::STATUS_ATCELTS]);
    }
}
