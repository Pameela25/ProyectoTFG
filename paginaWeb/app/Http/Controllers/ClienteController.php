<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
class ClienteController extends Controller
{
     //funcion encargada de listar los productos y se envian a la vista index
     public function index()
    {
         $clientes = Cliente::all();
         return view('indexCliente', compact('clientes'));
    }
  
}
