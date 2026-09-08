<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
</head>
<body style="margin: 0; padding: 0;">

    <main class="main" id="main">
        <section id="hero" class="hero section" style="height: 100vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); color: white; font-family: 'Segoe UI', sans-serif; overflow: hidden;">
            <div id="error-container" style="text-align: center; opacity: 0; transform: translateY(30px); transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);">
                <h1 style="font-size: 10rem; margin: 0; color: #38bdf8; text-shadow: 0 0 20px rgba(56, 189, 248, 0.4); animation: float 3s ease-in-out infinite;">404</h1>
                <p style="font-size: 1.5rem; margin-bottom: 2.5rem; letter-spacing: 1px;">Oops! El destino que buscas no existe.</p>
                <a href="?pagina=principal" id="btn-home" style="padding: 12px 30px; background: transparent; color: #38bdf8; text-decoration: none; border: 2px solid #38bdf8; border-radius: 50px; font-weight: bold; transition: all 0.3s ease; display: inline-block;">
                    Volver al inicio
                </a>
            </div>
        </section>
    </main>



    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
    </style>

    <script>
        const container = document.getElementById("error-container");
        const btn = document.getElementById("btn-home");

        btn.addEventListener("mouseover", () => {
            btn.style.background = "#38bdf8";
            btn.style.color = "#0f172a";
            btn.style.boxShadow = "0 0 15px #38bdf8";
        });

        btn.addEventListener("mouseout", () => {
            btn.style.background = "transparent";
            btn.style.color = "#38bdf8";
            btn.style.boxShadow = "none";
        });

        window.addEventListener("load", () => {
            container.style.opacity = "1";
            container.style.transform = "translateY(0)";
        });
    </script>
</body>
</html>