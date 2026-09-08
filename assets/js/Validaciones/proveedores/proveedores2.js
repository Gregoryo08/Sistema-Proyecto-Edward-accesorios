$(document).ready(function () {
	function validarProveedorAntesDeConfirmar(id) {
		const $input = $(id);
		const valor = $input.val().trim();
		const $feedback = $("#error_" + id.substring(1));
		const esValido = valor.length >= 3 && /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s-]+$/.test(valor);

		$input.removeClass("is-valid is-invalid");
		$feedback.hide().text("");

		if (!valor) {
			$input.addClass("is-invalid");
			$feedback.text("El nombre del proveedor es obligatorio.").show();
			return false;
		}

		if (!esValido) {
			$input.addClass("is-invalid");
			$feedback.text("El nombre debe tener al menos 3 letras.").show();
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
			resultados.push(validarCampoProveedor("#rif", "El RIF es obligatorio.", valor => valor !== "" && /^[0-9]+$/.test(valor)));
		}
		resultados.push(validarCampoProveedor("#telefono" + sufijo, "El teléfono es obligatorio.", valor => valor !== "" && /^[0-9]{7,11}$/.test(valor)));

		const correoId = "#correo" + sufijo;
		const correo = $(correoId).val().trim();
		if (!correo) {
			resultados.push(validarCampoProveedor(correoId, "El correo electrónico es obligatorio.", valor => valor !== ""));
		} else {
			resultados.push(validarCampoProveedor(correoId, "Formato incorrecto. Ingrese un correo electrónico válido: correo@gmail.com", valor => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)));
		}

		resultados.push(validarCampoProveedor("#ubicacion" + sufijo, "La ubicación es obligatoria.", valor => valor !== ""));

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
		$(this).val($(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s-]/g, ""));
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

	$("#rif, #telefono, #rif_modificar, #telefono_modificar").on("input", function () {
		$(this).val($(this).val().replace(/[^0-9]/g, ""));
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
