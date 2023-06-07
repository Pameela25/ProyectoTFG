<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    protected $table = 'empresa';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'tipo',
        'telefono',
        'documentacionCliente',
        'documentacionProducto',
        'idUsuario'
    ];
    //Establecemos la relacion
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}
