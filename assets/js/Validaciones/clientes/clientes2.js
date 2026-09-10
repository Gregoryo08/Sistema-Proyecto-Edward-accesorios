$(document).ready(function () {

  function mostrarValidacionSimple(selector, mensajeId, mensaje) {
    const $campo = $(selector);
    const $mensaje = $(mensajeId);

    $campo.css({
      border: '1px solid #ff4d4d',
      'box-shadow': '0 0 4px rgba(255, 77, 77, 0.4)'
    }).removeClass('is-valid').addClass('is-invalid');

    if ($mensaje.length) {
      $mensaje.css({
        display: 'block',
        color: '#ff4d4d',
        'font-size': '13px',
        'font-weight': '600',
        'margin-top': '4px',
        'text-align': 'left'
      }).text(mensaje);
    }
  }

  function limpiarValidacionSimple(selector, mensajeId) {
    const $campo = $(selector);
    const $mensaje = $(mensajeId);

    $campo.css({
      border: '1px solid #cfd4da',
      'box-shadow': 'none'
    }).removeClass('is-invalid is-valid');

    if ($mensaje.length) {
      $mensaje.css({ display: 'none' }).text('');
    }
  }

  function esMayorDeEdad(fechaStr) {
    const hoy = new Date();
    const fechaNac = new Date(fechaStr + 'T00:00:00');

    if (isNaN(fechaNac.getTime())) return false;

    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();

    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
      edad--;
    }

    return {
      esMayor: edad >= 18,
      esFutura: fechaNac > hoy
    };
  }

  function bloquearNumerosEnTexto(selector, mensajeSelector) {
    $(selector).on('keydown keypress', function (event) {
      const tecla = event.key;
      const codigo = event.which || event.keyCode;

      if ((event.type === 'keydown' && tecla.length === 1 && /[0-9]/.test(tecla)) ||
          (event.type === 'keypress' && (codigo >= 48 && codigo <= 57))) {
        event.preventDefault();
        mostrarValidacionSimple(selector, mensajeSelector, 'Este campo no acepta números.');
        return false;
      }
    });
  }

  function bloquearLetrasEnNumeros(selector, mensajeSelector, permiteDecimales = false) {
    $(selector).on('keydown keypress', function (event) {
      const tecla = event.key;
      const codigo = event.which || event.keyCode;

      if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(tecla)) {
        return true;
      }

      if (permiteDecimales && (tecla === '.' || tecla === ',')) {
        if (this.value.includes('.') || this.value.includes(',')) {
          event.preventDefault();
          return false;
        }
        return true;
      }

      if ((event.type === 'keydown' && tecla.length === 1 && !/[0-9]/.test(tecla)) ||
          (event.type === 'keypress' && (codigo < 48 || codigo > 57))) {
        event.preventDefault();
        mostrarValidacionSimple(selector, mensajeSelector, 'Este campo no acepta letras.');
        return false;
      }
    });
  }

  function configurarTecladoCedula(selector, mensajeSelector) {
    $(selector).on("keydown keypress", function (event) {
      const tecla = event.key;
      const codigo = event.which || event.keyCode;

      if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(tecla)) return true;

      if ((event.type === "keydown" && tecla.length === 1 && !/[0-9]/.test(tecla)) ||
          (event.type === "keypress" && (codigo < 48 || codigo > 57 || this.value.length >= 9))) {
        event.preventDefault();
        mostrarValidacionSimple(selector, mensajeSelector, 'Este campo no acepta letras.');
        return false;
      }
    });
  }

  function validarDatosCliente() {
    var cedula = $("#cedula").val();
    var prefijo = $("#prefijo").val();
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var fecha = $("#fecha_nacimiento").val();
    var sexo = $("#sexo").val();
    var correo = $("#correo").val();
    var operadora = $("#operadora").val();
    var telefono = $("#telefono").val();
    var direccion = $("#direccion").val();

    var tipo_residencia = $("#tipo_residencia").val();
    var estado_civil = $("#estado_civil").val();
    var profesion = $("#profesion").val();
    var carga_familiar = $("#carga_familiar").val();
    var ocupacion = $("#ocupacion").val();
    var ingreso_bs = $("#ingreso_bs").val();

    var valido = true;

    if (!prefijo) {
      mostrarValidacionSimple('#prefijo', '#texto_mensaje_cedula', 'Debes seleccionar el prefijo.');
      valido = false;
    } else {
      limpiarValidacionSimple('#prefijo', '#texto_mensaje_cedula');
    }

    if (!cedula || !/^[0-9]{7,9}$/.test(cedula)) {
      mostrarValidacionSimple('#cedula', '#texto_mensaje_cedula', 'La cédula debe tener entre 7 y 9 dígitos.');
      valido = false;
    } else if (prefijo) {
      limpiarValidacionSimple('#cedula', '#texto_mensaje_cedula');
    }

    if (!nombre || !nombre.trim()) {
      mostrarValidacionSimple('#nombre', '#texto_mensaje_nombre', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre.trim())) {
      mostrarValidacionSimple('#nombre', '#texto_mensaje_nombre', 'Este campo no acepta números.');
      valido = false;
    } else {
      limpiarValidacionSimple('#nombre', '#texto_mensaje_nombre');
    }

    if (!apellido || !apellido.trim()) {
      mostrarValidacionSimple('#apellido', '#texto_mensaje_apellido', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(apellido.trim())) {
      mostrarValidacionSimple('#apellido', '#texto_mensaje_apellido', 'Este campo no acepta números.');
      valido = false;
    } else {
      limpiarValidacionSimple('#apellido', '#texto_mensaje_apellido');
    }

    if (!fecha) {
      mostrarValidacionSimple('#fecha_nacimiento', '#texto_mensaje_fecha_nacimiento', 'Debes seleccionar la fecha de nacimiento.');
      valido = false;
    } else {
      const validacionEdad = esMayorDeEdad(fecha);
      if (validacionEdad.esFutura) {
        mostrarValidacionSimple('#fecha_nacimiento', '#texto_mensaje_fecha_nacimiento', 'La fecha introducida no puede ser futura.');
        valido = false;
      } else if (!validacionEdad.esMayor) {
        mostrarValidacionSimple('#fecha_nacimiento', '#texto_mensaje_fecha_nacimiento', 'Debe ser mayor de 18 años.');
        valido = false;
      } else {
        limpiarValidacionSimple('#fecha_nacimiento', '#texto_mensaje_fecha_nacimiento');
      }
    }

    if (!sexo) {
      mostrarValidacionSimple('#sexo', '#texto_mensaje_sexo', 'Debes seleccionar el sexo.');
      valido = false;
    } else {
      limpiarValidacionSimple('#sexo', '#texto_mensaje_sexo');
    }

    if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      mostrarValidacionSimple('#correo', '#texto_mensaje_correo', 'Ingrese un correo válido, ejemplo: correo@gmail.com');
      valido = false;
    } else {
      limpiarValidacionSimple('#correo', '#texto_mensaje_correo');
    }

    if (!operadora) {
      mostrarValidacionSimple('#operadora', '#texto_mensaje_telefono', 'Debes seleccionar una operadora.');
      valido = false;
    } else {
      limpiarValidacionSimple('#operadora', '#texto_mensaje_telefono');
    }

    if (!telefono || !telefono.trim()) {
      mostrarValidacionSimple('#telefono', '#texto_mensaje_telefono', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[0-9]{6,7}$/.test(telefono)) {
      mostrarValidacionSimple('#telefono', '#texto_mensaje_telefono', 'Este campo debe contener entre 6 y 7 dígitos.');
      valido = false;
    } else if (operadora) {
      limpiarValidacionSimple('#telefono', '#texto_mensaje_telefono');
    }

    if (!direccion || direccion.trim().length < 10) {
      mostrarValidacionSimple('#direccion', '#texto_mensaje_direccion', 'La dirección debe tener al menos 10 caracteres.');
      valido = false;
    } else {
      limpiarValidacionSimple('#direccion', '#texto_mensaje_direccion');
    }

    if (!tipo_residencia) {
      mostrarValidacionSimple('#tipo_residencia', '#texto_mensaje_tipo_residencia', 'Seleccione una opción');
      valido = false;
    } else {
      limpiarValidacionSimple('#tipo_residencia', '#texto_mensaje_tipo_residencia');
    }

    if (!estado_civil) {
      mostrarValidacionSimple('#estado_civil', '#texto_mensaje_estado_civil', 'Seleccione una opción');
      valido = false;
    } else {
      limpiarValidacionSimple('#estado_civil', '#texto_mensaje_estado_civil');
    }

    if (!profesion) {
      mostrarValidacionSimple('#profesion', '#texto_mensaje_profesion', 'Seleccione una opción');
      valido = false;
    } else {
      limpiarValidacionSimple('#profesion', '#texto_mensaje_profesion');
    }

    if (carga_familiar === "" || carga_familiar === null) {
      mostrarValidacionSimple('#carga_familiar', '#texto_mensaje_carga_familiar', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[0-9]+$/.test(carga_familiar.toString().trim())) {
      mostrarValidacionSimple('#carga_familiar', '#texto_mensaje_carga_familiar', 'Este campo no acepta letras.');
      valido = false;
    } else {
      limpiarValidacionSimple('#carga_familiar', '#texto_mensaje_carga_familiar');
    }

    if (!ocupacion || !ocupacion.trim()) {
      mostrarValidacionSimple('#ocupacion', '#texto_mensaje_ocupacion', 'Este campo no puede estar vacío.');
      valido = false;
    } else {
      limpiarValidacionSimple('#ocupacion', '#texto_mensaje_ocupacion');
    }

    if (!ingreso_bs || !ingreso_bs.trim()) {
      mostrarValidacionSimple('#ingreso_bs', '#texto_mensaje_ingresos', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[0-9]+([.,][0-9]+)?$/.test(ingreso_bs.trim())) {
      mostrarValidacionSimple('#ingreso_bs', '#texto_mensaje_ingresos', 'Este campo no acepta letras.');
      valido = false;
    } else {
      limpiarValidacionSimple('#ingreso_bs', '#texto_mensaje_ingresos');
    }

    return valido;
  }

  bloquearNumerosEnTexto('#nombre', '#texto_mensaje_nombre');
  bloquearNumerosEnTexto('#apellido', '#texto_mensaje_apellido');
  bloquearLetrasEnNumeros('#telefono', '#texto_mensaje_telefono');
  bloquearLetrasEnNumeros('#carga_familiar', '#texto_mensaje_carga_familiar');
  bloquearLetrasEnNumeros('#ingreso_bs', '#texto_mensaje_ingresos', true);
  configurarTecladoCedula('#cedula', '#texto_mensaje_cedula');

  $("#ingreso_bs, #tasa_bcv").on("input", function() {
    let bs = parseFloat($("#ingreso_bs").val()) || 0;
    let tasa = parseFloat($("#tasa_bcv").val()) || 1;
    let usd = bs / tasa;
    $("#calc_usd").text(usd.toFixed(2));
    $("#ingresos_mensuales").val(usd.toFixed(2));
  });

  $("#prefijo, #cedula, #nombre, #apellido, #fecha_nacimiento, #sexo, #correo, #operadora, #telefono, #direccion, #tipo_residencia, #estado_civil, #profesion, #carga_familiar, #ocupacion, #ingreso_bs")
    .on("input change blur", function () {
      validarDatosCliente();
    });

  $("#guardarCliente").on("click", function (event) {
    event.preventDefault();
    if (validarDatosCliente()) {
      console.log("Registro de cliente exitoso. Proceder con AJAX.");
    }
  });

  function validarModificaciónCliente() {
    var nombre = $("#nombreModificar").val();
    var apellido = $("#apellidoModificar").val();
    var sexo = $("#sexoModificar").val();
    var correo = $("#correoModificar").val();
    var operadora = $("#operadoraModificar").val();
    var telefono = $("#telefonoModificar").val();
    var tipo_residencia = $("#tipo_residenciaModificar").val();
    var estado_civil = $("#estado_civilModificar").val();
    var profesion = $("#profesionModificar").val();
    var carga_familiar = $("#carga_familiarModificar").val();
    var ocupacion = $("#ocupacionModificar").val();
    var ingresos = $("#ingresosModificar").val();
    var direccion = $("#direccionModificar").val();

    var valido = true;

    if (!nombre || !nombre.trim()) {
      mostrarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre.trim())) {
      mostrarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar', 'Este campo no acepta números.');
      valido = false;
    } else {
      limpiarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar');
    }

    if (!apellido || !apellido.trim()) {
      mostrarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(apellido.trim())) {
      mostrarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar', 'Este campo no acepta números.');
      valido = false;
    } else {
      limpiarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar');
    }

    if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      mostrarValidacionSimple('#correoModificar', '#texto_mensaje_correo_modificar', 'Ingrese un correo válido.');
      valido = false;
    } else {
      limpiarValidacionSimple('#correoModificar', '#texto_mensaje_correo_modificar');
    }

    if (!sexo) {
      mostrarValidacionSimple('#sexoModificar', '#texto_mensaje_sexo_modificar', 'Debes seleccionar el sexo.');
      valido = false;
    } else {
      limpiarValidacionSimple('#sexoModificar', '#texto_mensaje_sexo_modificar');
    }

    if (!operadora) {
      mostrarValidacionSimple('#operadoraModificar', '#texto_mensaje_telefono_modificar', 'Seleccione una operadora.');
      valido = false;
    } else {
      limpiarValidacionSimple('#operadoraModificar', '#texto_mensaje_telefono_modificar');
    }

    if (!telefono || !telefono.trim()) {
      mostrarValidacionSimple('#telefonoModificar', '#texto_mensaje_telefono_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[0-9]{6,7}$/.test(telefono)) {
      mostrarValidacionSimple('#telefonoModificar', '#texto_mensaje_telefono_modificar', 'Ingrese entre 6 y 7 dígitos.');
      valido = false;
    } else if (operadora) {
      limpiarValidacionSimple('#telefonoModificar', '#texto_mensaje_telefono_modificar');
    }

    if (!tipo_residencia) {
      mostrarValidacionSimple('#tipo_residenciaModificar', '#texto_mensaje_tipo_residencia_modificar', 'Seleccione una opción.');
      valido = false;
    } else {
      limpiarValidacionSimple('#tipo_residenciaModificar', '#texto_mensaje_tipo_residencia_modificar');
    }

    if (!estado_civil) {
      mostrarValidacionSimple('#estado_civilModificar', '#texto_mensaje_estado_civil_modificar', 'Seleccione una opción.');
      valido = false;
    } else {
      limpiarValidacionSimple('#estado_civilModificar', '#texto_mensaje_estado_civil_modificar');
    }

    if (!profesion) {
      mostrarValidacionSimple('#profesionModificar', '#texto_mensaje_cargo_modificar', 'Seleccione una opción.');
      valido = false;
    } else {
      limpiarValidacionSimple('#profesionModificar', '#texto_mensaje_cargo_modificar');
    }

    if (carga_familiar === "" || carga_familiar === null) {
      mostrarValidacionSimple('#carga_familiarModificar', '#texto_mensaje_carga_familiar_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[0-9]+$/.test(carga_familiar.toString().trim())) {
      mostrarValidacionSimple('#carga_familiarModificar', '#texto_mensaje_carga_familiar_modificar', 'Este campo no acepta letras.');
      valido = false;
    } else {
      limpiarValidacionSimple('#carga_familiarModificar', '#texto_mensaje_carga_familiar_modificar');
    }

    if (!ocupacion || !ocupacion.trim()) {
      mostrarValidacionSimple('#ocupacionModificar', '#texto_mensaje_ocupacion_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    } else {
      limpiarValidacionSimple('#ocupacionModificar', '#texto_mensaje_ocupacion_modificar');
    }

    if (!ingresos || parseFloat(ingresos) <= 0) {
      mostrarValidacionSimple('#ingresosModificar', '#texto_mensaje_ingresos_modificar', 'Ingrese un valor de ingresos válido.');
      valido = false;
    } else {
      limpiarValidacionSimple('#ingresosModificar', '#texto_mensaje_ingresos_modificar');
    }

    if (!direccion || direccion.trim().length < 10) {
      mostrarValidacionSimple('#direccionModificar', '#texto_mensaje_direccion_modificar', 'La dirección debe tener al menos 10 caracteres.');
      valido = false;
    } else {
      limpiarValidacionSimple('#direccionModificar', '#texto_mensaje_direccion_modificar');
    }

    return valido;
  }

  bloquearNumerosEnTexto('#nombreModificar', '#texto_mensaje_nombre_modificar');
  bloquearNumerosEnTexto('#apellidoModificar', '#texto_mensaje_apellido_modificar');
  bloquearLetrasEnNumeros('#telefonoModificar', '#texto_mensaje_telefono_modificar');
  bloquearLetrasEnNumeros('#carga_familiarModificar', '#texto_mensaje_carga_familiar_modificar');
  bloquearLetrasEnNumeros('#ingresosModificar', '#texto_mensaje_ingresos_modificar', true);

  $("#nombreModificar, #apellidoModificar, #correoModificar, #sexoModificar, #operadoraModificar, #telefonoModificar, #tipo_residenciaModificar, #estado_civilModificar, #profesionModificar, #carga_familiarModificar, #ocupacionModificar, #ingresosModificar, #direccionModificar")
    .on("input change blur", function () {
      validarModificaciónCliente();
    });

  $("#modificarDatos").on("click", function (event) {
    event.preventDefault();
    if (validarModificaciónCliente()) {
      console.log("Modificación del cliente válida. Procesar datos.");
    }
  });

  function validarPerfilFinanciero() {
    var cedula = $("#cedulaPerfil").val();
    var tipo_residencia = $("#tipo_residenciaPerfil").val();
    var estado_civil = $("#estado_civilPerfil").val();
    var profesion = $("#profesionPerfil").val();
    var carga_familiar = $("#carga_familiarPerfil").val();
    var ocupacion = $("#ocupacionPerfil").val();
    var ingreso_bs = $("#ingreso_bs_perfil").val();

    var valido = true;

    if (!cedula || !cedula.trim()) {
      mostrarValidacionSimple('#cedulaPerfilVisual', '#texto_mensaje_cedula_perfil', 'Debe estar asociado a una cédula.');
      valido = false;
    } else {
      limpiarValidacionSimple('#cedulaPerfilVisual', '#texto_mensaje_cedula_perfil');
    }

    if (!tipo_residencia) {
      mostrarValidacionSimple('#tipo_residenciaPerfil', '#texto_mensaje_tipo_residencia_perfil', 'Seleccione una opción.');
      valido = false;
    } else {
      limpiarValidacionSimple('#tipo_residenciaPerfil', '#texto_mensaje_tipo_residencia_perfil');
    }

    if (!estado_civil) {
      mostrarValidacionSimple('#estado_civilPerfil', '#texto_mensaje_estado_civil_perfil', 'Seleccione una opción.');
      valido = false;
    } else {
      limpiarValidacionSimple('#estado_civilPerfil', '#texto_mensaje_estado_civil_perfil');
    }

    if (!profesion) {
      mostrarValidacionSimple('#profesionPerfil', '#texto_mensaje_profesion_perfil', 'Seleccione una opción.');
      valido = false;
    } else {
      limpiarValidacionSimple('#profesionPerfil', '#texto_mensaje_profesion_perfil');
    }

    if (carga_familiar === "" || carga_familiar === null) {
      mostrarValidacionSimple('#carga_familiarPerfil', '#texto_mensaje_carga_familiar_perfil', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[0-9]+$/.test(carga_familiar.toString().trim())) {
      mostrarValidacionSimple('#carga_familiarPerfil', '#texto_mensaje_carga_familiar_perfil', 'Este campo no acepta letras.');
      valido = false;
    } else {
      limpiarValidacionSimple('#carga_familiarPerfil', '#texto_mensaje_carga_familiar_perfil');
    }

    if (!ocupacion || !ocupacion.trim()) {
      mostrarValidacionSimple('#ocupacionPerfil', '#texto_mensaje_ocupacion_perfil', 'Este campo no puede estar vacío.');
      valido = false;
    } else {
      limpiarValidacionSimple('#ocupacionPerfil', '#texto_mensaje_ocupacion_perfil');
    }

    if (!ingreso_bs || !ingreso_bs.trim()) {
      mostrarValidacionSimple('#ingreso_bs_perfil', '#texto_mensaje_ingresos_perfil', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (!/^[0-9]+([.,][0-9]+)?$/.test(ingreso_bs.trim())) {
      mostrarValidacionSimple('#ingreso_bs_perfil', '#texto_mensaje_ingresos_perfil', 'Este campo no acepta letras.');
      valido = false;
    } else {
      limpiarValidacionSimple('#ingreso_bs_perfil', '#texto_mensaje_ingresos_perfil');
    }

    return valido;
  }

  bloquearLetrasEnNumeros('#carga_familiarPerfil', '#texto_mensaje_carga_familiar_perfil');
  bloquearLetrasEnNumeros('#ingreso_bs_perfil', '#texto_mensaje_ingresos_perfil', true);

  $("#ingreso_bs_perfil").on("input", function() {
    let bs = parseFloat($(this).val()) || 0;
    let tasa = parseFloat($("#tasa_bcv_perfil").val()) || 60.00; 
    $("#calc_usd_perfil").text(usd.toFixed(2));
    $("#modalRegistroPerfilFinanciero #ingresos_mensuales").val(usd.toFixed(2));
  });

  $("#tipo_residenciaPerfil, #estado_civilPerfil, #profesionPerfil, #carga_familiarPerfil, #ocupacionPerfil, #ingreso_bs_perfil")
    .on("input change blur", function () {
      validarPerfilFinanciero();
    });

  $("#guardarPerfilFinanciero").on("click", function (event) {
    event.preventDefault();
    if (validarPerfilFinanciero()) {
      console.log("Perfil financiero válido. Enviando datos.");
    }
  });

});