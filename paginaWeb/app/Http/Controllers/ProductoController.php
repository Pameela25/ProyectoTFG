<?php

namespace App\Http\Controllers;
use App\Models\Producto;

class ProductoController extends Controller
{
    //funcion encargada de listar los productos y se envian a la vista index
    public function index()
    {
        $productos = Producto::all();
        return view('indexProducto', compact('productos'));
    }

}
