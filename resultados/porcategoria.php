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
                overflow: visible !important; /* Esto quita el scroll */
            }

            .navbar a {
                display: none;
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
    </style>
    <script>
        let activeButton = null;

        function printPage() {
            window.print();
        }

        function toggleColumnConEmpates() {
            const conEmpatesColumns = document.querySelectorAll('.con-empates');
            const button = document.getElementById('btn-con-empates');
            const otherButton = document.getElementById('btn-sin-empates');

            if (activeButton === 'con-empates') {
                conEmpatesColumns.forEach(col => col.classList.add('d-none'));
                button.classList.remove('btn-success');
                button.classList.add('btn-secondary');
                activeButton = null;
            } else {
                conEmpatesColumns.forEach(col => col.classList.remove('d-none'));
                button.classList.remove('btn-secondary');
                button.classList.add('btn-success');
                otherButton.classList.remove('btn-success');
                otherButton.classList.add('btn-secondary');
                document.querySelectorAll('.sin-empates').forEach(col => col.classList.add('d-none'));
                activeButton = 'con-empates';
            }
        }

        function toggleColumnSinEmpates() {
            const sinEmpatesColumns = document.querySelectorAll('.sin-empates');
            const button = document.getElementById('btn-sin-empates');
            const otherButton = document.getElementById('btn-con-empates');

            if (activeButton === 'sin-empates') {
                sinEmpatesColumns.forEach(col => col.classList.add('d-none'));
                button.classList.remove('btn-success');
                button.classList.add('btn-secondary');
                activeButton = null;
            } else {
                sinEmpatesColumns.forEach(col => col.classList.remove('d-none'));
                button.classList.remove('btn-secondary');
                button.classList.add('btn-success');
                otherButton.classList.remove('btn-success');
                otherButton.classList.add('btn-secondary');
                document.querySelectorAll('.con-empates').forEach(col => col.classList.add('d-none'));
                activeButton = 'sin-empates';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Marcar el botón "Lugar con empates" como activo al cargar la página
            toggleColumnConEmpates();
        });
    </script>
</head>
<body class="bg-light text-dark">

    <?php include('../aparatos/navbar.php'); ?>

    <div class="container mt-5">
        <div class="button-container mb-4 text-center">
            <button class="btn btn-primary me-2" onclick="printPage()">Imprimir Página</button>
            <button id="btn-con-empates" class="btn btn-success me-2" onclick="toggleColumnConEmpates()">Lugar con empates</button>
            <button id="btn-sin-empates" class="btn btn-secondary me-2" onclick="toggleColumnSinEmpates()">Lugar sin empates</button>
            <button class="btn btn-danger me-2" onclick="moverCalificaciones()">Limpiar Calificaciones</button>
        </div>

        <h1 class="text-center mb-4">Calificaciones de Gimnasia</h1>
        

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
    // Incluir archivo de conexión
include '../conexion.php';

    // Nombres de los aparatos
$aparatos = ["salto", "barras", "viga", "piso", "tumbling", "arzones", "anillos", "barras_paralelas", "barra_fija", "circuitos"];

    // Niveles especiales para ocultar sumatoria o promedio y usar lugares personalizados
$niveles_con_promedio = ["PN", "N1", "N2"];

    // Obtener los niveles con datos
$sql_niveles = "SELECT DISTINCT nivel FROM categoria 
WHERE id IN (SELECT DISTINCT categoria_id FROM participante)";
$result_niveles = $conn->query($sql_niveles);

if ($result_niveles->num_rows > 0) {
    $primera_vez = true;
    while ($nivel_row = $result_niveles->fetch_assoc()) {
        $nivel = $nivel_row["nivel"];
        $nivel_mostrado = false;

            // Verificar si el nivel tiene categorías visibles
        $sql_categorias_visibles = "SELECT id FROM categoria WHERE nivel = '$nivel'";
        $result_categorias_visibles = $conn->query($sql_categorias_visibles);
        $hay_categorias_visibles = false;
        while ($categoria_row_visibles = $result_categorias_visibles->fetch_assoc()) {
            $categoria_id_visibles = $categoria_row_visibles["id"];
            foreach ($aparatos as $aparato) {
                $sql_check_visibles = "SELECT COUNT(*) AS count
                FROM calificacion
                JOIN participante ON calificacion.participante_id = participante.id
                WHERE participante.categoria_id = $categoria_id_visibles AND calificacion.$aparato IS NOT NULL";
                $check_result_visibles = $conn->query($sql_check_visibles);
                $row_check_visibles = $check_result_visibles->fetch_assoc();

                if ($row_check_visibles['count'] > 0) {
                    $hay_categorias_visibles = true;
                    break 2;
                }
            }
        }

            // Añadir un salto de página antes de cada nivel solo si tiene categorías visibles y no es el primer nivel
        if (!$primera_vez && $hay_categorias_visibles) {
            echo "<div class='page-break'></div>";
        }
        if ($hay_categorias_visibles) {
            $primera_vez = false;
        }

            // Obtener configuraciones de lugar si el nivel es PN, N1, o N2
        $config_lugares = [];
        if (in_array($nivel, $niveles_con_promedio)) {
            $sql_config = "SELECT lugar, rango_min, rango_max FROM configuracion_lugares_unificada";
            $result_config = $conn->query($sql_config);
            while ($config = $result_config->fetch_assoc()) {
                $config_lugares[] = $config;
            }
        }

            // Obtener categorías de este nivel
        $sql_categorias = "SELECT id, categoria FROM categoria WHERE nivel = '$nivel' ORDER BY categoria";
        $result_categorias = $conn->query($sql_categorias);

        while ($categoria_row = $result_categorias->fetch_assoc()) {
            $categoria_id = $categoria_row["id"];
            $categoria = $categoria_row["categoria"];
            $hay_calificaciones = false;

                // Verificar si hay calificaciones en al menos un aparato para la categoría actual
            foreach ($aparatos as $aparato) {
                $sql_check = "SELECT COUNT(*) AS count
                FROM calificacion
                JOIN participante ON calificacion.participante_id = participante.id
                WHERE participante.categoria_id = $categoria_id AND calificacion.$aparato IS NOT NULL";
                $check_result = $conn->query($sql_check);
                $row_check = $check_result->fetch_assoc();

                if ($row_check['count'] > 0) {
                    $hay_calificaciones = true;
                    break;
                }
            }

                // Si la categoría tiene calificaciones, mostrarla
            if ($hay_calificaciones) {
                    // Mostrar el encabezado del nivel solo una vez
                if (!$nivel_mostrado) {
                    echo "<div class='page-break' style='page-break-before: always;'>
                    ";
                    $nivel_mostrado = true;
                }

                    // Agregar un separador visual para cada categoría
                echo "<div class='category-divider page-break' style='page-break-before: always;'>";
                echo "<h3>Nivel: $nivel - Categoría: $categoria</h3>";

                foreach ($aparatos as $aparato) {
                        // Verificar si existen calificaciones en este aparato para esta categoría
                    $sql_check = "SELECT COUNT(*) AS count
                    FROM calificacion
                    JOIN participante ON calificacion.participante_id = participante.id
                    WHERE participante.categoria_id = $categoria_id AND calificacion.$aparato IS NOT NULL";
                    $check_result = $conn->query($sql_check);
                    $row_check = $check_result->fetch_assoc();

                    if ($row_check['count'] > 0) {
                        echo "<h4 class='text-muted'>Aparato: " . ucfirst(str_replace('_', ' ', $aparato)) . "</h4>";

                            // Consulta para obtener calificaciones del aparato en la categoría actual
                        $sql = "SELECT participante.id, participante.nombre, calificacion.$aparato AS calificacion
                        FROM calificacion
                        JOIN participante ON calificacion.participante_id = participante.id
                        WHERE participante.categoria_id = $categoria_id
                        ORDER BY calificacion.$aparato DESC";
                        $result = $conn->query($sql);

                            // Calcular lugares con y sin empates
                        $calificaciones = [];
                        while ($row = $result->fetch_assoc()) {
                            $calificaciones[] = $row;
                        }

                        $lugar_con_empates = 1;
                        $lugar_sin_empates = 1;
                        $ultimo_valor = null;
                        $contador_lugar = 1;

                            // Generar tabla
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-sm table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                        if (!in_array($nivel, $niveles_con_promedio)) {
                            echo "<th class='con-empates'>Lugar (Con Empates)</th><th class='sin-empates'>Lugar (Sin Empates)</th>";
                        }
                        if (in_array($nivel, $niveles_con_promedio)) {
                            echo "<th>Lugar por Rango</th>";
                        }
                        echo "<th>ID Participante</th><th>Nombre</th><th>Calificación ($aparato)</th></tr>";
                        echo "</thead>";
                        echo "<tbody>";

                        foreach ($calificaciones as $index => $row) {
                            if ($row['calificacion'] !== $ultimo_valor) {
                                $lugar_con_empates = $contador_lugar;
                                $ultimo_valor = $row['calificacion'];
                            }

                            $lugar_por_rango = "-";
                            if (in_array($nivel, $niveles_con_promedio)) {
                                foreach ($config_lugares as $config) {
                                    if ($row['calificacion'] >= $config['rango_min'] && $row['calificacion'] <= $config['rango_max']) {
                                        $lugar_por_rango = $config['lugar'];
                                        break;
                                    }
                                }
                            }

                            echo "<tr>";
                            if (!in_array($nivel, $niveles_con_promedio)) {
                                echo "<td class='con-empates'>" . $lugar_con_empates . "</td><td class='sin-empates'>" . $lugar_sin_empates . "</td>";
                            }
                            if (in_array($nivel, $niveles_con_promedio)) {
                                echo "<td>" . $lugar_por_rango . "</td>";
                            }
                            echo "<td>" . $row["id"] . "</td><td>" . $row["nombre"] . "</td><td>" . $row["calificacion"] . "</td></tr>";

                            $lugar_sin_empates++;
                            $contador_lugar++;
                        }
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                    }
                }

                echo "<h4 class='text-muted'>All Around</h4>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm table-bordered'>";
                echo "<thead>";
                echo "<tr>";
                if (!in_array($nivel, $niveles_con_promedio)) {
                    echo "<th class='con-empates'>Lugar (Con Empates)</th><th class='sin-empates'>Lugar (Sin Empates)</th>";
                }
                if (in_array($nivel, $niveles_con_promedio)) {
                    echo "<th>Lugar por Rango</th>";
                }
                echo "<th>ID Participante</th><th>Nombre</th>";
                if (in_array($nivel, $niveles_con_promedio)) {
                    echo "<th>Promedio</th>";
                } else {
                    echo "<th>Sumatoria</th>";
                }
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                $sql_all_around = "SELECT participante.id, participante.nombre,
                SUM(COALESCE(calificacion.salto, 0) + COALESCE(calificacion.barras, 0) + 
                  COALESCE(calificacion.viga, 0) + COALESCE(calificacion.piso, 0) + 
                  COALESCE(calificacion.tumbling, 0) + COALESCE(calificacion.arzones, 0) + 
                  COALESCE(calificacion.anillos, 0) + COALESCE(calificacion.barras_paralelas, 0) + 
                  COALESCE(calificacion.barra_fija, 0) + COALESCE(calificacion.circuitos, 0)) AS sumatoria,
                SUM(COALESCE(calificacion.salto, 0) + COALESCE(calificacion.barras, 0) + 
                  COALESCE(calificacion.viga, 0) + COALESCE(calificacion.piso, 0) + 
                  COALESCE(calificacion.tumbling, 0) + COALESCE(calificacion.arzones, 0) + 
                  COALESCE(calificacion.anillos, 0) + COALESCE(calificacion.barras_paralelas, 0) + 
                  COALESCE(calificacion.barra_fija, 0) + COALESCE(calificacion.circuitos, 0)) / 
                NULLIF(SUM(CASE WHEN calificacion.salto IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.barras IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.viga IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.piso IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.tumbling IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.arzones IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.anillos IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.barras_paralelas IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.barra_fija IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN calificacion.circuitos IS NOT NULL THEN 1 ELSE 0 END), 0) AS promedio
                FROM calificacion
                JOIN participante ON calificacion.participante_id = participante.id
                WHERE participante.categoria_id = $categoria_id
                GROUP BY participante.id
                ORDER BY sumatoria DESC";

                $result_all_around = $conn->query($sql_all_around);

                $lugar_con_empates = 1;
                $lugar_sin_empates = 1;
                $contador_lugar = 1;
                $ultimo_valor = null;

                while ($row = $result_all_around->fetch_assoc()) {
                    if ($row['sumatoria'] != $ultimo_valor) {
                        $lugar_con_empates = $contador_lugar;
                        $ultimo_valor = $row['sumatoria'];
                    }

                    $lugar_por_rango = "-";
                    if (in_array($nivel, $niveles_con_promedio)) {
                        foreach ($config_lugares as $config) {
                            if ($row['promedio'] >= $config['rango_min'] && $row['promedio'] <= $config['rango_max']) {
                                $lugar_por_rango = $config['lugar'];
                                break;
                            }
                        }
                    }

                    echo "<tr>";
                    if (!in_array($nivel, $niveles_con_promedio)) {
                        echo "<td class='con-empates'>" . $lugar_con_empates . "</td><td class='sin-empates'>" . $lugar_sin_empates . "</td>";
                    }
                    if (in_array($nivel, $niveles_con_promedio)) {
                        echo "<td>" . $lugar_por_rango . "</td>";
                    }
                    echo "<td>" . $row["id"] . "</td><td>" . $row["nombre"] . "</td>";
                    if (in_array($nivel, $niveles_con_promedio)) {
                        echo "<td>" . number_format($row["promedio"], 2) . "</td>";
                    } else {
                        echo "<td>" . $row["sumatoria"] . "</td>";
                    }
                    echo "</tr>";

                    $lugar_sin_empates++;
                    $contador_lugar++;
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                    echo "</div>"; // Fin de la categoría
                }
            }
        }
    } else {
        echo "<center><p>Aun no existen calificaciones para ningun participante.</p></center>";
    }

    $conn->close();
    ?>

</div>



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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
