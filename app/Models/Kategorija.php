<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategorija extends Model
{
    use HasFactory;

    protected $table = 'kategorijas';
    protected $primaryKey = 'kategorijas_id';

    protected $fillable = [
        'nosaukums',
        'apraksts',
    ];

    public function produkti(): HasMany
    {
        return $this->hasMany(Produkts::class, 'kategorijas_id', 'kategorijas_id');
    }
}
