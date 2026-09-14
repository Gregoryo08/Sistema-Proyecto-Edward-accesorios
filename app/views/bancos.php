<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalBancos.php'); ?>

<main class="main m-4" id="main">
    <section id="hero" class="hero section m-2" style="height: auto;">
        
        <div>
            <h2 class="text-center">Administrar Bancos</h2>

            <div class="text-end m-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalBancos">
                    <i class="bi bi-bank"></i> Registrar banco
                </button>

            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <input type="hidden" id="btn_delete">

                    <table id="tablaBancos" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre del Banco</th>
                                <th>Telefono</th>
                                <th>Cedula / RIF</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                </td>
                                <td></td>
                                <td>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

</main>
<style>
   
select.dt-input, 
input.dt-input {
    background-color: #ffffff !important;
    color: #212529 !important;
    border: 1px solid #ced4da !important;
    padding: 4px 8px !important;
}


[data-bs-theme="dark"] select.dt-input, 
[data-bs-theme="dark"] input.dt-input,
body.dark-mode select.dt-input, 
body.dark-mode input.dt-input,
.dark select.dt-input {
    background-color: #212529 !important;
    color: #ffffff !important;
    border-color: #495057 !important;
}
select.dt-input option {
    background-color: #ffffff !important;
    color: #212529 !important;
}


[data-bs-theme="dark"] select.dt-input option,
body.dark-mode select.dt-input option,
.dark select.dt-input option {
    background-color: #212529 !important;
    color: #ffffff !important;
}
</style>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<script src="assets/js/validaciones/bancos/bancos.js"></script>
<script src="assets/js/validaciones/bancos/bancos2.js"></script>

</body>

</html>