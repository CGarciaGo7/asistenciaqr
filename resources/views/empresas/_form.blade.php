@csrf

<div class="row">
    <div class="col-md-4">
        <label>RUC *</label>
        <input
            type="text"
            id="ruc"
            name="ruc"
            maxlength="11"
            value="{{ old('ruc',$empresa->ruc ?? '') }}"
            class="form-control numeros">

            <small id="mensajeRuc"></small>
    </div>

    <div class="col-md-8">
        <label>Razón Social *</label>
        <input
            type="text"
            name="razon_social"
            value="{{ old('razon_social',$empresa->razon_social ?? '') }}"
            class="form-control mayusculas">
    </div>
</div>

<br>
<div class="row">
    <div class="col-md-6">
        <label>Nombre Comercial *</label>
        <input
            type="text"
            name="nombre_comercial"
            value="{{ old('nombre_comercial',$empresa->nombre_comercial ?? '') }}"
            class="form-control mayusculas">
    </div>

    <div class="col-md-6">
        <label>Correo</label>
        <input
            type="email"
            name="correo"
            value="{{ old('correo',$empresa->correo ?? '') }}"
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
            value="{{ old('direccion',$empresa->direccion ?? '') }}"
            class="form-control mayusculas">
    </div>

    <div class="col-md-4">
        <label>Teléfono</label>
        <input
            type="text"
            name="telefono"
            value="{{ old('telefono',$empresa->telefono ?? '') }}"
            class="form-control">
    </div>
</div>

<br>
<div class="row">
    <div class="col-md-8">
        <label>Logo</label>
        <input
            type="file"
            name="logo"
            id="logo"
            class="form-control"
            accept=".jpg,.jpeg,.png">
    </div>

    <div class="col-md-4 text-center">
        <img
        id="preview"
        src="{{ isset($empresa) && $empresa->logo
                ? asset('storage/'.$empresa->logo)
                : asset('img/logo_empresa.png') }}"
        class="img-thumbnail shadow-sm"
        style="width:210px; height:180px; object-fit:contain; background:#f8f9fa; padding:10px;">
    <br>
    <small class="text-muted">
        Vista previa del logo
    </small>

    </div>
</div>

<br>
<button class="btn btn-primary">
    <i class="fas fa-save"></i> Guardar </button>

<a href="{{ route('empresas.index') }}"
   class="btn btn-secondary"> Cancelar </a>

@section('js')

<script>

$('#ruc').keyup(function(){

    let ruc=$(this).val();

    if(ruc.length!=11){
        $('#mensajeRuc').html('');
        return;
    }
    $.get("{{ url('empresas/validar-ruc') }}/"+ruc,function(data){

        if(data.existe){
            $('#mensajeRuc')
                .html('<span class="text-danger">Este RUC ya existe.</span>');
        }else{
            $('#mensajeRuc')
                .html('<span class="text-success">RUC disponible.</span>');
        }
    });
});

</script>   

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