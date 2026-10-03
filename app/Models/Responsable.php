<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Responsable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'responsable';

    protected $primaryKey = 'id_responsable';

    protected $fillable = [
        'nombre_responsable',
        'apellido_responsable',
        'cargo_responsable',
    ];

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'responsable_id', 'id_responsable');
    }
}
