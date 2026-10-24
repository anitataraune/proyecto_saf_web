<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Baja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'baja';

    protected $primaryKey = 'id_baja';

    protected $fillable = [
        'activo_id',
        'fecha_baja',
        'motivo_baja',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_baja' => 'date',
        ];
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class, 'activo_id', 'id_activo');
    }
}
