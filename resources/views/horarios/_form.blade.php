@csrf

<div class="row">

    {{-- Empresa --}}
    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">
            Empresa <span class="text-danger">*</span>
        </label>

        <div class="row">

    <div class="col-md-12">
        <div class="alert alert-info">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label>
                        <strong>
                            Plantilla de Horario
                        </strong>
                    </label>

                    @if(!isset($horario))
                    <div class="alert alert-info">
                        <select
                            id="plantilla"
                            class="form-control">

                            <option value="">
                                -- Seleccione una plantilla --
                            </option>

                            <option value="MANANA">
                                Turno Mañana
                            </option>

                            <option value="TARDE">
                                Turno Tarde
                            </option>

                            <option value="TARDE2">
                                Turno Tarde 2
                            </option>

                            <option value="VENTAS">
                                Ventas (Partido)
                            </option>

                            <option value="QUIMICO">
                                Químico Farmacéutico
                            </option>

                            <option value="ADMINISTRATIVO">
                                Administrativo
                            </option>

                            <option value="ALMACEN">
                                Almacén
                            </option>
                        </select>
                    </div>

                    @endif
                </div>

                <div class="col-md-4">
                    <button
                        type="button"
                        class="btn btn-success btn-block"
                        id="btnPlantilla">
                        <i class="fas fa-magic"></i>
                        Cargar Plantilla
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

        <select
            name="empresa_id"
            id="empresa_id"
            class="form-control @error('empresa_id') is-invalid @enderror"
            required>

            <option value="">Seleccione una empresa</option>
            @foreach($empresas as $empresa)
                <option value="{{ $empresa->id }}"
                    {{ old('empresa_id', $horario->empresa_id ?? '') == $empresa->id ? 'selected' : '' }}>
                    {{ $empresa->nombre_comercial }}
                </option>
            @endforeach
        </select>

        @error('empresa_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Nombre --}}
    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">
            Nombre del Horario
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="nombre"
            id="nombre"
            class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre',$horario->nombre ?? '') }}"
            autocomplete="off"
            required>
        <small id="mensajeNombre"></small>

        @error('nombre')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<hr>
<h5 class="text-primary">
    <i class="fas fa-clock"></i>
    Configuración del Turno
</h5>
<hr>
<div class="row">
    {{-- Tipo de Turno --}}
    <div class="col-md-4 mb-3">
        <label class="font-weight-bold"> Tipo de Turno</label>
        <select  name="tipo_turno" id="tipo_turno" class="form-control">
            <option value="CONTINUO"
                {{ old('tipo_turno',$horario->tipo_turno ?? 'CONTINUO')=='CONTINUO'?'selected':'' }}>
                CONTINUO
            </option>

            <option value="PARTIDO"
                {{ old('tipo_turno',$horario->tipo_turno ?? '')=='PARTIDO'?'selected':'' }}>
                PARTIDO
            </option>
        </select>
    </div>

    {{-- Refrigerio --}}
    <div class="col-md-4 mb-3">
        <label class="font-weight-bold"> Refrigerio (min) </label>

        <input
            type="number"
            min="0"
            max="180"
            name="refrigerio"
            class="form-control"
            value="{{ old('refrigerio',$horario->refrigerio ?? 0) }}">
    </div>

    {{-- Tolerancia --}}
    <div class="col-md-4 mb-3">
        <label class="font-weight-bold"> Tolerancia (min) </label>
        <input
            type="number"
            min="0"
            max="60"
            name="tolerancia"
            class="form-control"
            value="{{ old('tolerancia',$horario->tolerancia ?? 10) }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <strong>Primer Bloque</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Hora Ingreso</label>
                        <input
                            type="time"
                            name="hora_ingreso"
                            class="form-control"
                            value="{{ old('hora_ingreso',$horario->hora_ingreso ?? '') }}"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label>Hora Salida</label>
                        <input
                            type="time"
                            name="hora_salida"
                            class="form-control"
                            value="{{ old('hora_salida',$horario->hora_salida ?? '') }}"
                            required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6" id="bloque2">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <strong>Segundo Bloque</strong>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Hora Ingreso</label>
                        <input
                            type="time"
                            name="hora_ingreso2"
                            id="hora_ingreso2"
                            class="form-control"
                            value="{{ old('hora_ingreso2',$horario->hora_ingreso2 ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label>Hora Salida</label>
                        <input
                            type="time"
                            name="hora_salida2"
                            id="hora_salida2"
                            class="form-control"
                            value="{{ old('hora_salida2',$horario->hora_salida2 ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="font-weight-bold"> Observaciones</label>

        <textarea
            name="observacion"
            id="observacion"
            rows="3"
            class="form-control">{{ old('observacion',$horario->observacion ?? '') }}</textarea>
    </div>


    <div class="col-md-4 mb-3">
        <label class="font-weight-bold"> Estado</label>

        <select name="estado" class="form-control">
            <option value="1"
                {{ old('estado',$horario->estado ?? 1)==1?'selected':'' }}>
                Activo
            </option>

            <option value="0"
                {{ old('estado',$horario->estado ?? 1)==0?'selected':'' }}>
                Inactivo
            </option>
        </select>
    </div>
</div>

<hr>
<div class="text-right">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar </button>
    <a href="{{ route('horarios.index') }}" class="btn btn-secondary"> Cancelar </a>
</div>

@push('js')
<script>
$("#btnPlantilla").click(function(){

    let plantilla=$("#plantilla").val();

    switch(plantilla){

        case "MANANA":

            $("#nombre").val("TURNO MAÑANA");
            $("#tipo_turno").val("CONTINUO").change();

            $("input[name='hora_ingreso']").val("07:00");
            $("input[name='hora_salida']").val("15:00");

            $("input[name='refrigerio']").val(0);
            $("input[name='tolerancia']").val(10);

        break;

        case "TARDE":

            $("#nombre").val("TURNO TARDE");

            $("#tipo_turno").val("CONTINUO").change();

            $("input[name='hora_ingreso']").val("15:00");
            $("input[name='hora_salida']").val("23:00");

            $("input[name='refrigerio']").val(0);
            $("input[name='tolerancia']").val(10);

        break;

        case "TARDE2":

            $("#nombre").val("TURNO TARDE 2");

            $("#tipo_turno").val("CONTINUO").change();

            $("input[name='hora_ingreso']").val("14:00");
            $("input[name='hora_salida']").val("22:00");

            $("input[name='refrigerio']").val(0);
            $("input[name='tolerancia']").val(10);

        break;

        case "VENTAS":

            $("#nombre").val("VENTAS");

            $("#tipo_turno").val("PARTIDO").change();

            $("input[name='hora_ingreso']").val("10:00");
            $("input[name='hora_salida']").val("14:00");

            $("input[name='hora_ingreso2']").val("18:00");
            $("input[name='hora_salida2']").val("22:00");

            $("input[name='refrigerio']").val(0);
            $("input[name='tolerancia']").val(10);

        break;

        case "QUIMICO":

            $("#nombre").val("QUÍMICO FARMACÉUTICO");

            $("#tipo_turno").val("CONTINUO").change();

            $("input[name='hora_ingreso']").val("07:00");
            $("input[name='hora_salida']").val("16:00");

            $("input[name='refrigerio']").val(60);
            $("input[name='tolerancia']").val(10);

        break;

        case "ADMINISTRATIVO":

            $("#nombre").val("ADMINISTRATIVO");

            $("#tipo_turno").val("CONTINUO").change();

            $("input[name='hora_ingreso']").val("08:00");
            $("input[name='hora_salida']").val("16:00");

            $("input[name='refrigerio']").val(60);
            $("input[name='tolerancia']").val(10);
        break;

        case "ALMACEN":
            $("#nombre").val("ALMACÉN");

            $("#tipo_turno").val("CONTINUO").change();

            $("input[name='hora_ingreso']").val("09:00");
            $("input[name='hora_salida']").val("17:00");

            $("input[name='refrigerio']").val(60);
            $("input[name='tolerancia']").val(10);

        break;
    }

});
</script>

<script>

$(function(){
    function controlarTurno(){
        let tipo=$("#tipo_turno").val();
        if(tipo==="PARTIDO"){
            $("#bloque2").slideDown();
            $("#hora_ingreso2").prop("required",true);
            $("#hora_salida2").prop("required",true);
        }else{
            $("#bloque2").slideUp();
            $("#hora_ingreso2").prop("required",false).val('');
            $("#hora_salida2").prop("required",false).val('');
        }
    }
    controlarTurno();
    $("#tipo_turno").change(controlarTurno);
    $("#nombre,#observacion").on("keyup",function(){
        $(this).val($(this).val().toUpperCase());
    });
});
</script>

@endpush