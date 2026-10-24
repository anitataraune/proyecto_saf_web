<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Activo extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $table = 'activo';
    protected $primaryKey = 'id_activo';

    protected $fillable = [
        'descripcion',
        'valor_compra',
        'fecha_compra',
        'nro_depreciacion',
        'imp_depreciacion',
        'estado_actual',
        'categoria_id',
        'ubicacion_id',
        'proveedor_id',
    ];

    protected function casts(): array
    {
        return [
            'valor_compra'     => 'float',
            'fecha_compra'     => 'date:Y-m-d',
            'nro_depreciacion' => 'integer',
            'imp_depreciacion' => 'float',
        ];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'id_proveedor');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id', 'id_ubicacion');
    }

    public function bajas(): HasMany
    {
        return $this->hasMany(Baja::class, 'activo_id', 'id_activo');
    }

    public function depreciaciones(): HasMany
    {
        return $this->hasMany(Depreciacion::class, 'activo_id', 'id_activo');
    }

    // Estado que le corresponde según su última depreciación (sin considerar bajas)
    public function estadoSegunDepreciacion(): string
    {
        $ultima = $this->depreciaciones()->orderByDesc('anio')->orderByDesc('mes')->first();

        return ($ultima && $ultima->valor_neto <= 0) ? 'depreciado' : 'activo';
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'activo_id', 'id_activo');
    }

    public function asignacionActual(): HasOne
    {
        return $this->hasOne(Asignacion::class, 'activo_id', 'id_activo')
                    ->where('estado_asignacion', 'asignado')
                    ->latestOfMany('id_asignacion'); 
    }

    public function polizas(): BelongsToMany
    {
        return $this->belongsToMany(
            PolizaSeguro::class, 
            'activo_poliza', 
            'activo_id', 
            'poliza_seguro_id'
        )
        ->withPivot('fecha_asignacion')
        ->withTimestamps();
    }
}