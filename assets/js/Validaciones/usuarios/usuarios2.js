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

  // Bloqueos en tiempo de escritura
  function bloquearNumeros(selector, mensajeId) {
    $(selector).on('keydown keypress', function (e) {
      const tecla = e.key;
      const codigo = e.which || e.keyCode;
      if ((e.type === 'keydown' && tecla.length === 1 && /[0-9]/.test(tecla)) ||
          (e.type === 'keypress' && codigo >= 48 && codigo <= 57)) {
        e.preventDefault();
        mostrarError(selector, mensajeId, 'Este campo no acepta números.');
      }
    });
  }

  function bloquearLetras(selector, mensajeId) {
    $(selector).on('keydown keypress', function (e) {
      const tecla = e.key;
      const codigo = e.which || e.keyCode;
      if ((e.type === 'keydown' && tecla.length === 1 && !/[0-9]/.test(tecla)) ||
          (e.type === 'keypress' && (codigo < 48 || codigo > 57))) {
        e.preventDefault();
        mostrarError(selector, mensajeId, 'Este campo no acepta letras.');
      }
    });
  }

  bloquearNumeros('#nombre, #apellido, #nombre_mod, #apellido_mod', '#msg_nombre, #msg_apellido, #msg_nombre_mod, #msg_apellido_mod');
  bloquearLetras('#telefono, #telefono_mod', '#msg_telefono, #msg_telefono_mod');

  function validarSeguridad() {
    let valido = true;
    const cedula = $('#cedula').val();
    const clave = $('#clave').val();
    const regexClave = /^(?=.*[A-Z])(?=.*[^a-zA-Z0-9])[a-zA-Z0-9\W]+$/;

    const numerosCedula = cedula.replace('V-', '').trim();
    if (!numerosCedula || numerosCedula.length < 7 || numerosCedula.length > 9) {
      mostrarError('#cedula', '#msg_cedula', 'La cédula debe tener entre 7 y 9 dígitos.');
      valido = false;
    } else {
      limpiarError('#cedula', '#msg_cedula');
    }

    if (!clave || !clave.trim()) {
      mostrarError('#clave', '#msg_clave', 'Este campo no puede estar vacío.');
      valido = false;
    } else if (clave.length < 5) {
      mostrarError('#clave', '#msg_clave', 'La contraseña debe ser mayor a 5 caracteres.');
      valido = false;
    } else if (!regexClave.test(clave)) {
      mostrarError('#clave', '#msg_clave', 'Debe incluir al menos una mayúscula y un carácter especial.');
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

    if (!nombre) {
      mostrarError('#nombre', '#msg_nombre', 'Este campo no puede estar vacío.');
      valido = false;
    } else { limpiarError('#nombre', '#msg_nombre'); }

    if (!apellido) {
      mostrarError('#apellido', '#msg_apellido', 'Este campo no puede estar vacío.');
      valido = false;
    } else { limpiarError('#apellido', '#msg_apellido'); }

    if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      mostrarError('#correo', '#msg_correo', 'Ingrese un correo válido, ejemplo: correo@gmail.com');
      valido = false;
    } else { limpiarError('#correo', '#msg_correo'); }

    if (!telefono || !/^[0-9]{10,11}$/.test(telefono)) {
      mostrarError('#telefono', '#msg_telefono', 'Ingrese un teléfono válido (10-11 dígitos).');
      valido = false;
    } else { limpiarError('#telefono', '#msg_telefono'); }

    if (!direccion || direccion.length < 10) {
      mostrarError('#direccion', '#msg_direccion', 'La dirección debe tener al menos 10 caracteres.');
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

    if (tipo === 'cliente') {
      if (!$('#id_rol').val()) {
        mostrarError('#id_rol', '#msg_id_rol', 'Debes seleccionar un rol.');
        valido = false;
      } else { limpiarError('#id_rol', '#msg_id_rol'); }
    } else if (tipo === 'empleado') {
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

  $('#btnFinalizarRegistro').on('click', function () {
    if (validarClasificacion()) {
    }
  });

  $('#btnGuardarModificacion').on('click', function () {
    let valido = true;

    const nombre = $('#nombre_mod').val().trim();
    const apellido = $('#apellido_mod').val().trim();
    const correo = $('#correo_mod').val().trim();
    const telefono = $('#telefono_mod').val().trim();
    const direccion = $('#direccion_mod').val().trim();
    const fecha = $('#fecha_nacimiento_mod').val();
    const sexo = $('#sexo_mod').val();
    const rol = $('#id_rol_mod').val();

    if (!rol) { mostrarError('#id_rol_mod', '#msg_id_rol_mod', 'Debes seleccionar un rol.'); valido = false; }
    else { limpiarError('#id_rol_mod', '#msg_id_rol_mod'); }

    if (!nombre) { mostrarError('#nombre_mod', '#msg_nombre_mod', 'Este campo no puede estar vacío.'); valido = false; }
    else { limpiarError('#nombre_mod', '#msg_nombre_mod'); }

    if (!apellido) { mostrarError('#apellido_mod', '#msg_apellido_mod', 'Este campo no puede estar vacío.'); valido = false; }
    else { limpiarError('#apellido_mod', '#msg_apellido_mod'); }

    if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      mostrarError('#correo_mod', '#msg_correo_mod', 'Ingrese un correo válido.'); valido = false;
    } else { limpiarError('#correo_mod', '#msg_correo_mod'); }

    if (!telefono || !/^[0-9]{10,11}$/.test(telefono)) {
      mostrarError('#telefono_mod', '#msg_telefono_mod', 'Ingrese un teléfono válido.'); valido = false;
    } else { limpiarError('#telefono_mod', '#msg_telefono_mod'); }

    if (!direccion || direccion.length < 10) {
      mostrarError('#direccion_mod', '#msg_direccion_mod', 'La dirección debe tener al menos 10 caracteres.'); valido = false;
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

    if (valido) {
    }
  });

});

function toggleOpciones(valor) {
  if (valor === 'cliente') {
    $('#container_rol').removeClass('d-none');
    $('#container_cargo').addClass('d-none');
  } else if (valor === 'empleado') {
    $('#container_cargo').removeClass('d-none');
    $('#container_rol').addClass('d-none');
  } else {
    $('#container_rol').addClass('d-none');
    $('#container_cargo').addClass('d-none');
  }
}