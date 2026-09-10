$(document).ready(function () {
  function mostrarValidacionSimple(selector, mensajeId, mensaje) {
    const $campo = $(selector);
    const $mensaje = $(mensajeId);

    $campo.css({
      border: '1px solid #cfd4da',
      'box-shadow': 'none'
    }).removeClass('is-invalid is-valid');

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

  // Helper para validar si la fecha corresponde a un mayor de 18 años
  function esMayorDeEdad(fechaStr) {
    const hoy = new Date();
    const fechaNac = new Date(fechaStr + 'T00:00:00'); // Evita desfasajes por zona horaria
    
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

      if (event.type === 'keydown' && tecla.length === 1 && /[0-9]/.test(tecla)) {
        event.preventDefault();
        mostrarValidacionSimple(selector, mensajeSelector, 'Este campo no acepta números.');
        return false;
      }

      if (event.type === 'keypress' && (codigo >= 48 && codigo <= 57)) {
        event.preventDefault();
        mostrarValidacionSimple(selector, mensajeSelector, 'Este campo no acepta números.');
        return false;
      }
    });
  }

  function bloquearLetrasEnTelefono(selector, mensajeSelector) {
    $(selector).on('keydown keypress', function (event) {
      const tecla = event.key;
      const codigo = event.which || event.keyCode;

      if (event.type === 'keydown' && tecla.length === 1 && !/[0-9]/.test(tecla)) {
        event.preventDefault();
        mostrarValidacionSimple(selector, mensajeSelector, 'Este campo no acepta letras.');
        return false;
      }

      if (event.type === 'keypress' && (codigo < 48 || codigo > 57)) {
        event.preventDefault();
        mostrarValidacionSimple(selector, mensajeSelector, 'Este campo no acepta letras.');
        return false;
      }
    });
  }

  function validarDatosEmpleado() {
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
    var cargo = $("#cargo").val();

    var valido = true;

    if (!cargo) {
      mostrarValidacionSimple('#cargo', '#texto_mensaje_cargo', 'Debes seleccionar un cargo.');
      valido = false;
    } else {
      limpiarValidacionSimple('#cargo', '#texto_mensaje_cargo');
    }

    if (!prefijo) {
      mostrarValidacionSimple('#prefijo', '#texto_mensaje_cedula', 'Debes seleccionar el prefijo.');
      valido = false;
    } else {
      limpiarValidacionSimple('#prefijo', '#texto_mensaje_cedula');
    }

    if (!cedula || !/^[0-9]{7,9}$/.test(cedula)) {
      mostrarValidacionSimple('#cedula', '#texto_mensaje_cedula', 'La cédula debe tener entre 7 y 9 dígitos.');
      valido = false;
    } else {
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

    // Validación de Fecha de Nacimiento (Edad mínima: 18 años)
    if (!fecha) {
      mostrarValidacionSimple('#fecha_nacimiento', '#texto_mensaje_fecha_nacimiento', 'Debes seleccionar la fecha de nacimiento.');
      valido = false;
    } else {
      const validacionEdad = esMayorDeEdad(fecha);
      if (validacionEdad.esFutura) {
        mostrarValidacionSimple('#fecha_nacimiento', '#texto_mensaje_fecha_nacimiento', 'La fecha introducida no puede ser futura.');
        valido = false;
      } else if (!validacionEdad.esMayor) {
        mostrarValidacionSimple('#fecha_nacimiento', '#texto_mensaje_fecha_nacimiento', 'El empleado debe ser mayor de 18 años.');
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
      mostrarValidacionSimple('#telefono', '#texto_mensaje_telefono', 'Este campo no acepta letras.');
      valido = false;
    } else {
      limpiarValidacionSimple('#telefono', '#texto_mensaje_telefono');
    }

    if (!direccion || direccion.trim().length < 10) {
      mostrarValidacionSimple('#direccion', '#texto_mensaje_direccion', 'La dirección debe tener al menos 10 caracteres.');
      valido = false;
    } else {
      limpiarValidacionSimple('#direccion', '#texto_mensaje_direccion');
    }

    return valido;
  }

  function validarDatosModificar() {
    var nombre = $("#nombreModificar").val();
    var apellido = $("#apellidoModificar").val();
    var correo = $("#correoModificar").val();
    var telefono = $("#telefonoModificar").val();
    var direccion = $("#direccionModificar").val();
    var cargo = $("#cargoModificar").val();

    var hayAlgoEscrito = false;
    var valido = true;

    var campos = [
      { campo: '#nombreModificar', mensaje: '#texto_mensaje_nombre_modificar', regla: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/ },
      { campo: '#apellidoModificar', mensaje: '#texto_mensaje_apellido_modificar', regla: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/ },
      { campo: '#correoModificar', mensaje: '#texto_mensaje_correo_modificar', regla: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
      { campo: '#telefonoModificar', mensaje: '#texto_mensaje_telefono_modificar', regla: /^[0-9]{6,7}$/ },
      { campo: '#direccionModificar', mensaje: '#texto_mensaje_direccion_modificar', regla: /^.{10,}$/ },
      { campo: '#cargoModificar', mensaje: '#texto_mensaje_cargo_modificar', regla: /^.+$/ }
    ];

    campos.forEach(function (item) {
      const valor = $(item.campo).val();
      const tieneValor = valor !== undefined && valor !== null && String(valor).trim() !== '';

      if (!tieneValor) return;

      hayAlgoEscrito = true;
      const valorLimpio = String(valor).trim();

      if (!item.regla.test(valorLimpio)) {
        let mensaje = 'Este campo no puede estar vacío.';

        if (item.campo === '#telefonoModificar') {
          mensaje = 'Este campo no acepta letras.';
        } else if (item.campo === '#correoModificar') {
          mensaje = 'Ingrese un correo válido, ejemplo: correo@gmail.com';
        } else if (item.campo === '#nombreModificar' || item.campo === '#apellidoModificar') {
          mensaje = 'Este campo no acepta números.';
        } else if (item.campo === '#direccionModificar') {
          mensaje = 'La dirección debe tener al menos 10 caracteres.';
        }

        mostrarValidacionSimple(item.campo, item.mensaje, mensaje);
        valido = false;
      } else {
        limpiarValidacionSimple(item.campo, item.mensaje);
      }
    });

    if (!hayAlgoEscrito) return true;

    if (!nombre || !nombre.trim()) {
      mostrarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    }

    if (!apellido || !apellido.trim()) {
      mostrarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    }

    if (!direccion || !direccion.trim()) {
      mostrarValidacionSimple('#direccionModificar', '#texto_mensaje_direccion_modificar', 'Este campo no puede estar vacío.');
      valido = false;
    }

    return valido;
  }

  function actualizarEstadoBotonRegistro() {
    var habilitar = validarDatosEmpleado();
    $("#btn_registrar").prop("disabled", !habilitar);
  }

  $("#btn_registrar").prop("disabled", true);

  $("#cargo, #prefijo, #cedula, #nombre, #apellido, #fecha_nacimiento, #sexo, #correo, #operadora, #telefono, #direccion")
    .on("input change blur", function () {
      actualizarEstadoBotonRegistro();
    });

  $("#cedula").on("keydown keypress", function (event) {
    if (event.type === "keydown" && (event.key.length === 1 && !/[0-9]/.test(event.key))) {
      event.preventDefault();
      mostrarValidacionSimple('#cedula', '#texto_mensaje_cedula', 'Este campo no acepta letras.');
      return false;
    }

    if (event.type === "keypress" && (event.which < 48 || event.which > 57 || this.value.length === 9)) {
      event.preventDefault();
      mostrarValidacionSimple('#cedula', '#texto_mensaje_cedula', 'Este campo no acepta letras.');
      return false;
    }
  });

  $("#cedula").keyup(function () {
    var valor = $(this).val();
    var prefijo = $("#prefijo").val();

    $("#texto_mensaje_cedula").css("display", "none").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("#prefijo").css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (!valor && !prefijo) return;

    if (!prefijo) {
      $("#texto_mensaje_cedula").css("display", "block").text("Debes seleccionar el prefijo.");
      return;
    }

    if (valor.length < 7 || valor.length > 9) {
      $("#texto_mensaje_cedula").css("display", "block").text("La cédula debe tener entre 7 y 9 dígitos.");
      return;
    }

    if (validarDatosEmpleado()) {
      $("#btn_registrar").prop("disabled", false);
    } else {
      $("#btn_registrar").prop("disabled", true);
    }
  });

  $("#prefijo").change(function () {
    $("#texto_mensaje_cedula").css("display", "none").text("");
    $("#cedula").css("border", "1px solid #ced4da").css("box-shadow", "none");
  });

  bloquearNumerosEnTexto('#nombre', '#texto_mensaje_nombre');
  bloquearNumerosEnTexto('#apellido', '#texto_mensaje_apellido');

  $("#nombre").on("blur", function () {
    if (!$(this).val() || !$(this).val().trim()) {
      mostrarValidacionSimple('#nombre', '#texto_mensaje_nombre', 'Este campo no puede estar vacío.');
    } else {
      limpiarValidacionSimple('#nombre', '#texto_mensaje_nombre');
    }
  });

  $("#apellido").on("blur", function () {
    if (!$(this).val() || !$(this).val().trim()) {
      mostrarValidacionSimple('#apellido', '#texto_mensaje_apellido', 'Este campo no puede estar vacío.');
    } else {
      limpiarValidacionSimple('#apellido', '#texto_mensaje_apellido');
    }
  });

  $("#telefono").on("keydown keypress", function (event) {
    if (event.type === "keydown" && (event.key.length === 1 && !/[0-9]/.test(event.key))) {
      event.preventDefault();
      $(this).css({ border: '1px solid #cfd4da', 'box-shadow': 'none' });
      $("#texto_mensaje_telefono").css({
        display: 'block',
        color: '#ff4d4d',
        'font-size': '13px',
        'font-weight': '600',
        'margin-top': '4px',
        'text-align': 'left'
      }).text('Este campo no acepta letras.');
      return false;
    }

    if (event.type === "keypress" && (event.which < 48 || event.which > 57 || this.value.length === 7)) {
      event.preventDefault();
      $(this).css({ border: '1px solid #cfd4da', 'box-shadow': 'none' });
      $("#texto_mensaje_telefono").css({
        display: 'block',
        color: '#ff4d4d',
        'font-size': '13px',
        'font-weight': '600',
        'margin-top': '4px',
        'text-align': 'left'
      }).text('Este campo no acepta letras.');
      return false;
    }
  });

  $("#telefono").on("blur", function () {
    if (!$(this).val() || !$(this).val().trim()) {
      mostrarValidacionSimple('#telefono', '#texto_mensaje_telefono', 'Este campo no puede estar vacío.');
    } else if (!/^[0-9]{6,7}$/.test($(this).val())) {
      mostrarValidacionSimple('#telefono', '#texto_mensaje_telefono', 'Este campo no acepta letras.');
    } else {
      limpiarValidacionSimple('#telefono', '#texto_mensaje_telefono');
    }
  });

  $("#telefonoModificar").on("keydown keypress", function (event) {
    if (event.type === "keydown" && (event.key.length === 1 && !/[0-9]/.test(event.key))) {
      event.preventDefault();
      $(this).css({ border: '1px solid #cfd4da', 'box-shadow': 'none' });
      $("#texto_mensaje_telefono_modificar").css({
        display: 'block',
        color: '#ff4d4d',
        'font-size': '13px',
        'font-weight': '600',
        'margin-top': '4px',
        'text-align': 'left'
      }).text('Este campo no acepta letras.');
      return false;
    }

    if (event.type === "keypress" && (event.which < 48 || event.which > 57 || this.value.length === 7)) {
      event.preventDefault();
      $(this).css({ border: '1px solid #cfd4da', 'box-shadow': 'none' });
      $("#texto_mensaje_telefono_modificar").css({
        display: 'block',
        color: '#ff4d4d',
        'font-size': '13px',
        'font-weight': '600',
        'margin-top': '4px',
        'text-align': 'left'
      }).text('Este campo no acepta letras.');
      return false;
    }
  });

  $("#telefonoModificar").on("blur", function () {
    if (!$(this).val() || !$(this).val().trim()) {
      mostrarValidacionSimple('#telefonoModificar', '#texto_mensaje_telefono_modificar', 'Este campo no puede estar vacío.');
    } else if (!/^[0-9]{6,7}$/.test($(this).val())) {
      mostrarValidacionSimple('#telefonoModificar', '#texto_mensaje_telefono_modificar', 'Este campo no acepta letras.');
    } else {
      limpiarValidacionSimple('#telefonoModificar', '#texto_mensaje_telefono_modificar');
    }
  });

  $("#nombreModificar").on("keydown keypress", function (event) {
    const tecla = event.key;
    const codigo = event.which || event.keyCode;

    if (event.type === 'keydown' && tecla.length === 1 && /[0-9]/.test(tecla)) {
      event.preventDefault();
      mostrarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar', 'Este campo no acepta números.');
      return false;
    }

    if (event.type === 'keypress' && (codigo >= 48 && codigo <= 57)) {
      event.preventDefault();
      mostrarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar', 'Este campo no acepta números.');
      return false;
    }
  });

  $("#apellidoModificar").on("keydown keypress", function (event) {
    const tecla = event.key;
    const codigo = event.which || event.keyCode;

    if (event.type === 'keydown' && tecla.length === 1 && /[0-9]/.test(tecla)) {
      event.preventDefault();
      mostrarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar', 'Este campo no acepta números.');
      return false;
    }

    if (event.type === 'keypress' && (codigo >= 48 && codigo <= 57)) {
      event.preventDefault();
      mostrarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar', 'Este campo no acepta números.');
      return false;
    }
  });

  $("#nombreModificar").on("blur", function () {
    if (!$(this).val() || !$(this).val().trim()) {
      mostrarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar', 'Este campo no puede estar vacío.');
    } else {
      limpiarValidacionSimple('#nombreModificar', '#texto_mensaje_nombre_modificar');
    }
  });

  $("#apellidoModificar").on("blur", function () {
    if (!$(this).val() || !$(this).val().trim()) {
      mostrarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar', 'Este campo no puede estar vacío.');
    } else {
      limpiarValidacionSimple('#apellidoModificar', '#texto_mensaje_apellido_modificar');
    }
  });

  $("#direccionModificar").on("blur", function () {
    if (!$(this).val() || !$(this).val().trim()) {
      mostrarValidacionSimple('#direccionModificar', '#texto_mensaje_direccion_modificar', 'Este campo no puede estar vacío.');
    } else if ($(this).val().trim().length < 10) {
      mostrarValidacionSimple('#direccionModificar', '#texto_mensaje_direccion_modificar', 'La dirección debe tener al menos 10 caracteres.');
    } else {
      limpiarValidacionSimple('#direccionModificar', '#texto_mensaje_direccion_modificar');
    }
  });

  $("#btn_registrar").on("click", function (event) {
    event.preventDefault();

    if (!validarDatosEmpleado()) {
      $("#btn_registrar").prop("disabled", true);
      return false;
    }

    $("#btn_registrar").prop("disabled", false);
  });

  $("#modificarDatos").on("click", function () {
    if (validarDatosModificar()) {
    }
  });
});