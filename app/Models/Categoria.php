<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categoria';

    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre',
        'descripcion',
        'vida_util', // Ahora manejado como entero para cálculos matemáticos
    ];

    protected function casts(): array
    {
        return [
            'vida_util' => 'integer',
        ];
    }
}
