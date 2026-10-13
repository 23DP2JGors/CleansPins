<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class GrozaRinda extends Model
{
    use HasFactory;

    protected $table = 'groza_rindas';
    protected $primaryKey = 'rindas_id';

    protected $fillable = [
        'lietotaja_id',
        'produkta_id',
        'daudzums',
    ];

    protected function casts(): array
    {
        return [
            'daudzums' => 'integer',
        ];
    }

    public function lietotajs(): BelongsTo
    {
        return $this->belongsTo(Lietotajs::class, 'lietotaja_id', 'lietotaja_id');
    }

    public function produkts(): BelongsTo
    {
        return $this->belongsTo(Produkts::class, 'produkta_id', 'produkta_id');
    }

    public function starpsumma(): string
    {
        return bcmul((string) $this->daudzums, (string) $this->produkts->cena, 2);
    }
}
