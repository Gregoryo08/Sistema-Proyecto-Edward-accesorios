<div class="modal fade modal-detalles" id="modalbitacora" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Accion del Usuario</h5>
            </div>
            <div class="modal-body">

                <div><p><strong>Tabla: </strong> <span id="modalTabla"></span></p></div>

                <div><p><strong>Módulo: </strong> <span id="modalModulo"></span></p></div>

                <div><p><strong>Fecha: </strong> <span id="modalFecha"></span></p> </div>

                <div id=operacionNueva>
                    <p><strong>Datos Ingresados Nuevos:</strong></p>
                    <pre id="modalOperacionNueva" style="white-space: pre-wrap; font-family: monospace; background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;"></pre>
                </div>

                <div id=operacionAntigua>
                    <p><strong>Datos Antiguos:</strong></p>
                    <pre id="modalOperacionAntigua" style="white-space: pre-wrap; font-family: monospace; background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;"></pre>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrarDetalles" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
