<div class="container_habitacion" id="container_empleados">
    <div class="container_cont">
        <div class="text-right mb-3 container_buttons">

            <button type="button" class="btn btn-danger" id="btn_salir">
                <i class="bi bi-x-lg"></i>
            </button>

        </div>

        <div class="table-responsive">
            <div class="table-container" style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

                <table id="tablaInactivos" class="table table-responsive table-striped table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cedula</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<style>
    #container_empleados {
    display: none; /* Oculto por defecto */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7); /* Fondo oscuro semitransparente */
    z-index: 1050; /* Por encima del menú y otras tablas */
    padding: 50px 20px;
    overflow-y: auto;
}

.container_cont {
    max-width: 900px;
    margin: 0 auto;
}
</style>