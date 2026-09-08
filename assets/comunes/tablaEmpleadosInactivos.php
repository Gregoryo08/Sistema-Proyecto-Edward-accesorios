<div class="container" id="container_empleados">
    <div class="container_cont">
        <div class="text-right mb-3 container_buttons">
            <button type="button" class="btn btn-danger" id="btn_salir">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="table-responsive">
            <div class="table-container" style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); position: relative;">
                
                <button type="button" class="btn-close btn-cerrar-inactivos" aria-label="Close" style="position: absolute; top: 10px; right: 10px;"></button>
                
                <table id="tablaInactivos" class="table table-responsive table-striped table-bordered text-center" style="margin-top: 20px;">
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

<script>
    

$(document).on("click", "#btn_verInactivos", function() {
    $("#container_empleados").fadeIn();
    // Solo inicializar la tabla cuando el contenedor ya es visible
        cargarTablaInactivos();  
});


$(document).on("click", ".btn-cerrar-inactivos, #btn_salir", function() {
    $("#container_empleados").fadeOut();
});

$(document).on("click", "#container_empleados", function(e) {
    if (e.target.id === "container_empleados") {
        $(this).fadeOut();
    }
});
</script>
<style>
    #container_empleados {
    display: none; /* Inicia oculto */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999; /* Asegúrate de que sea muy alto */
    padding: 50px 20px;
    overflow-y: auto;
}

    .container_cont {
        max-width: 900px;
        margin: 0 auto;
    }
</style>