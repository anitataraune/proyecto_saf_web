<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    protected $guard_name = 'api';

    protected $fillable = [
        'usuario',
        'clave',
        'activo',
    ];

    /**
     * Atributos que deben ocultarse en las respuestas JSON.
     */
    protected $hidden = [
        'clave',
    ];

    /**
     * Conversión de tipos de atributos (Casting).
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'clave' => 'hashed', // Laravel encriptará automáticamente la clave al guardarla
        ];
    }

    /**
     * Indicarle a Laravel que la contraseña de este modelo se llama 'clave'
     * en lugar del campo 'password' que busca por defecto.
     */
    public function getAuthPassword()
    {
        return $this->clave;
    }
}
