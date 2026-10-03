<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PolizaSeguro extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $table = 'poliza_seguro';
    protected $primaryKey = 'id_seguro';

    protected $fillable = [
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date:Y-m-d',
            'fecha_fin'    => 'date:Y-m-d',
        ];
    }

    public function activos(): BelongsToMany
    {
        return $this->belongsToMany(
            Activo::class, 
            'activo_poliza', 
            'poliza_seguro_id', 
            'activo_id'
        )
        ->withPivot('fecha_asignacion')
        ->withTimestamps();             
    }
}