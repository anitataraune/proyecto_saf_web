<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activo extends Model
{
    use HasFactory, SoftDeletes;

    // Tu tabla en singular
    protected $table = 'activo';

    // Tu llave primaria personalizada
    protected $primaryKey = 'id_activo';

    protected $fillable = [
        'descripcion',
        'valor_compra',
        'fecha_compra',
        'nro_depreciacion',
        'imp_depreciacion',
        'estado_actual',
        'categoria_id',
    ];

    /**
     * Conversión de tipos de atributos (Casting).
     */
    protected function casts(): array
    {
        return [
            'valor_compra'     => 'float',
            'fecha_compra'     => 'date:Y-m-d',
            'nro_depreciacion' => 'integer',
            'imp_depreciacion' => 'float',
        ];
    }

    /**
     * Relación: Un activo pertenece a una Categoría.
     */
    public function categoria()
    {
        // Ajusta 'id_categoria' si la llave primaria en tu modelo Categoria se llama distinto
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }

    public function bajas()
    {
        return $this->hasMany(Baja::class, 'activo_id', 'id_activo');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'activo_id', 'id_activo');
    }

    public function asignacionActual(): HasOne
    {
        return $this->hasOne(Asignacion::class, 'activo_id', 'id_activo')
                    ->where('estado_asignacion', 'asignado')
                    ->latestOfMany();
    }
}