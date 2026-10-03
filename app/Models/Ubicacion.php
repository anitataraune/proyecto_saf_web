<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ubicacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ubicacion';

    protected $primaryKey = 'id_ubicacion';

    protected $fillable = [
        'nombre_ubicacion',
        'descripcion_ubicacion',
    ];

    public function activos(): HasMany
    {
        return $this->hasMany(Activo::class, 'ubicacion_id', 'id_ubicacion');
    }
}
