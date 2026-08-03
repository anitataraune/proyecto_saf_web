<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubicacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ubicacion';

    protected $primaryKey = 'id_ubicacion';

    protected $fillable = [
        'nombre_ubicacion',
        'descripcion_ubicacion',
    ];
}
