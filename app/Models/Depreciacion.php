<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depreciacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'depreciacion';
    protected $primaryKey = 'id_depreciacion';

    protected $fillable = [
        'activo_id',
        'mes',
        'anio',
        'fecha_proceso',
        'metodo_depreciacion',
        'ufv_inicio',
        'ufv_fin',
        'valor_inicial',
        'actualizacion_valor',
        'valor_actualizado',
        'depreciacion_periodo',
        'depreciacion_acumulada',
        'valor_neto',
    ];

    public function activo()
    {
        return $this->belongsTo(Activo::class, 'activo_id', 'id_activo');
    }
}