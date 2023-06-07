<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\HomeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Ruta para mostrar el formulario de inserción de datos de la empresa
Route::get('/insertar-empresa', [EmpresaController::class, 'showInsertForm'])->name('insertar-empresa');
//guardamos los datos de la empresa
Route::post('/guardar-empresa', [EmpresaController::class,'guardar'])->name('guardar-empresa');


// Ruta para mostrar los productos existentes
Route::get('/productos', [ProductoController::class, 'index'])->name('indexProducto');

// Ruta para mostrar los clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('indexCliente');
