$(document).ready(function () {

  function mostrarError(selector, mensajeId, texto) {
    $(selector).css({ border: '1px solid #cfd4da', 'box-shadow': 'none' });
    $(mensajeId).text(texto).css('display', 'block');
  }

  function limpiarError(selector, mensajeId) {
    $(selector).css({ border: '1px solid #cfd4da', 'box-shadow': 'none' });
    $(mensajeId).text('').css('display', 'none');
  }

  function esMayorDeEdad(fechaStr) {
    const hoy = new Date();
    const fechaNac = new Date(fechaStr + 'T00:00:00');
    if (isNaN(fechaNac.getTime())) return { esMayor: false, esFutura: false };

    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
      edad--;
    }
    return { esMayor: edad >= 18, esFutura: fechaNac > hoy };
  }

  function limitarTexto(selector, patron, maximo) {
    $(selector).on('input', function () {
      $(this).val($(this).val().replace(patron, '').slice(0, maximo));
    });
  }

  function limitarLongitud(selector, maximo) {
    $(selector).on('input', function () {
      $(this).val($(this).val().slice(0, maximo));
    });
  }

  function prepararCedula() {
    const campo = $('#cedula');

    campo.attr({ maxlength: 8, placeholder: '1234567' });
    campo.val(campo.val().replace(/[^0-9]/g, '').slice(0, 8));

    campo.off('input.cedula').on('input.cedula', function () {
      $(this).val($(this).val().replace(/[^0-9]/g, '').slice(0, 8));
    });
  }

  function obtenerCedula() {
    return $('#prefijo').val() + $('#cedula').val().trim();
  }

  prepararCedula();

  limitarTexto('#nombre, #apellido, #nombre_mod, #apellido_mod', /[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, 35);
  limitarTexto('#telefono, #telefono_mod', /[^0-9]/g, 11);
  limitarLongitud('#clave, #clave_mod', 15);
  limitarLongitud('#correo, #correo_mod', 40);
  limitarLongitud('#direccion, #direccion_mod', 30);

  function validarSeguridad() {
    let valido = true;
    const cedula = obtenerCedula();
    const clave = $('#clave').val();

    if (!/^[VE]-\d{7,8}$/.test(cedula)) {
      mostrarError('#cedula', '#msg_cedula', 'La cédula debe tener entre 7 y 8 dígitos.');
      valido = false;
    } else {
      limpiarError('#cedula', '#msg_cedula');
    }

    if (!clave || !clave.trim()) {
      mostrarError('#clave', '#msg_clave', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (clave.length < 5 || clave.length > 15) {
      mostrarError('#clave', '#msg_clave', 'La contraseña debe tener entre 5 y 15 caracteres.');
      valido = false;
    } else {
      limpiarError('#clave', '#msg_clave');
    }

    return valido;
  }

  function validarDatosPersonales() {
    let valido = true;
    const nombre = $('#nombre').val().trim();
    const apellido = $('#apellido').val().trim();
    const correo = $('#correo').val().trim();
    const telefono = $('#telefono').val().trim();
    const direccion = $('#direccion').val().trim();
    const fecha = $('#fecha_nacimiento').val();
    const sexo = $('#sexo').val();

    if (!nombre || nombre.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(nombre)) {
      mostrarError('#nombre', '#msg_nombre', 'El nombre solo debe contener letras y espacios, máximo 35 caracteres.');
      valido = false;
    } else { limpiarError('#nombre', '#msg_nombre'); }

    if (!apellido || apellido.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(apellido)) {
      mostrarError('#apellido', '#msg_apellido', 'El apellido solo debe contener letras y espacios, máximo 35 caracteres.');
      valido = false;
    } else { limpiarError('#apellido', '#msg_apellido'); }

    if (!correo || correo.length > 40 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      mostrarError('#correo', '#msg_correo', 'Ingrese un correo válido de máximo 40 caracteres.');
      valido = false;
    } else { limpiarError('#correo', '#msg_correo'); }

    if (!/^[0-9]{7,11}$/.test(telefono)) {
      mostrarError('#telefono', '#msg_telefono', 'El teléfono debe contener solo números, entre 7 y 11 dígitos.');
      valido = false;
    } else { limpiarError('#telefono', '#msg_telefono'); }

    if (direccion.length < 5 || direccion.length > 30) {
      mostrarError('#direccion', '#msg_direccion', 'La dirección debe tener entre 5 y 30 caracteres.');
      valido = false;
    } else { limpiarError('#direccion', '#msg_direccion'); }

    if (!fecha) {
      mostrarError('#fecha_nacimiento', '#msg_fecha_nacimiento', 'Debes seleccionar la fecha de nacimiento.');
      valido = false;
    } else {
      const vEdad = esMayorDeEdad(fecha);
      if (vEdad.esFutura) {
        mostrarError('#fecha_nacimiento', '#msg_fecha_nacimiento', 'La fecha introducida no puede ser futura.');
        valido = false;
      } else if (!vEdad.esMayor) {
        mostrarError('#fecha_nacimiento', '#msg_fecha_nacimiento', 'El usuario debe ser mayor de 18 años.');
        valido = false;
      } else {
        limpiarError('#fecha_nacimiento', '#msg_fecha_nacimiento');
      }
    }

    if (!sexo) {
      mostrarError('#sexo', '#msg_sexo', 'Debes seleccionar el sexo.');
      valido = false;
    } else { limpiarError('#sexo', '#msg_sexo'); }

    return valido;
  }

  function validarClasificacion() {
    let valido = true;
    const tipo = $('#tipoUsuario').val();

    if (!tipo) {
      mostrarError('#tipoUsuario', '#msg_tipoUsuario', 'Debes seleccionar un tipo de usuario.');
      valido = false;
    } else { limpiarError('#tipoUsuario', '#msg_tipoUsuario'); }

    if (!$('#id_rol').val()) {
      mostrarError('#id_rol', '#msg_id_rol', 'Debes seleccionar un rol válido.');
      valido = false;
    } else { limpiarError('#id_rol', '#msg_id_rol'); }

    if (tipo === 'empleado') {
      if (!$('#id_cargo').val()) {
        mostrarError('#id_cargo', '#msg_id_cargo', 'Debes seleccionar un cargo.');
        valido = false;
      } else { limpiarError('#id_cargo', '#msg_id_cargo'); }
    }

    return valido;
  }

  $('#btnSiguienteSeguridad').on('click', function () {
    if (validarSeguridad()) {
      var modalTarget = new bootstrap.Modal(document.getElementById('modalDatosPersonales'));
      bootstrap.Modal.getInstance(document.getElementById('modalSeguridad')).hide();
      modalTarget.show();
    }
  });

  $('#btnSiguienteDatos').on('click', function () {
    if (validarDatosPersonales()) {
      var modalTarget = new bootstrap.Modal(document.getElementById('modalClasificacion'));
      bootstrap.Modal.getInstance(document.getElementById('modalDatosPersonales')).hide();
      modalTarget.show();
    }
  });

  document.getElementById('btnFinalizarRegistro').addEventListener('click', function (evento) {
    if (!validarClasificacion()) {
      evento.preventDefault();
      evento.stopPropagation();
    } else {
      $('#cedula').val(obtenerCedula());
    }
  }, true);

  document.getElementById('btnGuardarModificacion').addEventListener('click', function (evento) {
    let valido = true;

    const cedula = $('#cedula_mod').val().trim();
    const nombre = $('#nombre_mod').val().trim();
    const apellido = $('#apellido_mod').val().trim();
    const correo = $('#correo_mod').val().trim();
    const telefono = $('#telefono_mod').val().trim();
    const direccion = $('#direccion_mod').val().trim();
    const fecha = $('#fecha_nacimiento_mod').val();
    const sexo = $('#sexo_mod').val();
    const rol = $('#id_rol_mod').val();
    const clave = $('#clave_mod').val();

    if (!cedula) {
      valido = false;
    }

    if (clave && (clave.length < 5 || clave.length > 15)) {
      mostrarError('#clave_mod', '#msg_clave_mod', 'La contraseña debe tener entre 5 y 15 caracteres.');
      valido = false;
    } else {
      limpiarError('#clave_mod', '#msg_clave_mod');
    }

    if (!rol) { mostrarError('#id_rol_mod', '#msg_id_rol_mod', 'Debes seleccionar un rol válido.'); valido = false; }
    else { limpiarError('#id_rol_mod', '#msg_id_rol_mod'); }

    if (!nombre || nombre.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(nombre)) { mostrarError('#nombre_mod', '#msg_nombre_mod', 'El nombre solo debe contener letras y espacios, máximo 35 caracteres.'); valido = false; }
    else { limpiarError('#nombre_mod', '#msg_nombre_mod'); }

    if (!apellido || apellido.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(apellido)) { mostrarError('#apellido_mod', '#msg_apellido_mod', 'El apellido solo debe contener letras y espacios, máximo 35 caracteres.'); valido = false; }
    else { limpiarError('#apellido_mod', '#msg_apellido_mod'); }

    if (!correo || correo.length > 40 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      mostrarError('#correo_mod', '#msg_correo_mod', 'Ingrese un correo válido de máximo 40 caracteres.'); valido = false;
    } else { limpiarError('#correo_mod', '#msg_correo_mod'); }

    if (!/^[0-9]{7,11}$/.test(telefono)) {
      mostrarError('#telefono_mod', '#msg_telefono_mod', 'El teléfono debe contener solo números, entre 7 y 11 dígitos.'); valido = false;
    } else { limpiarError('#telefono_mod', '#msg_telefono_mod'); }

    if (direccion.length < 5 || direccion.length > 30) {
      mostrarError('#direccion_mod', '#msg_direccion_mod', 'La dirección debe tener entre 5 y 30 caracteres.'); valido = false;
    } else { limpiarError('#direccion_mod', '#msg_direccion_mod'); }

    if (!fecha) {
      mostrarError('#fecha_nacimiento_mod', '#msg_fecha_nacimiento_mod', 'Seleccione la fecha de nacimiento.'); valido = false;
    } else {
      const vEdad = esMayorDeEdad(fecha);
      if (vEdad.esFutura || !vEdad.esMayor) {
        mostrarError('#fecha_nacimiento_mod', '#msg_fecha_nacimiento_mod', 'Fecha inválida o usuario menor de edad.'); valido = false;
      } else { limpiarError('#fecha_nacimiento_mod', '#msg_fecha_nacimiento_mod'); }
    }

    if (!sexo) { mostrarError('#sexo_mod', '#msg_sexo_mod', 'Debes seleccionar el sexo.'); valido = false; }
    else { limpiarError('#sexo_mod', '#msg_sexo_mod'); }

    if (!valido) {
      evento.preventDefault();
      evento.stopPropagation();
    }
  }, true);

});

function toggleOpciones(valor) {
  $('#container_rol').removeClass('d-none');
  $('#container_cargo').toggleClass('d-none', valor !== 'empleado');
}