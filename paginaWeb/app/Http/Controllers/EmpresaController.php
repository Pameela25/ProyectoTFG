<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use League\Csv\Reader;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Cliente;
class EmpresaController extends Controller
{//Funcion encargada de indicar la vista para insertar la empresa
    public function showInsertForm()
    {
        return view('empresa.insertar'); // nombre de tu vista
    }

    //Funcion encargada de guardar los datos
    public function guardar(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'nombre' => 'required',
            'tipo' => 'required',
            'telefono' => 'required',
            'documentacionCliente' => 'required',
            'documentacionProducto' => 'required',
            'idUsuario' => 'required',
        ],[
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'tipo.required' => 'El tipo de la empresa es obligatorio.',
            'telefono.required' => 'El tipo de la empresa es obligatorio.',
            'documentacionCliente.required' => 'El archivo de documentación del cliente es obligatorio.',
            'documentacionProducto.required' => 'El archivo de documentación del producto es obligatorio.',
            'documentacionCliente.mimes' => 'El archivo de documentación del cliente debe ser un archivo CSV o TXT.',
            'documentacionProducto.mimes' => 'El archivo de documentación del producto debe ser un archivo CSV o TXT.',
        ]);

        // Crear una nueva instancia de Empresa con los datos del formulario
        $empresa = new Empresa();
        $empresa->nombre = $request->nombre;
        $empresa->tipo = $request->tipo;
        $empresa->telefono = $request->telefono;

        //Guardamos la ruta del archivo CSV 
        if ($request->hasFile('documentacionCliente')) {
            $file = $request->file('documentacionCliente');
            $filePath = $file->getRealPath();
            $empresa->documentacionCliente = $filePath;
            if (!$this->isValidCsvFormat($filePath, ['nombre', 'telefono'])) {
                return redirect()->back()->with('error', 'El archivo CSV de clientes debe contener las columnas "nombre" y "telefono".');
            }
        }else {
            // Manejar el caso en el que no se haya proporcionado un archivo CSV
            return redirect()->back()->with('error', 'No se ha proporcionado un archivo CSV');
        }
        
        // Guardar la ruta del archivo CSV en el campo documentacionProducto
        if ($request->hasFile('documentacionProducto')) {
            $file = $request->file('documentacionProducto');
            $filePath = $file->getRealPath();
            $empresa->documentacionProducto = $filePath;
             // Validar el formato del archivo CSV de productos
            if (!$this->isValidCsvFormat($filePath, ['productos', 'referencia', 'caja', 'unidades', 'composicion'])) {
                return redirect()->back()->with('error', 'El archivo CSV de productos debe contener las columnas "productos", "referencia", "caja", "unidades" y "composicion".');
            }
        }else {
            // Manejar el caso en el que no se haya proporcionado un archivo CSV
            return redirect()->back()->with('error', 'No se ha proporcionado un archivo CSV');
        }
        
        $empresa->idUsuario = $request->idUsuario;
        // Guardar la empresa en la base de datos
        $empresa->save();

        
        // Leer el archivo CSV y crear los clientes
        if ($request->hasFile('documentacionCliente')) {
            $csv = Reader::createFromPath($request->file('documentacionCliente')->getPathname());
            $csv->setHeaderOffset(0); // Si el archivo CSV tiene encabezados

            foreach ($csv as $row) {
                $cliente = new Cliente();
                $cliente->nombre = $row['nombre'];
                $cliente->telefono = $row['telefono'];
                $cliente->idEmpresa = $empresa->id;// Asignar el ID de la empresa creada anteriormente
                $cliente->save();
            }
        }
        // Leer el archivo CSV y crear los productos
        if ($request->hasFile('documentacionProducto')) {
            $csv = Reader::createFromPath($request->file('documentacionProducto')->getPathname());
            $csv->setHeaderOffset(0); // Si el archivo CSV tiene encabezados

            foreach ($csv as $row) {
                $producto = new Producto();
                $producto->nombre = $row['productos'];
                $producto->referencia = $row['referencia'];
                $producto->caja = $row['caja'];
                $producto->unidad = $row['unidades'];
                $producto->composicion = $row['composicion'];
                $producto->save();
            }
        }
        // Redirigir a una página de home
        return redirect()->route('home');
    }
    // Función para validar el formato de un archivo CSV
    private function isValidCsvFormat($filePath, $requiredColumns)
    {
        $csv = Reader::createFromPath($filePath);
        $csv->setHeaderOffset(0);

        $headers = $csv->getHeader();

        return empty(array_diff($requiredColumns, $headers));
    }
    
}