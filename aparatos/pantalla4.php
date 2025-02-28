<?php
session_start();
include '../conexion.php';

// Obtener configuración desde la sesión para pantalla4.php
$config = $_SESSION['config']['pantalla4'] ?? [
    'background_color' => '#6436A9',
    'animations' => true,
    'zoom' => '100%'
];

// Definir los aparatos
$aparatos = ['Salto', 'Barras', 'Piso', 'Viga'];

// Obtener las últimas calificaciones para cada aparato
$calificaciones = [];

foreach ($aparatos as $aparato) {
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
    $calificaciones[$aparato] = $result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla General</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style type="text/css">
        body {
            background-color: <?= htmlspecialchars($config['background_color']); ?>;
            color: #fff;
            font-size: 1.5em;
            font-family: 'Arial', sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
            text-align: center;
        }

        h1 {
            font-size: 4em;
            margin: 30px 0;
            text-transform: uppercase;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 0;
            width: 100vw;
            height: 100vh;
        }

        .aparato-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.3);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
        }

        .info-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            padding: 15px;
        }

        .info-text {
            flex: 1;
        }

        .calificacion-box {
            background-color: #fff;
            color: #28a745;
            padding: 20px;
            margin-right: 50px;
            font-size: 3em;
            font-weight: bold;
            border-radius: 15px;
            text-align: center;
            width: 320px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .highlight {
            color: #ffc107;
            font-weight: bold;
            font-size: 1.8em;
        }

        h2 {
            font-size: 3.5em;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .club-logo {
            position: absolute;
            top: 10px;
            right: 10px;
            max-width: 150px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.body.style.zoom = "<?= htmlspecialchars($config['zoom']); ?>%";
        });
    </script>

    <div class="container">
        <h1>Últimas Calificaciones</h1>
        
        <div class="grid-container">
            <?php foreach ($calificaciones as $aparato => $datos): ?>
                <div class="aparato-container">
                    <?php if (!empty($datos['club_img'])): ?>
                        <img src="<?= htmlspecialchars($datos['club_img']); ?>" alt="Logo de <?= htmlspecialchars($datos['nombre_del_club']); ?>" class="club-logo">
                    <?php endif; ?>

                    <h2><?= htmlspecialchars($aparato); ?></h2>

                    <div class="info-container">
                        <div class="info-text">
                            <?php if ($datos): ?>
                                <p><span class="highlight"><?= htmlspecialchars($datos['nombre']); ?></span></p>
                                <p><strong>Nivel:</strong> <span class="highlight"><?= htmlspecialchars($datos['nivel']); ?></span></p>
                                <p><strong>Categoría:</strong> <span class="highlight"><?= htmlspecialchars($datos['categoria']); ?></span></p>
                                <p><strong>Club:</strong> <span class="highlight"><?= htmlspecialchars($datos['nombre_del_club']); ?></span></p>
                            <?php else: ?>
                                <p>Esperando calificación...</p>
                            <?php endif; ?>
                        </div>

                        <div class="calificacion-box" id="calificacion-<?= strtolower($aparato); ?>">
                            <?= $datos ? htmlspecialchars($datos['calificacion']) : '?'; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        <?php if ($config['animations']): ?>
            <?php foreach ($aparatos as $aparato): ?>
                gsap.from("#calificacion-<?= strtolower($aparato); ?>", { opacity: 0, scale: 0.5, duration: 1, ease: "elastic.out(1, 0.5)" });
            <?php endforeach; ?>
        <?php endif; ?>

        setTimeout(function() {
            window.location.reload();
        }, 15000);
    </script>
</body>
</html>