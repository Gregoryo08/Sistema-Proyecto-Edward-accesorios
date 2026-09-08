<div class="container_habitacion" id="container_clientes">
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
                            <th>Sexo</th>
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
    .container_habitacion {
    position: fixed;
    top: 0;
    left: -100%; /* Inicia oculto a la izquierda */
    width: 100%;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 1050; /* Por encima de todo */
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.5s ease-in-out;
    opacity: 0;
    visibility: hidden;
}

.container_habitacion.active {
    left: 0;
    opacity: 1;
    visibility: visible;
}

.container_cont {
    width: 90%;
    max-width: 1100px;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    max-height: 90vh;
    overflow-y: auto;
}
</style>