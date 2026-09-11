<?php
require_once('assets/comunes/menu.php');
require_once('assets/comunes/modal_servicio_tecnico.php');
?>

<main class="main m-4" id="main">

    <section id="hero" class="hero section m-2" style="height: auto;">
        <div>
            <h2 class="text-center"> Administrar Servicios Técnicos</h2>

            <div class="text-end mb-4">

                <button type="button" id="btnRegistrarServicio" class="btn btn-success rounded-pill px-4" style="display:none;" data-bs-toggle="modal" data-bs-target="#modalRegistrarServicio">
                    <i class="fas fa-pen"></i>&nbsp;&nbsp; Registrar Servicio
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="servicioTabla" class="table table-striped table-bordered text-center align-middle w-100">
                        <thead class="thead-dark align-middle">
                            <tr>
                                <th style="display: none;">ID</th>
                                <th class="text-center">Cédula</th>
                                <th class="text-center">Cliente</th> 
                                <th class="text-center">Equipo</th>
                                <th class="text-center">Falla</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>


<script src="assets/js/validaciones/servicio/servicio.js"></script>
<script src="assets/js/validaciones/servicio/servicio2.js"></script>

</body>

</html>