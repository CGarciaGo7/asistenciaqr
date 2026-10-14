@csrf

<div class="row">
    
    {{-- Estado --}}
    <div class="col-md-6">
        <label class="form-label" hidden>
            Estado
        </label>

        <select
            name="estado"
            class="form-control" hidden>
            <option value="1"
                {{ old('estado', $area->estado ?? 1)==1 ? 'selected':'' }} >
                Activo
            </option>

        </select>
    </div>
</div>

<br>
<div class="row">
    {{-- Nombre --}}
    <div class="col-md-12">
        <label class="form-label"> Nombre del Área
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            id="nombre"
            name="nombre"
            maxlength="100"
            autocomplete="off"
            value="{{ old('nombre', $area->nombre ?? '') }}"
            class="form-control mayusculas @error('nombre') is-invalid @enderror"
            placeholder="Ejemplo: VENTAS">
        <small id="mensajeNombre"></small>
        @error('nombre')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<br>
<div class="row">
    {{-- Descripción --}}
    <div class="col-md-12">
        <label class="form-label"> Descripción  </label>
        <textarea
            name="descripcion"
            rows="4"
            class="form-control mayusculas"
            placeholder="Ingrese una descripción del área...">{{ old('descripcion', $area->descripcion ?? '') }}</textarea>
    </div>
</div>

<hr>
<div class="text-end">
    <button type="submit" class="btn btn-primary"> <i class="fas fa-save"></i> Guardar </button>

    <a href="{{ route('areas.index') }}" class="btn btn-secondary"><i class="fas fa-times-circle"></i> Cancelar </a>
</div>