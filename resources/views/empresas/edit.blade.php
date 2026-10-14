@extends('adminlte::page')

@section('title','Editar Empresa')

@section('content')

<div class="card">
    <div class="card-header bg-warning">
        <h4>
            <i class="fas fa-edit"></i>
            Editar Empresa
        </h4>
    </div>

    <form
        action="{{ route('empresas.update',$empresa) }}"
        method="POST"
        enctype="multipart/form-data">
        @method('PUT')
        <div class="card-body">
            @include('empresas._form')
        </div>
    </form>
</div>
@stop