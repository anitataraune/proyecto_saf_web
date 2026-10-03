<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCambio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipo_cambio';
    protected $primaryKey = 'id_tipo_cambio';

    protected $fillable = [
        'fecha', // Campo nuevo
        'ufvs',
        'valor_tc',
        'monto',
    ];

    protected function casts(): array
    {
        return [
            'fecha'    => 'date:Y-m-d',
            'ufvs'     => 'double',
            'valor_tc' => 'double',
            'monto'    => 'double',
        ];
    }

    public function depreciaciones(): HasMany
    {
        return $this->hasMany(Depreciacion::class, 'tipo_cambio_id', 'id_tipo_cambio');
    }
}