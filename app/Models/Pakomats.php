<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pakomats extends Model
{
    protected $table = 'pakomati';
    protected $primaryKey = 'pakomata_id';

    public const OMNIVA = 'omniva';
    public const DPD = 'dpd';
    public const LATVIJAS_PASTS = 'latvijas_pasts';

    // pakalpojuma kods => nosaukums, ko rādīt lietotājam
    public const PAKALPOJUMI = [
        self::OMNIVA => 'Omniva',
        self::DPD => 'DPD',
        self::LATVIJAS_PASTS => 'Latvijas Pasts',
    ];

    protected $fillable = [
        'pakalpojums',
        'ext_id',
        'nosaukums',
        'adrese',
        'pilseta',
        'pasta_indekss',
        'latitude',
        'longitude',
        'darba_laiks',
        'aktivs',
    ];

    protected $hidden = [
        'meklesanai',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'aktivs' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // meklēšanas lauku vienmēr atjauno pirms saglabāšanas
        static::saving(function (Pakomats $pakomats) {
            $pakomats->meklesanai = self::normalizet(
                $pakomats->nosaukums . ' ' . $pakomats->adrese . ' ' . $pakomats->pilseta
            );
        });
    }

    // --- Attiecības ---

    public function pasutijumi(): HasMany
    {
        return $this->hasMany(Pasutijums::class, 'pakomata_id', 'pakomata_id');
    }

    // --- Palīgfunkcijas ---

    public function pakalpojumaNosaukums(): string
    {
        return self::PAKALPOJUMI[$this->pakalpojums] ?? $this->pakalpojums;
    }

    // "Omniva pakomāts: <nosaukums>, <adrese>"
    public function pilnaAdrese(): string
    {
        return $this->pakalpojumaNosaukums() . ' pakomāts: ' . $this->nosaukums . ', ' . $this->adrese;
    }

    /**
     * Mazie burti + bez garumzīmēm/mīkstinājumiem (LV, LT, EE),
     * lai meklēšana strādātu vienādi jebkurā DB.
     */
    public static function normalizet(string $teksts): string
    {
        $teksts = mb_strtolower(trim($teksts), 'UTF-8');

        return strtr($teksts, [
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k',
            'ļ' => 'l', 'ņ' => 'n', 'š' => 's', 'ū' => 'u', 'ž' => 'z', 'ō' => 'o',
            'ą' => 'a', 'ę' => 'e', 'ė' => 'e', 'į' => 'i', 'ų' => 'u',
            'ä' => 'a', 'ö' => 'o', 'õ' => 'o', 'ü' => 'u',
        ]);
    }

    // attālums km starp diviem punktiem (haversine)
    public static function attalumsKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
