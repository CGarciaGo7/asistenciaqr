@csrf

<div class="row">

    {{-- Trabajador --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Trabajador <span class="text-danger">*</span>
        </label>

        <select name="trabajador_id"
                id="trabajador_id"
                class="form-control select2 @error('trabajador_id') is-invalid @enderror">
            <option value="">Seleccione...</option>

            @foreach($trabajadores as $trabajador)
                <option value="{{ $trabajador->id }}"
                    {{ old('trabajador_id', $asignacionHorario->trabajador_id ?? '') == $trabajador->id ? 'selected' : '' }}>
                    {{ $trabajador->codigo }} -
                    {{ $trabajador->apellidos }}
                    {{ $trabajador->nombres }}
                </option>
            @endforeach

        </select>
        @error('trabajador_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Nuevo Horario --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Nuevo Horario <span class="text-danger">*</span>
        </label>

        <select name="horario_id"
                class="form-control select2 @error('horario_id') is-invalid @enderror">
            <option value="">Seleccione...</option>
            @foreach($horarios as $horario)
                <option value="{{ $horario->id }}"
                    {{ old('horario_id', $asignacionHorario->horario_id ?? '') == $horario->id ? 'selected' : '' }}>
                    {{ $horario->nombre }}
                </option>
            @endforeach

        </select>
        @error('horario_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<hr>
<h5 class="text-primary">
    <i class="fas fa-user"></i>
    Información del Trabajador
</h5>

<div class="row">
    <div class="col-md-3 mb-3">
        <label>Empresa</label>
        <input type="text"
            id="empresa"
            class="form-control"
            readonly>
    </div>

    <div class="col-md-3 mb-3">
        <label>Área</label>
        <input type="text"
            id="area"
            class="form-control"
            readonly>
    </div>

    <div class="col-md-3 mb-3">
        <label>Cargo</label>

        <input type="text"
            id="cargo"
            class="form-control"
            readonly>
    </div>

    <div class="col-md-3 mb-3">
        <label>Horario Actual</label>
        <input type="text"
            id="horario_actual"
            class="form-control"
            readonly>
    </div>
    <hr>
    <div id="alertaTipoPersonal" style="display:none;"></div>
    <hr>
</div>

<hr>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>
            Fecha Inicio <span class="text-danger">*</span>
        </label>

        <input type="date"
            name="fecha_inicio"
            value="{{ old('fecha_inicio', $asignacionHorario->fecha_inicio ?? date('Y-m-d')) }}"
            class="form-control">

    </div>

    <div class="col-md-4 mb-3">
        <label>
            Fecha Fin
        </label>

        <input type="date"
            name="fecha_fin"
            value="{{ old('fecha_fin', $asignacionHorario->fecha_fin ?? '') }}"
            class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>
            Estado
        </label>

        <select name="estado"
                class="form-control">
            <option value="1"
                {{ old('estado', $asignacionHorario->estado ?? 1)==1 ? 'selected':'' }}>
                ACTIVO
            </option>

            <option value="0"
                {{ old('estado', $asignacionHorario->estado ?? 1)==0 ? 'selected':'' }}>
                INACTIVO
            </option>
        </select>
    </div>
</div>

<div class="mb-3">

    <label> Observación </label>

    <textarea name="observacion"
        rows="3"
        class="form-control"
        style="text-transform:uppercase">{{ old('observacion', $asignacionHorario->observacion ?? '') }}</textarea>
</div>

<hr>

<div class="text-end">
    <a href="{{ route('asignacion-horarios.index') }}"
    class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Cancelar
    </a>

    <button type="submit"
            class="btn btn-primary">
        <i class="fas fa-save"></i>
        Guardar
    </button>

</div>