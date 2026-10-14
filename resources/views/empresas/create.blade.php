@extends('adminlte::page')

@section('title','Nueva Empresa')

@section('content')

<div class="card">
    <div class="card-header bg-primary">
        <h4>
            <i class="fas fa-building"></i>
            Nueva Empresa
        </h4>
    </div>

    <form
        action="{{ route('empresas.store') }}"
        method="POST"
        enctype="multipart/form-data">

        <div class="card-body">
            @include('empresas._form')
        </div>
    </form>
</div>

@stop

