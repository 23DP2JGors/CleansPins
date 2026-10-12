<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasutijumaRinda extends Model
{
    use HasFactory;

    protected $table = 'pasutijuma_rindas';
    protected $primaryKey = 'rindas_id';
    public $timestamps = false;

    protected $fillable = [
        'pasutijuma_id',
        'produkta_id',
        'daudzums',
        'cena_bridi', // produkta cena pasūtīšanas brīdī — nemainās, ja produkta cena vēlāk mainās
    ];

    protected function casts(): array
    {
        return [
            'daudzums' => 'integer',
            'cena_bridi' => 'decimal:2',
        ];
    }

    public function pasutijums(): BelongsTo
    {
        return $this->belongsTo(Pasutijums::class, 'pasutijuma_id', 'pasutijuma_id');
    }

    public function produkts(): BelongsTo
    {
        return $this->belongsTo(Produkts::class, 'produkta_id', 'produkta_id');
    }

    public function starpsumma(): string
    {
        return bcmul((string) $this->daudzums, (string) $this->cena_bridi, 2);
    }
}
