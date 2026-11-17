<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carrito vacío</title>
    <link rel="icon" href="./assets/img/icono.ico">
    <link rel="stylesheet" href="assets/Library/SweetAlerts/sweetalert2.min.css">
</head>

<body>
    <script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Carrito vacío',
            text: 'No hay productos en el carrito. Agrega productos antes de reportar un pago.',
            confirmButtonText: 'Aceptar'
        }).then(function () {
            window.location.href = '?pagina=web_Catalogo';
        });
    </script>
</body>

</html>
