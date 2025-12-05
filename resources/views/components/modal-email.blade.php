@props([
    'correoApelaciones',   // correo destino
    'periodos' => [],      // lista de periodos
    'action'   => null,    // ruta para el POST
])

<div>
    {{-- Overlay del modal --}}
    <div
        id="modal-redactar-overlay"
        style="display: none;
               position: fixed;
               top: 0; left: 0; right: 0; bottom: 0;
               background: rgba(0,0,0,0.5);
               justify-content: center;
               align-items: center;"
    >
        <div
            style="background: #fff;
                   padding: 20px;
                   border-radius: 8px;
                   width: 100%;
                   max-width: 500px;"
        >
            <h2 style="margin-top: 0;">Redactar correo</h2>

            <form id="form-redactar-correo">
                @csrf

                {{-- Destinatario oculto --}}
                <input type="hidden" name="correo_apelaciones" value="{{ $correoApelaciones }}">

                {{-- Periodo --}}
                <div style="margin-bottom: 15px;">
                    <label for="select-periodo" style="display:block; font-weight: 600;">
                        Periodo:
                    </label>
                    <select
                        id="select-periodo"
                        name="periodo"
                        class="form-select"
                        style="width: 100%; padding: 6px;"
                    >
                        <option value="">-- Selecciona un periodo --</option>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo }}">{{ $periodo }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Comentarios --}}
                <div style="margin-bottom: 15px;">
                    <label for="textarea-comentarios" style="display:block; font-weight: 600;">
                        Comentarios:
                    </label>
                    <textarea
                        id="textarea-comentarios"
                        name="comentarios"
                        rows="4"
                        style="width: 100%; padding: 6px; resize: vertical;"
                    ></textarea>
                </div>

                {{-- Botones --}}
                <div style="text-align: right;">
                    <button
                        type="button"
                        id="btn-cancelar-modal"
                        class="btn btn-secondary"
                        style="margin-right: 8px;"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        id="btn-enviar-modal"
                        class="btn btn-success"
                    >
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Alerta personalizada --}}
    <div
        id="alerta-overlay"
        style="display: none;
               position: fixed;
               top: 0; left: 0; right: 0; bottom: 0;
               background: rgba(0,0,0,0.5);
               justify-content: center;
               align-items: center;"
    >
        <div
            style="background: #fff;
                   padding: 20px;
                   border-radius: 8px;
                   width: 100%;
                   max-width: 400px;
                   text-align: center;"
        >
            <p id="alerta-mensaje" style="margin-bottom: 20px;"></p>
            <button
                type="button"
                id="btn-alerta-aceptar"
                class="btn btn-primary"
            >
                Aceptar
            </button>
        </div>
    </div>
</div>
