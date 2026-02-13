<div class="modal fade" id="modal_add_marca" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Nueva Marca</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-1"></i></div>
            </div>
            <div class="modal-body py-10">
                <input type="text" id="input_nueva_marca" class="form-control form-control-solid" placeholder="Nombre de la marca (HP, Dell...)" />
            </div>
            <div class="modal-footer"><button type="button" onclick="guardarDatoRapido('marca')" class="btn btn-primary">Guardar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_add_modelo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Nuevo Modelo</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-1"></i></div>
            </div>
            <div class="modal-body py-10">
                <input type="text" id="input_nuevo_modelo" class="form-control form-control-solid" placeholder="Nombre del modelo (Latitude, ThinkPad...)" />
            </div>
            <div class="modal-footer"><button type="button" onclick="guardarDatoRapido('modelo')" class="btn btn-primary">Guardar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_add_tipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Nuevo Tipo</h2>
            </div>
            <div class="modal-body py-10">
                <input type="text" id="input_nuevo_tipo" class="form-control form-control-solid" placeholder="Ej: Herramienta, Periférico..." />
            </div>
            <div class="modal-footer"><button type="button" onclick="guardarDatoRapido('tipo')" class="btn btn-primary">Guardar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_add_salud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Nuevo Estado</h2>
            </div>
            <div class="modal-body py-10">
                <input type="text" id="input_nueva_salud" class="form-control form-control-solid" placeholder="Ej: Nuevo, En revisión..." />
            </div>
            <div class="modal-footer"><button type="button" onclick="guardarDatoRapido('salud')" class="btn btn-primary">Guardar</button></div>
        </div>
    </div>
</div>