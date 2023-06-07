@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Panel') }}</div>
                <div class="btn-group">
                    <a href="{{ route('indexProducto') }}" class="btn btn-primary">Ver Productos</a>
                    <a href="{{ route('indexCliente') }}" class="btn btn-primary">Ver Cliente</a>
                </div>  
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Has iniciado sesión!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
