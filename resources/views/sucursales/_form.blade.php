    @csrf

<div class="row">
    
    <div class="col-md-8">
        <label>Nombre Sucursal *</label>
        <input
            type="text"
            name="nombre"
            value="{{ old('nombre',$sucursal->nombre ?? '') }}"
            class="form-control mayusculas">
    </div>
</div>

<br>
    <div class="col-md-6">
        <label>Correo</label>
        <input
            type="email"
            name="correo"
            value="{{ old('correo',$sucursal->correo ?? '') }}"
            class="form-control">
    </div>
</div>

<br>
<div class="row">
    <div class="col-md-8">
        <label>Dirección</label>
        <input
            type="text"
            name="direccion"
            value="{{ old('direccion',$sucursal->direccion ?? '') }}"
            class="form-control mayusculas">
    </div>

    <div class="col-md-4">
        <label>Teléfono</label>
        <input
            type="text"
            name="telefono"
            value="{{ old('telefono',$sucursal->telefono ?? '') }}"
            class="form-control">
    </div>
</div>


<br>
<button class="btn btn-primary">
    <i class="fas fa-save"></i> Guardar </button>

<a href="{{ route('sucursales.index') }}"
   class="btn btn-secondary"> Cancelar </a>

@section('js')

<script>

document.getElementById('logo').addEventListener('change',function(e){
    let reader=new FileReader();
    reader.onload=function(event){
        document.getElementById('preview').src=event.target.result;
    }
    reader.readAsDataURL(e.target.files[0]);
});

</script>

@stop