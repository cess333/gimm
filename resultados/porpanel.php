<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones de Gimnasia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                color: #000;
                background: #fff;
            }
            .button-container {
                display: none;
            }
            table {
                page-break-inside: avoid;
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }
            h2, h3, h4 {
                page-break-after: avoid;
            }
            .table-responsive {
                overflow: visible !important;
            }
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .d-none {
            display: none !important;
        }
    </style>
    <script>
        let activeButton = null;

        function printPage() {
            window.print();
        }

        function toggleColumnConEmpates() {
            document.querySelectorAll('.con-empates').forEach(el => el.classList.remove('d-none'));
            document.querySelectorAll('.sin-empates').forEach(el => el.classList.add('d-none'));
            document.getElementById('btn-con-empates').classList.add('btn-success');
            document.getElementById('btn-sin-empates').classList.remove('btn-success');
            activeButton = 'con-empates';
        }

        function toggleColumnSinEmpates() {
            document.querySelectorAll('.sin-empates').forEach(el => el.classList.remove('d-none'));
            document.querySelectorAll('.con-empates').forEach(el => el.classList.add('d-none'));
            document.getElementById('btn-sin-empates').classList.add('btn-success');
            document.getElementById('btn-con-empates').classList.remove('btn-success');
            activeButton = 'sin-empates';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isPorcentaje = document.body.getAttribute('data-is-porcentaje') === 'true';
            if (isPorcentaje) {
                document.getElementById('btn-con-empates').setAttribute('disabled', 'disabled');
                document.getElementById('btn-sin-empates').setAttribute('disabled', 'disabled');
            } else {
                toggleColumnConEmpates();
            }
        });
    </script>
</head>
<body class="bg-light text-dark" 
    <?php
        include '../conexion.php';
        $is_porcentaje = false;
    ?>
>
    <?php include('../aparatos/navbar.php'); ?>

    <div class="container mt-5">
        <div class="button-container mb-4 text-center">
            <button class="btn btn-primary me-2" onclick="printPage()">Imprimir Página</button>
            <button id="btn-con-empates" class="btn btn-secondary me-2" onclick="toggleColumnConEmpates()">Lugar con empates</button>
            <button id="btn-sin-empates" class="btn btn-secondary me-2" onclick="toggleColumnSinEmpates()">Lugar sin empates</button>
            <button class="btn btn-danger me-2" onclick="moverCalificaciones()">Limpiar Calificaciones</button>
        </div>

        <h1 class="text-center mb-4">Calificaciones de Gimnasia - Por panel</h1>

        <!--codigo de ronda-->

        <div class="text-center mb-4 ocultar-en-impresion">
            <input id="numero-ronda-input" type="number" min="1" class="form-control d-inline-block w-25" placeholder="N° de ronda">
        </div>

        <h1 id="titulo-calificaciones" class="text-center">Ronda #</h1>

        <script type="text/javascript">
            const numeroRondaInput = document.getElementById('numero-ronda-input');
            const tituloCalificaciones = document.getElementById('titulo-calificaciones');

            numeroRondaInput.addEventListener('input', () => {
        const numeroRonda = numeroRondaInput.value || 1; // Valor predeterminado: 1
        tituloCalificaciones.textContent = `Ronda ${numeroRonda}`;
    });
</script>

<!-- CSS -->
<style type="text/css">
    /* Ocultar el título en la página normal */
    #titulo-calificaciones {
        display: none;
    }

    @media print {
        /* Mostrar el título solo al imprimir */
        #titulo-calificaciones {
            display: block; /* Se asegura de que se muestre solo en la impresión */
            font-weight: bold;
            font-size: 3em; /* Ajusta el tamaño de la fuente aquí para que sea más grande */
            text-align: center;
        }

        .ocultar-en-impresion {
            display: none;
        }
    }
</style>

