<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Aparato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(45deg, #000428, #004e92);
            color: #fff;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(255, 255, 255, 0.3);
            animation: fadeIn 1s ease-in-out;
        }
        .btn-aparato {
            margin: 10px;
            font-size: 1.5em;
            padding: 10px 20px;
            width: 200px;
            border-radius: 10px;
            background-color: #ffc107;
            color: #000;
            font-weight: bold;
            transition: transform 0.2s ease-in-out;
        }
        .btn-aparato:hover {
            transform: scale(1.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Selecciona un Aparato</h2>
        <a href="pantalla.php?aparato=Salto" class="btn btn-aparato">Salto</a>
        <a href="pantalla.php?aparato=Barras" class="btn btn-aparato">Barras</a>
        <a href="pantalla.php?aparato=Piso" class="btn btn-aparato">Piso</a>
        <a href="pantalla.php?aparato=Viga" class="btn btn-aparato">Viga</a>
        <a href="pantalla4.php" class="btn btn-aparato">Todos</a>
        <button class="btn btn-aparato" onclick="abrirTodasLasPantallas()">Abrir Todas las Pantallas</button>

        <a href="configuracion.php" class="btn btn-aparato">Configuración</a>
    </div>

    <script>
        function abrirTodasLasPantallas() {
            // Abrir las 4 pantallas de calificación en nuevas pestañas
            window.open("pantalla.php?aparato=Salto", "_blank");
            window.open("pantalla.php?aparato=Barras", "_blank");
            window.open("pantalla.php?aparato=Piso", "_blank");
            window.open("pantalla.php?aparato=Viga", "_blank");
            window.open("pantalla4.php", "_blank");

        }
    </script>
</body>
</html>
