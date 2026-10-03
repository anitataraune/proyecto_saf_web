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
        'tipo_cambio_id',
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

    protected function casts(): array
    {
        return [
            'ufv_inicio'             => 'double',
            'ufv_fin'                => 'double',
            'valor_inicial'          => 'double',
            'actualizacion_valor'    => 'double',
            'valor_actualizado'      => 'double',
            'depreciacion_periodo'   => 'double',
            'depreciacion_acumulada' => 'double',
            'valor_neto'             => 'double',
        ];
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class, 'activo_id', 'id_activo');
    }

    // Relación añadida con TipoCambio
    public function tipoCambio(): BelongsTo
    {
        return $this->belongsTo(TipoCambio::class, 'tipo_cambio_id', 'id_tipo_cambio');
    }
}