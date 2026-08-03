<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    /**
     * Castear las fechas para trabajar con Carbon automáticamente.
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }
}
