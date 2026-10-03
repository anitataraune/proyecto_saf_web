<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asignacion';
    protected $primaryKey = 'id_asignacion';

    protected $fillable = [
        'activo_id',
        'responsable_id',
        'fecha_asignacion',
        'fecha_devolucion',
        'estado_asignacion',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'date',
            'fecha_devolucion' => 'date',
        ];
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class, 'activo_id', 'id_activo');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Responsable::class, 'responsable_id', 'id_responsable');
    }
}