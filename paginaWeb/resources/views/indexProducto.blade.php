@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Listado de Productos</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Referencia</th>
                    <th>Caja</th>
                    <th>Unidad</th>
                    <th>Composición</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    <tr>
                        <td>{{ $producto->idProducto }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->referencia }}</td>
                        <td>{{ $producto->caja }}</td>
                        <td>{{ $producto->unidad }}</td>
                        <td>{{ $producto->composicion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
