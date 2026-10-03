<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';
    protected $primaryKey = 'id_configuracion';

    protected $fillable = [
        'nombre_sistema',
        'nombre_empresa',
        'nit_empresa',
        'direccion',
        'telefono',
        'logo',
        'color_primario',
        'color_secundario',
        'color_fondo',
        'color_superficie',
        'color_texto',
        'fuente',
    ];

    // Carga automáticamente logo_url en las respuestas JSON
    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}