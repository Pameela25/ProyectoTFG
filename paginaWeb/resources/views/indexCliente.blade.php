@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Listado de Clientes</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>nombre</th>
                    <th>telefono</th>
                    <th>idEmpresa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->idCliente }}</td>
                        <td>{{ $cliente->Nombre }}</td>
                        <td>{{ $cliente->telefono }}</td>
                        <td>{{ $cliente->idEmpresa }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
