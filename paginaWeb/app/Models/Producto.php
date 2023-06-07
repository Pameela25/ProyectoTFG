<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table = 'producto';

    protected $primaryKey = 'idProducto';

    protected $fillable = [
        'nombre',
        'referencia',
        'caja',
        'unidad',
        'composicion',
    ];
    //hemos desactivado las marcas de tiempo
    public $timestamps = false;
}
