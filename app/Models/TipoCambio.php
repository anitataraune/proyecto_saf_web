<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCambio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipo_cambio';

    protected $primaryKey = 'id_tipo_cambio';

    protected $fillable = [
        'ufvs',
        'valor_tc',
        'monto',
    ];

    /**
     * Asegura que los valores decimales mantengan su naturaleza flotante/double al ser devueltos.
     */
    protected function casts(): array
    {
        return [
            'ufvs' => 'double',
            'valor_tc' => 'double',
            'monto' => 'double',
        ];
    }
}
