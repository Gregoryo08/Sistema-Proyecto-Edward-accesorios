<?php $rutaBuscada = isset($ruta_vista) ? $ruta_vista : ''; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Página no encontrada</title>
    <link rel="icon" href="./assets/img/icono.ico">
    <style>
        body {
            background: #000910;
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        }

        .contenedor {
            padding: 40px;
        }

        .codigo {
            font-size: 4rem;
            color: rgb(238, 191, 0);
            margin: 0;
        }

        .ruta {
            color: #9aa4ad;
            font-size: 0.9rem;
            word-break: break-all;
        }

        .enlace {
            display: inline-block;
            margin-top: 24px;
            padding: 12px 28px;
            background: rgb(238, 191, 0);
            color: #000910;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="contenedor">
        <h1 class="codigo">404</h1>
        <p>La página solicitada no existe.</p>
        <?php if ($rutaBuscada !== ''): ?>
            <p class="ruta"><?= htmlspecialchars($rutaBuscada, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <a class="enlace" href="?pagina=iniciarSesion">Ir al inicio</a>
    </div>
</body>

</html>
