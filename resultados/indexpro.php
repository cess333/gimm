<?php
// Incluir el archivo de conexión
include('../conexion.php');

// Consulta para contar el total de participantes
$total_sql = "SELECT COUNT(*) AS total_participantes FROM participante p";
$total_result = $conn->query($total_sql);
$total_data = $total_result->fetch_assoc();

// Consulta para contar los participantes sin calificación completa
$missing_participants_sql = "
    SELECT COUNT(DISTINCT p.id) AS faltan_calificacion
    FROM participante p
    LEFT JOIN calificacion c ON p.id = c.participante_id
    JOIN categoria cat ON p.categoria_id = cat.id
    WHERE 
        (cat.aparato_salto = 1 AND c.salto IS NULL) OR
        (cat.aparato_barras = 1 AND c.barras IS NULL) OR
        (cat.aparato_viga = 1 AND c.viga IS NULL) OR
        (cat.aparato_piso = 1 AND c.piso IS NULL) OR
        (cat.aparato_tumbling = 1 AND c.tumbling IS NULL) OR
        (cat.aparato_arzones = 1 AND c.arzones IS NULL) OR
        (cat.aparato_anillos = 1 AND c.anillos IS NULL) OR
        (cat.aparato_barras_paralelas = 1 AND c.barras_paralelas IS NULL) OR
        (cat.aparato_barra_fija = 1 AND c.barra_fija IS NULL) OR
        (cat.aparato_circuitos = 1 AND c.circuitos IS NULL) OR
        (cat.aparato_salto = 1 AND c.panel IS NULL)
";
$missing_participants_result = $conn->query($missing_participants_sql);
$missing_participants_data = $missing_participants_result->fetch_assoc();
$participantesSinCalificacion = $missing_participants_data['faltan_calificacion'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: #007bff;
            color: white;
            font-size: 1.5rem;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            color: white;
        }
        .btn-disabled {
            background-color: #b0b0b0;
            color: #6c757d !important;
            pointer-events: none;
            cursor: not-allowed;
        }
        .alert-custom {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <?php include('../aparatos/navbar.php'); ?>

    <div class="container text-center mt-5">
        <h1 class="mb-4">Selecciona el orden de las calificaciones</h1>
        
        <!-- Mostrar mensaje si hay participantes sin calificación -->
        <?php if ($participantesSinCalificacion > 0): ?>
            <div class="alert alert-custom">
                Faltan calificaciones en algún aparato. 
                Por favor, asegúrate de ingresar todas las calificaciones necesarias para continuar.
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-center gap-3">
            <!-- Botón para porcategoria.php -->
            <a href="porcategoria.php" id="btnPorCategoria" class="btn btn-custom <?php echo $participantesSinCalificacion > 0 ? 'btn-disabled' : ''; ?>">Por Categoría</a>
            <!-- Botón para porpanel.php -->
            <a href="porpanel.php" id="btnPorPanel" class="btn btn-custom <?php echo $participantesSinCalificacion > 0 ? 'btn-disabled' : ''; ?>">Por Panel</a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var participantesSinCalificacion = <?php echo $participantesSinCalificacion; ?>;
            var btnCategoria = document.getElementById("btnPorCategoria");
            var btnPanel = document.getElementById("btnPorPanel");

            if (participantesSinCalificacion > 0) {
                btnCategoria.classList.add("btn-disabled");
                btnCategoria.style.pointerEvents = "none";
                btnPanel.classList.add("btn-disabled");
                btnPanel.style.pointerEvents = "none";
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
