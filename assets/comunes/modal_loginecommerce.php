<div class="modal fade" id="modalLoginEcommerce" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Acceso Clientes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="clienteLoginForm" autocomplete="off">
                    <div class="mb-3">
                        <input type="email" id="email_cliente" name="correo" class="form-control" placeholder="Correo Electrónico">
                    </div>
                    <div class="mb-3">
                        <input type="password" id="pass_cliente" name="clave" class="form-control" placeholder="Contraseña">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    <p class="mt-2 text-center">¿No tienes cuenta? <a href="#" class="register-link">Regístrate</a></p>
                </form>
                <hr>
                <form id="clienteRegistroForm" autocomplete="off" style="display:none;">
                    <div class="mb-3">
                        <input type="text" name="cedula" class="form-control" placeholder="Cédula">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="nombre_apellido" class="form-control" placeholder="Nombre y Apellido">
                    </div>
                    <div class="mb-3">
                        <input type="email" name="correo" class="form-control" placeholder="Correo Electrónico">
                    </div>
                    <div class="mb-3">
                        <input type="tel" name="telefono" class="form-control" placeholder="Teléfono">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="clave" class="form-control" placeholder="Contraseña">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrarse</button>
                    <p class="mt-2 text-center"><a href="#" class="login-link">Ya tengo cuenta</a></p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
