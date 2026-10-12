<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Zona extends Model
{
    use HasFactory;

    protected $table = 'zonas';
    protected $primaryKey = 'zonas_id';
    public $timestamps = false;

    protected $fillable = [
        'nosaukums', // piem., "virsbūve", "salons", "diski"
    ];

    public function produkti(): BelongsToMany
    {
        return $this->belongsToMany(
            Produkts::class,
            'produktu_zonas',
            'zonas_id',
            'produkta_id',
            'zonas_id',
            'produkta_id'
        )->withPivot('produktu_zonas_id');
    }
}
