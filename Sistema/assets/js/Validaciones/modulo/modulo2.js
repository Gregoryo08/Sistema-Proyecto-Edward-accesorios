$(document).ready(function () {
    /*$("#modulo").keyup(function(){
        var rol = $(this).val()
        var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/

        const capitalizado = rol.replace(/\b\w+/g, function (palabra) {
            return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
        });
    
        $(this).val(capitalizado);

        if(rol.length > 4 && soloLetras.test(rol)){
            $("#registro").css("display","block")
        }
        else{
            $("#registro").css("display","none")
        }
    })*/

        $("#modulo").on("input", function () {
  let entrada = $(this).val();

  // 1. Eliminar caracteres no permitidos (solo letras y espacios)
  let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");

  // 2. Capitalizar cada palabra
  const capitalizado = soloLetras.replace(/\b\w+/g, function (palabra) {
    return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
  });

  $(this).val(capitalizado); // Actualizar el input con texto filtrado y capitalizado

  // 3. Validaciones de longitud
  const mensaje = $("#mensajeModulo");
  if (capitalizado.length >= 4) {
    $("#registro").show();
    mensaje.text("").hide();
  } else {
    $("#registro").hide();
    mensaje.text("El nombre debe tener al menos 4 letras.").show();
  }
});















$("#modulo_modificar").on("input", function () {
  let entrada = $(this).val();

  // 1. Eliminar números y símbolos (solo letras y espacios permitidos)
  let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");

  // 2. Capitalizar cada palabra
  const capitalizado = soloLetras.replace(/\b\w+/g, function (palabra) {
    return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
  });

  $(this).val(capitalizado); // Actualiza el valor en el input

  // 3. Validar longitud mínima y mostrar mensaje si es necesario
  const mensaje = $("#mensajeModuloModificar");
  if (capitalizado.length >= 4) {
    $("#modificar").show();
    mensaje.text("").hide();
  } else {
    $("#modificar").hide();
    mensaje.text("El nombre debe tener al menos 4 letras.").show();
  }
});



    /*$("#modulo_modificar").keyup(function(){
        var rol = $(this).val()
        var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/

        const capitalizado = rol.replace(/\b\w+/g, function (palabra) {
            return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
        });
    
        $(this).val(capitalizado);

        if(rol.length > 4 && soloLetras.test(rol)){
            $("#modificar").css("display","block")
        }
        else{
            $("#modificar").css("display","none")
        }
    })*/

});