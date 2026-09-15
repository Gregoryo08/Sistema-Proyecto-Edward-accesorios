$(document).ready(function () {
	function validarProveedorAntesDeConfirmar(id) {
		const $input = $(id);
		const valor = $input.val().trim();
		const $feedback = $("#error_" + id.substring(1));
		const esValido = valor.length > 0 && valor.length <= 35 && /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+$/.test(valor);

		$input.removeClass("is-valid is-invalid");
		$feedback.hide().text("");

		if (!valor) {
			$input.addClass("is-invalid");
			$feedback.text("El nombre del proveedor es obligatorio.").show();
			return false;
		}

		if (!esValido) {
			$input.addClass("is-invalid");
			$feedback.text("El nombre solo debe contener letras y espacios, máximo 35 caracteres.").show();
			return false;
		}

		$input.addClass("is-valid");
		return true;
	}

	function validarCampoProveedor(id, indicacion, regla) {
		const $input = $(id);
		const valor = $input.val().trim();
		const $feedback = $("#error_" + id.substring(1));

		$input.removeClass("is-valid is-invalid");
		$feedback.hide().text("");

		if (!regla(valor)) {
			$input.addClass("is-invalid");
			$feedback.text(indicacion).show();
			return false;
		}

		$input.addClass("is-valid");
		return true;
	}

	function validarFormularioProveedor(modificar) {
		const sufijo = modificar ? "_modificar" : "";
		const resultados = [];
		resultados.push(validarProveedorAntesDeConfirmar("#proveedor" + sufijo));
		if (!modificar) {
			resultados.push(validarCampoProveedor("#rif", "El RIF es obligatorio y debe tener máximo 9 dígitos.", valor => valor !== "" && /^[0-9]{1,9}$/.test(valor)));
		}
		resultados.push(validarCampoProveedor("#telefono" + sufijo, "El teléfono debe tener 7 dígitos después del código.", valor => valor !== "" && /^[0-9]{7}$/.test(valor)));

		const correoId = "#correo" + sufijo;
		const correo = $(correoId).val().trim();
		if (!correo) {
			resultados.push(validarCampoProveedor(correoId, "El correo electrónico es obligatorio.", valor => valor !== ""));
		} else {
			resultados.push(validarCampoProveedor(correoId, "Ingrese un correo válido de máximo 45 caracteres.", valor => valor.length <= 45 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)));
		}

		resultados.push(validarCampoProveedor("#ubicacion" + sufijo, "La dirección debe tener entre 5 y 50 caracteres.", valor => valor.length >= 5 && valor.length <= 50));

		const valido = resultados.every(Boolean);
		if (!valido) {
			const primerInvalido = resultados.indexOf(false);
			const mensajes = modificar
				? ["Nombre requerido", "Teléfono requerido", "Correo requerido", "Ubicación requerida"]
				: ["Nombre requerido", "RIF requerido", "Teléfono requerido", "Correo requerido", "Ubicación requerida"];
			Swal.fire({
				title: mensajes[primerInvalido] || "Campos requeridos",
				text: "Complete los campos marcados en rojo.",
				icon: "warning",
				color: "white",
				background: "#000910"
			});
		}
		return valido;
	}

	$("#proveedor, #proveedor_modificar").on("input", function () {
		$(this).val($(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, "").slice(0, 35));
	});

	$("#proveedor, #proveedor_modificar").on("keypress", function (e) {
		if (/[0-9]/.test(String.fromCharCode(e.which))) {
			e.preventDefault();
			const $feedback = $("#error_" + this.id);
			$(this).removeClass("is-valid").addClass("is-invalid");
			$feedback.text("Este campo solo puede aceptar letras!").show();
			Swal.fire({
				title: "Campo inválido",
				text: "Este campo solo puede aceptar letras!",
				icon: "warning",
				color: "white",
				background: "#000910"
			});
		}
	});

	$("#rif, #rif_modificar, #telefono, #telefono_modificar").on("keydown", function (e) {
		if (e.key.length === 1 && !/[0-9]/.test(e.key)) {
			e.preventDefault();
			const titulo = this.id.indexOf("telefono") === 0 ? "Teléfono inválido" : "RIF inválido";
			const texto = "Este campo solo puede aceptar números.";
			$(this).removeClass("is-valid").addClass("is-invalid");
			$("#error_" + this.id).text(texto).show();
			Swal.fire({ title: titulo, text: texto, icon: "warning", color: "white", background: "#000910" });
		}
	});

	$("#rif, #rif_modificar").on("input", function () {
		$(this).val($(this).val().replace(/[^0-9]/g, "").slice(0, 9));
	});

	$("#telefono, #telefono_modificar").on("input", function () {
		$(this).val($(this).val().replace(/[^0-9]/g, "").slice(0, 7));
	});

	$("#btn_registrar").on("click", function () {
		if (validarFormularioProveedor(false)) {
			window.showSweetAlert("pregunta1");
		}
	});

	$("#btn_modificar").on("click", function () {
		if (validarFormularioProveedor(true)) {
			window.showSweetAlert("pregunta2");
		}
	});
});