<!--Fin de codigo de ronda-->


        <?php
        $aparatos = ["salto", "barras", "viga", "piso", "tumbling", "arzones", "anillos", "barras_paralelas", "barra_fija", "circuitos"];
        $sql_panels = "SELECT DISTINCT panel FROM calificacion";
        $result_panels = $conn->query($sql_panels);

        if ($result_panels->num_rows > 0) {
            while ($panel_row = $result_panels->fetch_assoc()) {
                $panel = $panel_row['panel'];
                echo "<h2 class='text-center'>Panel: $panel</h2>";

                $sql_categories = "SELECT DISTINCT participante.categoria_id, categoria.categoria AS nombre_categoria, categoria.nivel, categoria.forma
                                   FROM participante
                                   JOIN categoria ON participante.categoria_id = categoria.id
                                   WHERE participante.id IN (SELECT participante_id FROM calificacion WHERE panel='$panel')";
                $result_categories = $conn->query($sql_categories);

                while ($category_row = $result_categories->fetch_assoc()) {
                    $categoria_id = $category_row['categoria_id'];
                    $nombre_categoria = $category_row['nombre_categoria'];
                    $nivel = $category_row['nivel'];
                    $forma = $category_row['forma'];

                    $is_porcentaje = ($forma === 'Porcentaje');

                    echo "<h3>Nivel: $nivel - Categoría: $nombre_categoria</h3>";

                    // Tablas de aparatos
                    foreach ($aparatos as $aparato) {
                        $sql_check_aparato = "SELECT COUNT(*) AS count
                                              FROM calificacion
                                              WHERE panel = '$panel' AND participante_id IN 
                                              (SELECT id FROM participante WHERE categoria_id = $categoria_id) 
                                              AND $aparato IS NOT NULL";
                        $check_result = $conn->query($sql_check_aparato);
                        $check_row = $check_result->fetch_assoc();

                        if ($check_row['count'] > 0) {
                            echo "<h4>Aparato: " . ucfirst(str_replace('_', ' ', $aparato)) . "</h4>";
                            echo "<div class='table-responsive'>";
                            echo "<table class='table table-bordered'>";
                            echo "<thead><tr>";
                            echo $is_porcentaje ? "<th>Lugar</th>" : "<th class='con-empates'>Lugar (Con Empates)</th><th class='sin-empates d-none'>Lugar (Sin Empates)</th>";
                            echo "<th>Nombre</th><th>Club</th><th>Calificación</th></tr></thead>";
                            echo "<tbody>";

                            $sql_aparato = "SELECT participante.nombre, club.nombre AS club, calificacion.$aparato AS calificacion
                                            FROM calificacion
                                            JOIN participante ON calificacion.participante_id = participante.id
                                            JOIN club ON participante.club_id = club.id
                                            WHERE calificacion.panel = '$panel' AND participante.categoria_id = $categoria_id
                                            ORDER BY calificacion.$aparato DESC";

                            $result_aparato = $conn->query($sql_aparato);

                            $lugar_con_empates = 1;
                            $lugar_sin_empates = 1;
                            $last_score = null;

                            while ($row = $result_aparato->fetch_assoc()) {
                                if (!$is_porcentaje) {
                                    if ($last_score !== $row['calificacion']) {
                                        $lugar_con_empates = $lugar_sin_empates;
                                        $last_score = $row['calificacion'];
                                    }

                                    echo "<tr>";
                                    echo "<td class='con-empates'>$lugar_con_empates</td>";
                                    echo "<td class='sin-empates d-none'>$lugar_sin_empates</td>";
                                    echo "<td>{$row['nombre']}</td>";
                                    echo "<td>{$row['club']}</td>";
                                    echo "<td>{$row['calificacion']}</td>";
                                    echo "</tr>";

                                    $lugar_sin_empates++;
                                } else {
                                    $lugar = "Sin asignar";

                                    $sql_lugar = "SELECT lugar FROM configuracion_lugares_unificada 
                                                  WHERE rango_min <= {$row['calificacion']} AND 
                                                        (rango_max IS NULL OR rango_max >= {$row['calificacion']})";
                                    $result_lugar = $conn->query($sql_lugar);
                                    if ($result_lugar->num_rows > 0) {
                                        $row_lugar = $result_lugar->fetch_assoc();
                                        $lugar = $row_lugar['lugar'];
                                    }

                                    echo "<tr>";
                                    echo "<td>$lugar</td>";
                                    echo "<td>{$row['nombre']}</td>";
                                    echo "<td>{$row['club']}</td>";
                                    echo "<td>{$row['calificacion']}</td>";
                                    echo "</tr>";
                                }
                            }

                            echo "</tbody></table></div>";
                        }
                    }

                    // Tabla All Around
$sql_all_around = $is_porcentaje
    ? "SELECT participante.nombre, club.nombre AS club,
        ROUND(SUM(COALESCE(calificacion.salto, 0) + COALESCE(calificacion.barras, 0) +
            COALESCE(calificacion.viga, 0) + COALESCE(calificacion.piso, 0) +
            COALESCE(calificacion.tumbling, 0) + COALESCE(calificacion.arzones, 0) +
            COALESCE(calificacion.anillos, 0) + COALESCE(calificacion.barras_paralelas, 0) +
            COALESCE(calificacion.barra_fija, 0) + COALESCE(calificacion.circuitos, 0)) / 
            NULLIF((CASE WHEN calificacion.salto IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.barras IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.viga IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.piso IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.tumbling IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.arzones IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.anillos IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.barras_paralelas IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.barra_fija IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN calificacion.circuitos IS NOT NULL THEN 1 ELSE 0 END), 0), 2) AS total
        FROM calificacion
        JOIN participante ON calificacion.participante_id = participante.id
        JOIN club ON participante.club_id = club.id
        WHERE calificacion.panel = '$panel' AND participante.categoria_id = $categoria_id
        GROUP BY participante.id
        ORDER BY total DESC"
    : "SELECT participante.nombre, club.nombre AS club,
        SUM(COALESCE(calificacion.salto, 0) + COALESCE(calificacion.barras, 0) +
            COALESCE(calificacion.viga, 0) + COALESCE(calificacion.piso, 0)) AS total
        FROM calificacion
        JOIN participante ON calificacion.participante_id = participante.id
        JOIN club ON participante.club_id = club.id
        WHERE calificacion.panel = '$panel' AND participante.categoria_id = $categoria_id
        GROUP BY participante.id
        ORDER BY total DESC";

$result_all_around = $conn->query($sql_all_around);

if ($result_all_around->num_rows > 0) {
    echo "<h4>All Around</h4>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr>";
    echo $is_porcentaje ? "<th>Lugar</th>" : "<th class='con-empates'>Lugar (Con Empates)</th><th class='sin-empates d-none'>Lugar (Sin Empates)</th>";
    echo "<th>Nombre</th><th>Club</th><th>Total</th></tr></thead>";
    echo "<tbody>";

    $lugar_con_empates = 1;
    $lugar_sin_empates = 1;
    $last_total = null;

    while ($row = $result_all_around->fetch_assoc()) {
        $lugar = "Sin asignar";

        if ($is_porcentaje) {
            // Consulta para asignar lugar según los rangos de configuración
            $sql_lugar = "SELECT lugar FROM configuracion_lugares_unificada 
                          WHERE rango_min <= {$row['total']} AND 
                                (rango_max IS NULL OR rango_max >= {$row['total']})";
            $result_lugar = $conn->query($sql_lugar);
            if ($result_lugar->num_rows > 0) {
                $row_lugar = $result_lugar->fetch_assoc();
                $lugar = $row_lugar['lugar'];
            }
        } else {
            if ($last_total !== $row['total']) {
                $lugar_con_empates = $lugar_sin_empates;
                $last_total = $row['total'];
            }
            $lugar = $lugar_con_empates;
        }

        echo "<tr>";
        echo "<td>$lugar</td>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td>{$row['club']}</td>";
        echo "<td>{$row['total']}</td>";
        echo "</tr>";

        if (!$is_porcentaje) {
            $lugar_sin_empates++;
        }
    }

    echo "</tbody></table></div>";
}

                }
            }
        } else {
            echo "<p class='text-center'>No hay datos disponibles.</p>";
        }

        $conn->close();
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function moverCalificaciones() {
        if (confirm("¿Estás seguro de que deseas limpiar todas las calificaciones? Esta acción no se puede deshacer.")) {
            fetch('mover_calificaciones.php', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.message.includes("correctamente")) {
                        location.reload(); // Recarga la página si el proceso fue exitoso
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al limpiar las calificaciones.');
                });
        }
    }
</script>

</body>
</html>
