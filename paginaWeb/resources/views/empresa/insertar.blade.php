@extends('layouts.app')

@section('content')
    <div class="container">
        
        <h1>Insertar Datos de la Empresa</h1>
        <!--Indicamos las advertencias-->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('guardar-empresa') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="nombre">Nombre de la Empresa:</label>
                <input type="text" name="nombre" id="nombre" class="form-control">
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Empresa:</label>
                <input type="text" name="tipo" id="tipo" class="form-control">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono"class="form-control" pattern="^34\d{9}$" title="Por favor, introduce un número de teléfono válido">
            </div>
            <div class="form-group">
                <label for="documentacionCliente">Documentación del Cliente:</label>
                <input type="file" name="documentacionCliente" id="documentacionCliente" class="form-control-file" >
           </div>
            </br>
            <div class="form-group">
                <label for="documentacionProducto">Documentación del Producto:</label>
                <input type="file" name="documentacionProducto" id="documentacionProducto" class="form-control-file" >
            </div>
            
            <input type="hidden" name="idUsuario" value="{{ Auth::user()->id }}">


        
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
@endsection
