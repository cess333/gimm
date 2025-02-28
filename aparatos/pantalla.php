<?php
// Incluir archivo de conexión
include '../conexion.php';

// Obtener el aparato desde la URL
$aparato = isset($_GET['aparato']) ? $_GET['aparato'] : '';
$aparatos_validos = ['Salto', 'Barras', 'Piso', 'Viga'];

if (!in_array($aparato, $aparatos_validos)) {
    die("Aparato no válido.");
}

session_start();
// Obtener configuración desde la sesión para pantalla.php
$config = $_SESSION['config']['pantalla'] ?? [
    'background_color' => '#000428',
    'animations' => true,
    'zoom' => '100%',
    'hide_info_after' => 10000  // Tiempo en milisegundos (10 segundos por defecto)
];

// Obtener las calificaciones más recientes del aparato solicitado, incluyendo la imagen del club
$sql = "
    SELECT p.nombre, c.$aparato AS calificacion, cl.nombre AS nombre_del_club, cl.img AS club_img, cat.nivel AS nivel, cat.categoria AS categoria
    FROM calificacion c
    JOIN participante p ON p.id = c.participante_id
    LEFT JOIN club cl ON p.club_id = cl.id
    LEFT JOIN categoria cat ON p.categoria_id = cat.id
    WHERE c.$aparato IS NOT NULL
    ORDER BY c.id DESC LIMIT 1
";
$result = $conn->query($sql);
$datos = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de <?php echo htmlspecialchars($aparato); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        body {
            background: linear-gradient(45deg, <?= htmlspecialchars($config['background_color']); ?>, #004e92);
            color: #fff;
            font-size: 2em;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(255, 255, 255, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 90%;
            max-width: 1400px;
        }
        h1.title {
            font-size: 4em;
            margin-bottom: 20px;
        }
        .highlight {
            color: #ffc107;
            font-weight: bold;
            font-size: 1.2em;
        }
        .info-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
            width: 100%;
            margin-top: 30px;
        }
        .calificacion-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 20px;
        }
        .calificacion-box {
            background-color: #fff;
            color: #28a745;
            padding: 80px 60px;
            font-size: 5em;
            font-weight: bold;
            border-radius: 20px;
            text-align: center;
            width: 100%;
            height: 100%;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }
        .espera {
            font-size: 3em;
            font-weight: bold;
            display: block;
        }

        /* Estilos para el logo dentro del recuadro de la información del participante */
        .club-logo {
            max-width: 100px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.3);
        }

        .info-text {
            position: relative; /* Necesario para que el logo esté en el contenedor de la información */
        }
        .info-container .info-text {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title"><?php echo htmlspecialchars($aparato); ?></h1>
        
        <div id="esperando" class="espera">Esperando calificación...</div>

        <div id="info-container" class="info-container">
            <div class="info-text">
                <h1 id="nombre"><?php echo htmlspecialchars($datos['nombre']); ?></h1>
                <p><strong>Nivel:</strong> <span class="highlight" id="nivel"><?php echo htmlspecialchars($datos['nivel']); ?></span></p>
                <p><strong>Categoría:</strong> <span class="highlight" id="categoria"><?php echo htmlspecialchars($datos['categoria']); ?></span></p>
                <p><strong>Club:</strong> <span class="highlight" id="club"><?php echo htmlspecialchars($datos['nombre_del_club']); ?></span></p>

                <!-- Aquí aparece el logo dentro de la información del participante -->
                <?php if (!empty($datos['club_img'])): ?>
                    <img src="<?php echo htmlspecialchars($datos['club_img']); ?>" alt="Logo de <?php echo htmlspecialchars($datos['nombre_del_club']); ?>" class="club-logo">
                <?php endif; ?>
            </div>

            <div class="calificacion-container">
                <div class="calificacion-box" id="calificacion-texto">
                    <?php echo htmlspecialchars($datos['calificacion']); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        let ultimaCalificacion = null;

        function actualizarCalificacion() {
            $.ajax({
                url: "obtener_calificacion.php?aparato=<?php echo urlencode($aparato); ?>",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    console.log("Respuesta del servidor:", data);

                    if (data && data.calificacion !== null && data.calificacion !== ultimaCalificacion) {
                        ultimaCalificacion = data.calificacion;

                        $("#nombre").text(data.nombre);
                        $("#nivel").text(data.nivel);
                        $("#categoria").text(data.categoria);
                        $("#club").text(data.nombre_del_club);
                        $("#calificacion-texto").text(data.calificacion);

                        $("#esperando").hide();
                        $("#info-container").show();

                        if (data.club_img) {
                            // Añadir el logo al contenedor de la información
                            $(".info-text img").attr("src", data.club_img);
                        }

                        <?php if ($config['animations']): ?>
                            gsap.from("#calificacion-texto", { opacity: 0, scale: 0.5, duration: 1, ease: "elastic.out(1, 0.5)" });
                        <?php endif; ?>

                        setTimeout(function() {
                            $("#info-container").hide();
                            $("#esperando").show();
                        }, <?= htmlspecialchars($config['hide_info_after']); ?>); // Ahora usa el valor de configuración
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en AJAX:", status, error);
                }
            });
        }

        setInterval(actualizarCalificacion, 2500);

        document.addEventListener("DOMContentLoaded", function () {
            document.body.style.zoom = "<?= htmlspecialchars($config['zoom']); ?>%";
        });
    </script>
</body>
</html>