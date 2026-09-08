<div class="card shadow mt-3">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>💱 Tasa de Cambio (USD → Bs.)</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <h3 class="mb-0">Bs. <span id="tasa-actual"><?= number_format($tasa, 2) ?></span></h3>
                <small class="text-muted" id="tasa-fecha"><?= $fecha ?></small><br>
                <small class="text-muted">Fuente: <span id="tasa-fuente"><?= htmlspecialchars($fuente ?? 'N/A') ?></span></small>
            </div>
            <div class="col-md-8">
                <div id="tasa-alert" class="alert <?= $vigencia['expirada'] ? 'alert-danger' : 'alert-success' ?> py-2 mb-2">
                    <i class="fas <?= $vigencia['expirada'] ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-1"></i>
                    <?= $vigencia['mensaje'] ?>
                </div>
                <div class="input-group">
                    <input type="number" id="nueva-tasa" class="form-control" step="0.01" min="0.01"
                           placeholder="Nueva tasa (Bs./USD)" value="<?= $tasa ?>" disabled>
                    <button class="btn btn-primary" id="btn-tasa" onclick="clickBotonTasa()">
                        <i class="fas fa-lock"></i> Actualizar
                    </button>
                    <button class="btn btn-secondary" id="btn-bcv" onclick="scrapearBCV()" disabled>
                        <i class="fas fa-globe"></i> BCV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var tasaEditMode = false;

function clickBotonTasa() {
    var input = document.getElementById('nueva-tasa');
    var btn = document.getElementById('btn-tasa');
    var btnBcv = document.getElementById('btn-bcv');

    if (!tasaEditMode) {
        Swal.fire({
            title: 'Clave de administrador',
            input: 'password',
            inputPlaceholder: 'Ingrese su clave',
            showCancelButton: true,
            confirmButtonText: 'Verificar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => !value ? 'Debe ingresar la clave' : null
        }).then(result => {
            if (!result.isConfirmed) return;
            fetch('?pagina=tasaCambio&action=verificarClave', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'clave=' + encodeURIComponent(result.value)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    tasaEditMode = true;
                    input.disabled = false;
                    input.focus();
                    btn.innerHTML = '<i class="fas fa-check"></i> Aceptar';
                    btn.className = 'btn btn-success';
                    btnBcv.disabled = false;
                    Swal.fire('Verificado', 'Clave correcta. Ingrese la nueva tasa.', 'success');
                } else {
                    Swal.fire('Error', 'Clave incorrecta', 'error');
                }
            });
        });
    } else {
        const tasa = input.value;
        if (!tasa || parseFloat(tasa) <= 0) {
            Swal.fire('Error', 'Ingrese una tasa válida', 'error');
            return;
        }
        fetch('?pagina=tasaCambio&action=actualizar', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'tasa=' + encodeURIComponent(tasa)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('tasa-actual').textContent = parseFloat(data.tasa).toFixed(2);
                document.getElementById('tasa-fecha').textContent = data.fecha;
                Swal.fire('Actualizada', data.message, 'success');
            } else {
                if (data.reauth) {
                    tasaEditMode = false;
                    input.disabled = true;
                    btn.innerHTML = '<i class="fas fa-lock"></i> Actualizar';
                    btn.className = 'btn btn-primary';
                    btnBcv.disabled = true;
                    Swal.fire('Sesión expirada', data.message, 'warning');
                    return;
                }
                Swal.fire('Error', data.message, 'error');
                return;
            }
        })
        .catch(function() {
            Swal.fire('Error', 'Error de conexión', 'error');
        })
        .then(function() {
            tasaEditMode = false;
            input.disabled = true;
            btn.innerHTML = '<i class="fas fa-lock"></i> Actualizar';
            btn.className = 'btn btn-primary';
            btnBcv.disabled = true;
        });
    }
}

function scrapearBCV() {
    Swal.fire({
        title: 'Obteniendo tasa del BCV...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    fetch('?pagina=tasaCambio&action=scrapear')
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('tasa-actual').textContent = parseFloat(data.tasa).toFixed(2);
            document.getElementById('nueva-tasa').value = data.tasa;
            document.getElementById('tasa-fecha').textContent = data.fecha;
            Swal.fire('BCV Actualizado', data.message, 'success');
        } else {
            if (data.reauth) {
                Swal.fire('Sesión expirada', 'Debe verificar su clave primero', 'warning');
                return;
            }
            Swal.fire('Error', data.message, 'error');
        }
    })
    .then(function() {
        var input = document.getElementById('nueva-tasa');
        var btn = document.getElementById('btn-tasa');
        var btnBcv = document.getElementById('btn-bcv');
        tasaEditMode = false;
        input.disabled = true;
        btn.innerHTML = '<i class="fas fa-lock"></i> Actualizar';
        btn.className = 'btn btn-primary';
        btnBcv.disabled = true;
    });
}
</script>
