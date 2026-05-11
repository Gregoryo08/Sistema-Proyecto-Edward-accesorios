<div class="container_habitacion" id="container_empleados" style="display: none;"> 
    <div class="container_cont">
        <div class="text-right mb-3 container_buttons">
            <button type="button" class="btn btn-danger" id="btn_salir">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="table-responsive">
            <div class="table-container" style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <table id="tablaInactivosPerfil" class="table table-striped table-bordered text-center" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cedula</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>#container_empleados {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    overflow-y: auto; /* Permite scroll si la tabla es muy larga */
    padding: 20px 0; /* Espaciado arriba y abajo */
}

.container_cont {
    max-width: 90%;
    margin: 20px auto; /* Centrado con margen */
    background: white;
    padding: 20px;
    border-radius: 10px;
    position: relative; /* Por si necesitas posicionar algo dentro */
}
</style>