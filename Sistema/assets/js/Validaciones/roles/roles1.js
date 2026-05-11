

$(".text-right button").css("display", "block");


$("#tablaRoles").DataTable({
  destroy: true,
  ajax: {
    url: "?pagina=roles&ajax=true",
    dataSrc: "",
  },
  columns: [
    {
      data: "idRol",
      visible: false
    },
    {
      data: "descripcion_rol"
    },
    {
      data: null,
      render: function (data, type, row) {
        const idActual = row.idRol;


        return `
                        <button type="button" class="btn btn-info btn_verPermisos" data-toggle="modal"
                        data-id="${idActual}" data-nombre="${row.descripcion_rol}"><i class="bi bi-eye-fill"></i></button>

                        <button type="button" class="btn btn-warning btn_modificar" data-toggle="modal" data-target="#modalModificar"
                        data-id="${idActual}"><i class="fa-solid fa-pen-to-square"></i></button>

                        <button type="button" class="btn btn-danger btn-eliminar" data-id="${idActual}" data-nombre="${row.descripcion_rol}"><i class="fa-solid fa-trash-can"></i></button>
                    `;
      },
    },
  ],
  pageLength: 4,
  lengthMenu: [[4, 8], ["4", "8"]],
  columnDefs: [{ className: "dt-head-center", targets: "_all" }],
  language: {
    lengthMenu: "Mostrar _MENU_ registros por página",
    zeroRecords: "No se encontraron resultados",
    info: "Mostrando página _PAGE_ de _PAGES_",
    infoEmpty: "No hay registros disponibles",
    infoFiltered: "(filtrado de _MAX_ registros totales)",
    search: "Buscar:",
    paginate: {
      first: "Primero",
      last: "Último",
      next: "Siguiente",
      previous: "Anterior",
    },
  },
});


// -----------------------------------------------------------------------------------------------------------

