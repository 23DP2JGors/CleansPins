<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Problema extends Model
{
    use HasFactory;

    protected $table = 'problemas';
    protected $primaryKey = 'problemas_id';
    public $timestamps = false;

    protected $fillable = [
        'nosaukums', // piem., "traipi uz sēdekļa", "skrāpējumi"
    ];

    public function produkti(): BelongsToMany
    {
        return $this->belongsToMany(
            Produkts::class,
            'produktu_problemas',
            'problemas_id',
            'produkta_id',
            'problemas_id',
            'produkta_id'
        )->withPivot('produktu_problemas_id');
    }
}