$(document).ready(function () {
  $("#btn_registro_rol").click(function () {
    const lista = document.getElementById("seleccion_permisos");
    lista.innerHTML = "";

    const datosParaEnviar = {
      accion: "consultaModulos",
    };

    $.ajax({
      type: "POST",
      url: "",
      contentType: "application/json",
      data: JSON.stringify(datosParaEnviar),
      dataType: "json",
      success: function (response) {
        var res = response;
        var modulos = res.modulo;
        const operaciones = [
          "Control Total",
          "Registrar",
          "Consultar",
          "Listar",
          "Modificar",
          "Eliminar",
        ];

        const lista = document.getElementById("seleccion_permisos");
        lista.innerHTML = "";
        lista.style.listStyleType = "none";
        lista.style.paddingLeft = 0;

        modulos.forEach((modulo) => {
          if (
            !(
              modulo.nombre_modulo.includes("Login") &&
              modulo.nombre_modulo.includes("Ayuda")
            )
          ) {
            const liModulo = document.createElement("li");
            liModulo.style.position = "relative";
            liModulo.style.paddingLeft = "25px";
            liModulo.style.marginBottom = "5px";

            const checkboxModulo = document.createElement("input");
            checkboxModulo.type = "checkbox";
            checkboxModulo.name = "modulos[]";
            checkboxModulo.dataset.id = modulo.id_modulo;
            checkboxModulo.value = modulo.id_modulo;
            checkboxModulo.classList = "modulo";
            checkboxModulo.id = `modulo-${modulo.id_modulo}`;
            checkboxModulo.style.display = "none";

            const labelModulo = document.createElement("label");
            labelModulo.htmlFor = checkboxModulo.id;
            labelModulo.innerHTML = "&nbsp;&nbsp;&nbsp;" + modulo.nombre_modulo;
            labelModulo.classList = "label_operacion";

            liModulo.appendChild(checkboxModulo);
            liModulo.appendChild(labelModulo);

            const ulOperaciones = document.createElement("ul");
            ulOperaciones.style.listStyleType = "none";
            ulOperaciones.style.paddingLeft = 0;

            for (var i = 0; i < operaciones.length; i++) {
              const liOp = document.createElement("li");
              liOp.style.position = "relative";
              liOp.style.paddingLeft = "25px";
              liOp.style.marginBottom = "8px";
              liOp.classList = "liOp";
              liOp.style.lineHeight = 1.6;

              const checkboxOp = document.createElement("input");
              checkboxOp.type = "checkbox";
              checkboxOp.name = `operaciones_modulo_${modulo.id_modulo}[]`;
              checkboxOp.value = 1;
              checkboxOp.classList = "operacion";
              checkboxOp.dataset.modulo_id = modulo.id_modulo;
              checkboxOp.dataset.operacion = operaciones[i].replace(" ", "_");
              checkboxOp.id = `op-${operaciones[i]}-modulo-${modulo.id_modulo}`;

              const labelOp = document.createElement("label");
              labelOp.htmlFor = checkboxOp.id;
              labelOp.innerHTML = "&nbsp;&nbsp;&nbsp;" + operaciones[i];

              liOp.appendChild(checkboxOp);
              liOp.appendChild(labelOp);
              ulOperaciones.appendChild(liOp);
            }

            liModulo.appendChild(ulOperaciones);
            lista.appendChild(liModulo);
          }
        });

        document.querySelectorAll(".modulo").forEach((moduloCheckbox) => {
          moduloCheckbox.addEventListener("change", function () {
            const idModulo = this.dataset.id;
            const operaciones = document.querySelectorAll(
              `.operacion[data-modulo_id="${idModulo}"]`
            );
            operaciones.forEach((op) => (op.checked = this.checked));
          });
        });

        document.querySelectorAll(".operacion").forEach((opCheckbox) => {
          opCheckbox.addEventListener("change", function () {
            const idModulo = this.dataset.modulo_id;
            const moduloCheckbox = document.querySelector(
              `.modulo[data-id="${idModulo}"]`
            );
            const operaciones = document.querySelectorAll(
              `.operacion[data-modulo_id="${idModulo}"]`
            );
            var check = false;

            for (var i = 0; i < operaciones.length; i++) {
              if (operaciones[i].checked) {
                check = true;

                break;
              }
            }

            if (!check) {
              moduloCheckbox.checked = false;

              return;
            }

            if (opCheckbox.dataset.operacion == "Control_Total") {
              operaciones.forEach((op) => (op.checked = this.checked));
              var check = false;

              for (var i = 0; i < operaciones.length; i++) {
                if (operaciones[i].checked) {
                  check = true;

                  break;
                }
              }

              if (!check) {
                moduloCheckbox.checked = false;

                return;
              }

              moduloCheckbox.checked = true;
              return;
            }

            if (this.checked && moduloCheckbox) {
              moduloCheckbox.checked = true;

              return;
            }
          });
        });
      },
      error: function (xhr, status, error) {
        mensaje("error", "Creado");
      },
    });
  });

  $("#registro").click(function () {
    if (validarDatos()) {
      mensaje("pregunta", "Estas Seguro de Registrar el Dato?", registrar);
    } else {
      mensaje(
        "warning",
        "Debes de ingresar el nombre del rol y al menos un permiso, por favor verifique para continuar con el registro"
      );
    }
  });

  function registrar() {
    var nombre = $("#rol").val();
    const checkboxes = document.querySelectorAll(
      'input[name^="operaciones_modulo_"]:checked'
    );

    const operacionesSeleccionadas = Array.from(checkboxes).map((cb) => ({
      operacion: cb.dataset.operacion,
      valor_operacion: cb.value,
      id_modulo: getModuloIdFromName(cb.name),
    }));

    function getModuloIdFromName(name) {
      const match = name.match(/operaciones_modulo_(\d+)\[\]/);
      return match ? parseInt(match[1]) : null;
    }

    const todaslasoperaciones = [
      ...new Set(
        operacionesSeleccionadas.map((item) => item.operacion.toLowerCase())
      ),
    ];

    const datosTransformados = operacionesSeleccionadas.reduce(
      (acumulador, actual) => {
        const { id_modulo, operacion, valor_operacion } = actual;
        let moduloExiste = acumulador.find(
          (item) => item.id_modulo == id_modulo
        );

        if (!moduloExiste) {
          moduloExiste = { id_modulo: id_modulo };

          todaslasoperaciones.forEach((op) => {
            moduloExiste[op] = 0;
          });
          acumulador.push(moduloExiste);
        }

        moduloExiste[operacion.toLowerCase()] = parseInt(valor_operacion);

        return acumulador;
      },
      []
    );

    const datosParaEnviar = {
      permisos: datosTransformados,
      nombre: nombre,
      accion: "registrar",
    };

    let timerInterval;
    Swal.fire({
      title: "Procesando!",
      html: "",
      timer: 2000,
      color: "white",
      background: "#000910",
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
      },
      willClose: () => {
        clearInterval(timerInterval);
      },
    }).then((result) => {
      if (result.dismiss === Swal.DismissReason.timer) {
        $.ajax({
          type: "POST",
          url: "",
          contentType: "application/json",
          data: JSON.stringify(datosParaEnviar),
          dataType: "json",
          success: function (response) {
            var res = response;

            if (res.success) {
              mensaje("success");

              $("#modalRegistroRol").modal("hide");
              $("#btn_cancel").click();

              const lista = document.getElementById("seleccion_permisos");
              lista.innerHTML = "";
              $("#formRegistroRol")[0].reset();

              $("input")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");
              $("select")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");
              $("textarea")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");

              $("#registrar").css("display", "none");
              $("#tablaRoles").DataTable().ajax.reload();
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);

              $(
                "#formRegistroRol input, #formRegistroRol select, #formRegistroRol textarea"
              )
                .css("border", "1px solid rgb(14, 184, 37)")
                .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

              $.each(array, function (index, value) {
                $("#" + value)
                  .css("border", "1px solid rgb(158, 3, 3)")
                  .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
              });
            } else if (res.invalido) {
              mensaje("warning", res.invalido);

              $(
                "#formRegistroRol input, #formRegistroRol select, #formRegistroRol textarea"
              )
                .css("border", "1px solid rgb(14, 184, 37)")
                .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

              $("#" + value)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
            } else {
              mensaje("error", "Ah Ocurrido un error en el Servidor!");
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Ah Ocurrido un error en el Servidor!");
          },
        });
      }
    });
  }

  $("#btn_cancel").click(function () {
    const lista = document.getElementById("seleccion_permisos");
    lista.innerHTML = "";
    $("#formRegistroRol")[0].reset();

    $("input").css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("select").css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("textarea").css("border", "1px solid #ced4da").css("box-shadow", "none");

    $("#registrar").css("display", "none");
  });

  // -------------------------------------------------------------

  $(document).on("click", ".btn_verPermisos", function () {
    var id_rol = $(this).data("id");
    var nombre = $(this).data("nombre");
    var datosParaEnviar = {
      id_rol: id_rol,
      accion: "consultarPermisos",
    };

    var contenedor = document.getElementById("mostrarPermisos");
    contenedor.innerHTML = "";

    $.ajax({
      type: "POST",
      url: "",
      contentType: "application/json",
      data: JSON.stringify(datosParaEnviar),
      dataType: "json",
      success: function (response) {
    var res = response;

    if (res.incompleto) mensaje("warning", res.incompleto);
    if (res.invalido) mensaje("warning", res.invalido);
    if (res.error) mensaje("error", res.error);

    $("#nombre_rol").html(nombre);

    var contenedor = document.getElementById("mostrarPermisos");
    contenedor.innerHTML = ""; 

    var ul_contenedor = document.createElement("ul");
    ul_contenedor.style.padding = "0";
    ul_contenedor.style.display = "flex";
    ul_contenedor.style.flexWrap = "wrap";
    ul_contenedor.style.gap = "20px";
    ul_contenedor.style.justifyContent = "center";

   
    const traductorAcciones = {
        1: "Registrar",
        2: "Consultar",
        3: "Modificar",
        4: "Eliminar",
        5: "Listar",
        6: "Control Total"
    };

    
    const datosAgrupados = {};

    res.forEach(registro => {
        const mod = registro.nombre_modulo;
        if (!datosAgrupados[mod]) {
            datosAgrupados[mod] = new Set();
        }
       
        const nombreAccion = traductorAcciones[registro.id_accion];
        if (nombreAccion) {
            datosAgrupados[mod].add(nombreAccion);
        }
    });

    
    Object.keys(datosAgrupados).forEach((nombreModulo) => {
        var li_modulo = document.createElement("li");
        li_modulo.style.listStyle = "none";
        li_modulo.style.width = "280px"; 
        li_modulo.style.marginBottom = "20px";
        li_modulo.style.border = "1px solid #ddd";
        li_modulo.style.borderRadius = "10px";
        li_modulo.style.padding = "10px";
        li_modulo.style.backgroundColor = "#fff";
        li_modulo.style.boxShadow = "0 4px 6px rgba(0,0,0,0.1)";

        const labelModulo = document.createElement("label");
        labelModulo.innerHTML = '<i class="bi bi-folder-fill"></i> ' + nombreModulo;
        labelModulo.style.display = "block";
        labelModulo.style.fontWeight = "bold";
        labelModulo.style.textAlign = "center";
        labelModulo.style.padding = "8px";
        labelModulo.style.backgroundColor = "#343a40";
        labelModulo.style.color = "#fff";
        labelModulo.style.borderRadius = "5px";
        labelModulo.style.marginBottom = "10px";
        
        li_modulo.appendChild(labelModulo);

        var ul_operaciones = document.createElement("ul");
        ul_operaciones.style.listStyleType = "none";
        ul_operaciones.style.padding = "0";
        ul_operaciones.style.display = "flex";
        ul_operaciones.style.flexWrap = "wrap";
        ul_operaciones.style.gap = "5px";
        ul_operaciones.style.justifyContent = "center";

        datosAgrupados[nombreModulo].forEach(accion => {
            var li_operacion = document.createElement("li");
            var span_operacion = document.createElement("span");
            
            var esControl = accion === "Control Total";
            var color = esControl ? "bg-success" : "bg-primary";
            
            span_operacion.classList = "badge " + color;
            span_operacion.style.padding = "6px 10px";
            span_operacion.style.fontSize = "0.7rem";
            span_operacion.style.textTransform = "uppercase";
            span_operacion.innerHTML = accion;
            
            li_operacion.append(span_operacion);
            ul_operaciones.append(li_operacion);
        });

        li_modulo.append(ul_operaciones);
        ul_contenedor.append(li_modulo);
    });

    contenedor.append(ul_contenedor);
    $("#modalVerRol").modal("show");
},
    });
  });

  $("#btn_cancelar_ver").click(function () {
    var contenedor = document.getElementById("mostrarPermisos");
    contenedor.innerHTML = "";
  });

  // ----------------------------------------------------------------

  $(document).on("click", ".btn_modificar", function () {
    var id = $(this).data("id");

    const lista = document.getElementById("modificarPermisos");
    lista.innerHTML = "";

    const datosParaEnviar = {
      accion: "consultaModulos",
    };

    $.ajax({
      type: "POST",
      url: "",
      contentType: "application/json",
      data: JSON.stringify(datosParaEnviar),
      dataType: "json",
      success: function (response) {
        var res = response;
        var modulos = res.modulo;
        const operaciones = [
          "Control Total",
          "Registrar",
          "Consultar",
          "Listar",
          "Modificar",
          "Eliminar",
        ];

        const lista = document.getElementById("modificarPermisos");
        lista.innerHTML = "";
        lista.style.listStyleType = "none";
        lista.style.paddingLeft = 0;

        modulos.forEach((modulo) => {
          if (
            !(
              modulo.nombre_modulo.includes("Login") ||
              modulo.nombre_modulo.includes("Ayuda")
            )
          ) {
            const liModulo = document.createElement("li");
            liModulo.style.position = "relative";
            liModulo.style.paddingLeft = "25px";
            liModulo.style.marginBottom = "5px";

            const checkboxModulo = document.createElement("input");
            checkboxModulo.type = "checkbox";
            checkboxModulo.name = "modulos_modificar[]";
            checkboxModulo.dataset.id = modulo.id_modulo;
            checkboxModulo.value = modulo.id_modulo;
            checkboxModulo.classList = "modulo_modificar";
            checkboxModulo.id = `modulo_modificar-${modulo.id_modulo}`;
            checkboxModulo.style.display = "none";

            const labelModulo = document.createElement("label");
            labelModulo.htmlFor = checkboxModulo.id;
            labelModulo.innerHTML = "&nbsp;&nbsp;&nbsp;" + modulo.nombre_modulo;
            labelModulo.classList = "label_operacion";

            liModulo.appendChild(checkboxModulo);
            liModulo.appendChild(labelModulo);

            const ulOperaciones = document.createElement("ul");
            ulOperaciones.style.listStyleType = "none";
            ulOperaciones.style.paddingLeft = 0;

            for (var i = 0; i < operaciones.length; i++) {
              const liOp = document.createElement("li");
              liOp.style.position = "relative";
              liOp.style.paddingLeft = "25px";
              liOp.style.marginBottom = "8px";
              liOp.classList = "liOp";
              liOp.style.lineHeight = 1.6;

              const checkboxOp = document.createElement("input");
              checkboxOp.type = "checkbox";
              checkboxOp.name = `operaciones_modulo_modificar_${modulo.id_modulo}[]`;
              checkboxOp.value = 1;
              checkboxOp.classList = "operacion_modificar";
              checkboxOp.dataset.modulo_id_modificar = modulo.id_modulo;
              checkboxOp.dataset.operacion_modificar = operaciones[i].replace(
                " ",
                "_"
              );
              checkboxOp.id = `op_modificar-${operaciones[i]}-modulo-${modulo.id_modulo}`;

              const labelOp = document.createElement("label");
              labelOp.htmlFor = checkboxOp.id;
              labelOp.innerHTML = "&nbsp;&nbsp;&nbsp;" + operaciones[i];

              liOp.appendChild(checkboxOp);
              liOp.appendChild(labelOp);
              ulOperaciones.appendChild(liOp);
            }

            liModulo.appendChild(ulOperaciones);
            lista.appendChild(liModulo);
          }
        });

       var datosParaEnviar = {
    id_rol: id,
    accion: "consultarPermisos",
};

$.ajax({
    type: "POST",
    url: "",
    contentType: "application/json",
    data: JSON.stringify(datosParaEnviar),
    dataType: "json",
    success: function (response) {
        var res = response;

        $(".modulo_modificar, .operacion_modificar").prop("checked", false);

        const mapaAccionesBD = {
            1: "Registrar",
            2: "Consultar",
            3: "Modificar",
            4: "Eliminar",
            5: "Listar",
            6: "Control_Total"
        };

       
        res.forEach(permiso => {
            const idModulo = permiso.id_modulo;
            const idAccion = permiso.id_accion;
            const nombreAccionUI = mapaAccionesBD[idAccion];

            
            const checkModulo = document.getElementById(`modulo_modificar-${idModulo}`);
            if (checkModulo) checkModulo.checked = true;

          
            const selector = `input[data-modulo_id_modificar="${idModulo}"][data-operacion_modificar="${nombreAccionUI}"]`;
            const checkAccion = document.querySelector(selector);
            
            if (checkAccion) {
                checkAccion.checked = true;
            }

         
            if (idAccion == 6) {
                $(`input[data-modulo_id_modificar="${idModulo}"]`).prop("checked", true);
            }
        });

        
        $("#modificar").css("display", "block");
        
       
        $("#id_rol").val(id);
        $("#modalModificarRol").modal("show");
    },
    error: function (xhr, status, error) {
        mensaje("error", "Ah ocurrido un error en el Servidor!");
    },
        });

        $("#id_rol").val(id)
        $("#modalModificarRol").modal("show");

        document
          .querySelectorAll(".modulo_modificar")
          .forEach((moduloCheckbox) => {
            moduloCheckbox.addEventListener("change", function () {
              const idModulo = this.dataset.id;
              const operaciones = document.querySelectorAll(
                `.operacion_modificar[data-modulo_id_modificar="${idModulo}"]`
              );
              operaciones.forEach((op) => (op.checked = this.checked));
            });
          });

        document
          .querySelectorAll(".operacion_modificar")
          .forEach((opCheckbox) => {
            opCheckbox.addEventListener("change", function () {
              const idModulo = this.dataset.modulo_id_modificar;
              const moduloCheckbox = document.querySelector(
                `.modulo_modificar[data-id="${idModulo}"]`
              );
              const operaciones = document.querySelectorAll(
                `.operacion_modificar[data-modulo_id_modificar="${idModulo}"]`
              );
              var check = false;

              for (var i = 0; i < operaciones.length; i++) {
                if (operaciones[i].checked) {
                  check = true;

                  break;
                }
              }

              if (!check) {
                moduloCheckbox.checked = false;

                return;
              }

              if (opCheckbox.dataset.operacion_modificar == "Control_Total") {
                operaciones.forEach((op) => (op.checked = this.checked));
                var check = false;

                for (var i = 0; i < operaciones.length; i++) {
                  if (operaciones[i].checked) {
                    check = true;

                    break;
                  }
                }

                if (!check) {
                  moduloCheckbox.checked = false;

                  return;
                }

                moduloCheckbox.checked = true;
                return;
              }

              if (this.checked && moduloCheckbox) {
                moduloCheckbox.checked = true;

                return;
              }
            });
          });
      },
      error: function (xhr, status, error) {
        mensaje("error", "Creado");
      },
    });
  });

  $("#modificar").click(function () {
    if (validarDatosModificar()) {
      mensaje("pregunta", "Estas Seguro de Modificar el Dato?", modificar);
    } else {
      mensaje(
        "warning",
        "Debes de seleccionar al menos un permiso, por favor verifique para continuar con la actualización"
      );
    }

  });

  function modificar() {
    var id = $("#id_rol").val();

    const checkboxes = document.querySelectorAll(
      'input[name^="operaciones_modulo_modificar_"]:checked'
    );

    const operacionesSeleccionadas = Array.from(checkboxes).map((cb) => ({
      operacion: cb.dataset.operacion_modificar,
      valor_operacion: cb.value,
      id_modulo: getModuloIdFromName(cb.name),
    }));

    function getModuloIdFromName(name) {
      const match = name.match(/operaciones_modulo_modificar_(\d+)\[\]/);
      return match ? parseInt(match[1]) : null;
    }

    const todaslasoperaciones = [
      ...new Set(
        operacionesSeleccionadas.map((item) => item.operacion.toLowerCase())
      ),
    ];

    const datosTransformados = operacionesSeleccionadas.reduce(
      (acumulador, actual) => {
        const { id_modulo, operacion, valor_operacion } = actual;
        let moduloExiste = acumulador.find(
          (item) => item.id_modulo == id_modulo
        );

        if (!moduloExiste) {
          moduloExiste = { id_modulo: id_modulo };

          todaslasoperaciones.forEach((op) => {
            moduloExiste[op] = 0;
          });
          acumulador.push(moduloExiste);
        }

        moduloExiste[operacion.toLowerCase()] = parseInt(valor_operacion);

        return acumulador;
      },
      []
    );

    const datosParaEnviar = {
      id: id,
      permisos: datosTransformados,
      accion: "modificar"
    }

    let timerInterval;
    Swal.fire({
      title: "Procesando!",
      html: "",
      timer: 2000,
      color: "white",
      background: "#000910",
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
      },
      willClose: () => {
        clearInterval(timerInterval);
      },
    }).then((result) => {
      if (result.dismiss === Swal.DismissReason.timer) {
        $.ajax({
          type: "POST",
          url: "",
          contentType: "application/json",
          data: JSON.stringify(datosParaEnviar),
          dataType: "json",
          success: function (response) {
            var res = response

            if (res.success) {
              mensaje("success");

              $("#modalModificarRol").modal("hide");
              $("#btn_cancel").click();

              const lista = document.getElementById("modificarPermisos");
              lista.innerHTML = "";
              $("#formModificarRol")[0].reset();

              $("#modificar").css("display", "none");
              $("#tablaRoles").DataTable().ajax.reload();
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
            } else if (res.invalido) {
              mensaje("warning", res.invalido);
            } else {
              mensaje("error", "Ah Ocurrido un error en el Servidor!");
            }

          },
          error: function (xhr, status, error) {
            mensaje("error", "Ah Ocurrido un error en el Servidor!");
          },
        });
      }
    });
  }

  $("#btn_cancel_modificar").click(function () {
    const lista = document.getElementById("modificarPermisos");
    lista.innerHTML = "";
    $("#formModificarRol")[0].reset();

  });

  // ------------------------------------------------------------------

  $(document).on("click", ".btn-eliminar", function () {
    $("#id_rol").val($(this).data("id"));

    mensaje(
      "eliminar",
      "Estas Seguro de Eliminar el Rol?",
      eliminar,
      "Al eliminar el rol '" +
      $(this).data("nombre") +
      "', se eliminaran sus permisos y los usuarios ligados al mismo no podra entrar al sistema!"
    );
  });

  function eliminar() {
    var id = $("#id_rol").val();

    const datosParaEnviar = {
      id: id,
      accion: "eliminar",
    };

    let timerInterval;
    Swal.fire({
      title: "Procesando!",
      html: "",
      timer: 2000,
      color: "white",
      background: "#000910",
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
      },
      willClose: () => {
        clearInterval(timerInterval);
      },
    }).then((result) => {
      if (result.dismiss === Swal.DismissReason.timer) {
        $.ajax({
          type: "POST",
          url: "",
          contentType: "application/json",
          data: JSON.stringify(datosParaEnviar),
          dataType: "json",
          success: function (response) {
            var res = response;

            if (res.success) {
              $("#tablaRoles").DataTable().ajax.reload();
              mensaje("success");
            } else if (res.error) {
              mensaje("error", "Ah Ocurrido un error en el Servidor!");
            } else if (res.invalido) {
              mensaje("invalido", res.invalido);
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Ah Ocurrido un error en el Servidor!");
          },
        });
      }
    });
  }

  function validarDatos() {
    var rol = $("#rol").val();
    const checkboxes = document.querySelectorAll(
      'input[name^="operaciones_modulo_"]:checked'
    );

    const operacionesSeleccionadas = Array.from(checkboxes).map((cb) => ({
      operacion: cb.dataset.operacion,
      valor_operacion: cb.value,
      id_modulo: getModuloIdFromName(cb.name),
    }));

    function getModuloIdFromName(name) {
      const match = name.match(/operaciones_modulo_(\d+)\[\]/);
      return match ? parseInt(match[1]) : null;
    }

    if (rol && operacionesSeleccionadas.length > 0) {
      return true;
    } else {
      return false;
    }
  }

  function validarDatosModificar() {
    var rol = $("#id_rol").val();
    const checkboxes = document.querySelectorAll(
      'input[name^="operaciones_modulo_modificar_"]:checked'
    );

    const operacionesSeleccionadas = Array.from(checkboxes).map((cb) => ({
      operacion: cb.dataset.operacion,
      valor_operacion: cb.value,
      id_modulo: getModuloIdFromName(cb.name),
    }));

    function getModuloIdFromName(name) {
      const match = name.match(/operaciones_modulo_modificar_(\d+)\[\]/);
      return match ? parseInt(match[1]) : null;
    }

    if (rol && operacionesSeleccionadas.length > 0) {
      return true;
    } else {
      return false;
    }
  }

  function mensaje(accion, mensaje, funcion, title) {
    if (accion == "error") {
      Swal.fire({
        title: "Ups!",
        text: mensaje,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "warning") {
      Swal.fire({
        title: "Lo Siento!",
        text: mensaje,
        icon: "warning",
        color: "white",
        showConfirmButton: false,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
        timer: 2000,
      });
    } else if (accion == "invalido") {
      Swal.fire({
        title: "Ups!",
        text: mensaje,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "pregunta") {
      Swal.fire({
        title: "Estas Seguro!",
        text: mensaje,
        icon: "question",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        confirmButtonBorder: "rgb(238, 191, 0)",
        background: "#000910",
        confirmButtonText: "Confirmar",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          funcion();
        }
      });
    } else if (accion == "eliminar") {
      Swal.fire({
        title: mensaje,
        text: title,
        icon: "question",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        confirmButtonBorder: "rgb(238, 191, 0)",
        background: "#000910",
        confirmButtonText: "Confirmar",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          funcion();
        }
      });
    } else {

      Swal.fire({
        title: "Listo!",
        text: "Proceso Ejecutado con Exito!",
        icon: "success",
        color: "white",
        showConfirmButton: false,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
        timer: 1500,
      });
    }
  }
});
