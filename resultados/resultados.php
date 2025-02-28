<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Gimnasia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { color: #000; background: #fff; margin: 0; padding: 0; }
            .button-container, .filter-container, .navbar { display: none; } /* Ocultar elementos no deseados */
            table { page-break-inside: avoid; border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #000; padding: 8px; text-align: center; }
            h2, h3, h4 { page-break-after: avoid; }
            .table-responsive { overflow: visible !important; }
        }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .d-none { display: none !important; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            toggleLugarDisplay(); // Mostrar la opción seleccionada al cargar

            // Manejar los tres checkboxes
            const conEmpatesCheckbox = document.getElementById('checkbox-con-empates');
            const sinEmpatesCheckbox = document.getElementById('checkbox-sin-empates');
            const conEmpates2Checkbox = document.getElementById('checkbox-con-empates-2');

            conEmpatesCheckbox.addEventListener('change', () => {
                if (conEmpatesCheckbox.checked) {
                    sinEmpatesCheckbox.checked = false;
                    conEmpates2Checkbox.checked = false;
                } else if (!sinEmpatesCheckbox.checked && !conEmpates2Checkbox.checked) {
                    conEmpatesCheckbox.checked = true; // Forzar al menos uno seleccionado
                }
                toggleLugarDisplay();
            });

            sinEmpatesCheckbox.addEventListener('change', () => {
                if (sinEmpatesCheckbox.checked) {
                    conEmpatesCheckbox.checked = false;
                    conEmpates2Checkbox.checked = false;
                } else if (!conEmpatesCheckbox.checked && !conEmpates2Checkbox.checked) {
                    sinEmpatesCheckbox.checked = true; // Forzar al menos uno seleccionado
                }
                toggleLugarDisplay();
            });

            conEmpates2Checkbox.addEventListener('change', () => {
                if (conEmpates2Checkbox.checked) {
                    conEmpatesCheckbox.checked = false;
                    sinEmpatesCheckbox.checked = false;
                } else if (!conEmpatesCheckbox.checked && !sinEmpatesCheckbox.checked) {
                    conEmpates2Checkbox.checked = true; // Forzar al menos uno seleccionado
                }
                toggleLugarDisplay();
            });
        });

        function printPage() {
            window.print();
        }

        function filterResults() {
            const ronda = document.getElementById('ronda-select').value;
            const modo = document.getElementById('modo-select').value;
            let lugar = 'con-empates';
            if (document.getElementById('checkbox-sin-empates').checked) {
                lugar = 'sin-empates';
            } else if (document.getElementById('checkbox-con-empates-2').checked) {
                lugar = 'con-empates-2';
            }
            window.location.href = `resultados.php?ronda=${ronda}&modo=${modo}&lugar=${lugar}`;
        }

        function toggleLugarDisplay() {
            const conEmpates = document.getElementById('checkbox-con-empates').checked;
            const sinEmpates = document.getElementById('checkbox-sin-empates').checked;
            const conEmpates2 = document.getElementById('checkbox-con-empates-2').checked;

            document.querySelectorAll('.con-empates').forEach(col => col.classList.add('d-none'));
            document.querySelectorAll('.sin-empates').forEach(col => col.classList.add('d-none'));
            document.querySelectorAll('.con-empates-2').forEach(col => col.classList.add('d-none'));

            if (conEmpates) {
                document.querySelectorAll('.con-empates').forEach(col => col.classList.remove('d-none'));
            } else if (sinEmpates) {
                document.querySelectorAll('.sin-empates').forEach(col => col.classList.remove('d-none'));
            } else if (conEmpates2) {
                document.querySelectorAll('.con-empates-2').forEach(col => col.classList.remove('d-none'));
            }
        }
    </script>
</head>
<body class="bg-light text-dark">
    <?php include('../aparatos/navbar.php'); ?>

    <div class="container mt-5">
        <?php
        include '../conexion.php';

        // Obtener rondas existentes
        $sql_rondas = "SELECT DISTINCT ronda FROM calificacion_ronda ORDER BY ronda";
        $result_rondas = $conn->query($sql_rondas);
        $rondas = [];
        while ($row = $result_rondas->fetch_assoc()) {
            $rondas[] = $row['ronda'];
        }

        // Filtro de ronda, modo y lugar desde GET o valores por defecto
        $ronda_filter = isset($_GET['ronda']) && in_array($_GET['ronda'], $rondas) ? $_GET['ronda'] : (empty($rondas) ? '' : end($rondas)); // Última ronda por defecto
        $modo_filter = isset($_GET['modo']) && in_array($_GET['modo'], ['panel', 'categoria']) ? $_GET['modo'] : 'categoria'; // Por categoría por defecto
        $lugar_filter = isset($_GET['lugar']) && in_array($_GET['lugar'], ['con-empates', 'sin-empates', 'con-empates-2']) ? $_GET['lugar'] : 'con-empates-2';
        ?>

        <div class="button-container mb-4 text-center">
            <button class="btn btn-primary me-2" onclick="printPage()">Imprimir Página</button>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="checkbox-con-empates-2" <?php echo $lugar_filter === 'con-empates-2' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="checkbox-con-empates-2">Lugar con empates</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="checkbox-con-empates" <?php echo $lugar_filter === 'con-empates' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="checkbox-con-empates">Desocupar lugares</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="checkbox-sin-empates" <?php echo $lugar_filter === 'sin-empates' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="checkbox-sin-empates">Lugar sin empates</label>
            </div>

            
        </div>

        <div class="filter-container mb-4 text-center">
            <label for="ronda-select">Ronda:</label>
            <select id="ronda-select" class="form-control d-inline-block w-25 me-2" onchange="filterResults()">
                <?php
                foreach ($rondas as $ronda) {
                    $selected = $ronda == $ronda_filter ? 'selected' : '';
                    echo "<option value='$ronda' $selected>$ronda</option>";
                }
                if (empty($rondas)) {
                    echo "<option value=''>Sin rondas</option>";
                }
                ?>
            </select>
            <label for="modo-select">Modo:</label>
            <select id="modo-select" class="form-control d-inline-block w-25 me-2" onchange="filterResults()">
                <option value="panel" <?php echo $modo_filter == 'panel' ? 'selected' : ''; ?>>Por Panel</option>
                <option value="categoria" <?php echo $modo_filter == 'categoria' ? 'selected' : ''; ?>>Por Categoría</option>
            </select>
        </div>

        <div>
            <h1 class="text-center mb-4">Resultados de Gimnasia - Ronda <?php echo $ronda_filter; ?></h1>

            <?php
            $aparatos = ["salto", "barras", "viga", "piso", "tumbling", "arzones", "anillos", "barras_paralelas", "barra_fija", "circuitos"];

            if (!empty($rondas)) {
                if ($modo_filter == 'panel') {
                    $sql_panels = "SELECT DISTINCT panel FROM calificacion_ronda WHERE ronda = '$ronda_filter' ORDER BY panel";
                    $result_panels = $conn->query($sql_panels);

                    if ($result_panels->num_rows > 0) {
                        while ($panel_row = $result_panels->fetch_assoc()) {
                            $panel = $panel_row['panel'];
                            echo "<h2 class='text-center mt-5'>Panel: $panel</h2>";

                            $where_clause = "WHERE cr.ronda = '$ronda_filter' AND cr.panel = '$panel'";
                            $sql_categories = "SELECT DISTINCT p.categoria_id, c.categoria AS nombre_categoria, c.nivel, c.forma
                                               FROM participante p
                                               JOIN categoria c ON p.categoria_id = c.id
                                               JOIN calificacion_ronda cr ON cr.participante_id = p.id
                                               $where_clause";
                            $result_categories = $conn->query($sql_categories);

                            if ($result_categories->num_rows > 0) {
                                while ($category_row = $result_categories->fetch_assoc()) {
                                    $categoria_id = $category_row['categoria_id'];
                                    $nombre_categoria = $category_row['nombre_categoria'];
                                    $nivel = $category_row['nivel'];
                                    $forma = $category_row['forma'];

                                    echo "<h3>Nivel: $nivel - Categoría: $nombre_categoria</h3>";
                                    mostrarTablas($conn, $aparatos, $where_clause, $categoria_id, $forma);
                                }
                            } else {
                                echo "<p class='text-center'>No hay categorías disponibles para este panel.</p>";
                            }
                        }
                    } else {
                        echo "<p class='text-center'>No hay paneles registrados para la ronda $ronda_filter.</p>";
                    }
                } else {
                    $where_clause = "WHERE cr.ronda = '$ronda_filter'";
                    $sql_categories = "SELECT DISTINCT p.categoria_id, c.categoria AS nombre_categoria, c.nivel, c.forma
                                       FROM participante p
                                       JOIN categoria c ON p.categoria_id = c.id
                                       JOIN calificacion_ronda cr ON cr.participante_id = p.id
                                       $where_clause";
                    $result_categories = $conn->query($sql_categories);

                    if ($result_categories->num_rows > 0) {
                        while ($category_row = $result_categories->fetch_assoc()) {
                            $categoria_id = $category_row['categoria_id'];
                            $nombre_categoria = $category_row['nombre_categoria'];
                            $nivel = $category_row['nivel'];
                            $forma = $category_row['forma'];

                            echo "<h2 class='text-center mt-5'>Nivel: $nivel - Categoría: $nombre_categoria</h2>";
                            mostrarTablas($conn, $aparatos, $where_clause, $categoria_id, $forma);
                        }
                    } else {
                        echo "<p class='text-center'>No hay categorías registradas para la ronda $ronda_filter.</p>";
                    }
                }
            } else {
                echo "<p class='text-center'>No hay rondas registradas en la base de datos.</p>";
            }

            $conn->close();

            function mostrarTablas($conn, $aparatos, $where_clause, $categoria_id, $forma) {
                global $lugar_filter;

                foreach ($aparatos as $aparato) {
                    $sql_check = "SELECT COUNT(*) AS count FROM calificacion_ronda cr
                                  $where_clause
                                  AND cr.participante_id IN (SELECT id FROM participante WHERE categoria_id = $categoria_id)
                                  AND cr.$aparato IS NOT NULL";
                    $check_result = $conn->query($sql_check);
                    $check_row = $check_result->fetch_assoc();

                    if ($check_row['count'] > 0) {
                        echo "<h4>Aparato: " . ucfirst(str_replace('_', ' ', $aparato)) . "</h4>";
                        echo "<div class='table-responsive'><table class='table table-bordered'>";
                        echo "<thead><tr>";
                        echo "<th class='con-empates'>Lugar</th>";
                        echo "<th class='sin-empates d-none'>Lugar</th>";
                        echo "<th class='con-empates-2 d-none'>Lugar</th>";
                        echo "<th>Nombre</th><th>Club</th><th>Calificación</th></tr></thead><tbody>";

                        $sql_aparato = ($forma === 'Porcentaje')
                            ? "SELECT p.nombre, cl.nombre AS club, cr.$aparato AS calificacion,
                               ROUND(SUM(COALESCE(cr2.salto, 0) + COALESCE(cr2.barras, 0) + COALESCE(cr2.viga, 0) + COALESCE(cr2.piso, 0) +
                                     COALESCE(cr2.tumbling, 0) + COALESCE(cr2.arzones, 0) + COALESCE(cr2.anillos, 0) +
                                     COALESCE(cr2.barras_paralelas, 0) + COALESCE(cr2.barra_fija, 0) + COALESCE(cr2.circuitos, 0)) /
                                     NULLIF((CASE WHEN cr2.salto IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.barras IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.viga IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.piso IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.tumbling IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.arzones IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.anillos IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.barras_paralelas IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.barra_fija IS NOT NULL THEN 1 ELSE 0 END +
                                             CASE WHEN cr2.circuitos IS NOT NULL THEN 1 ELSE 0 END), 0), 2) AS all_around
                               FROM calificacion_ronda cr
                               JOIN participante p ON cr.participante_id = p.id
                               JOIN club cl ON p.club_id = cl.id
                               LEFT JOIN calificacion_ronda cr2 ON cr2.participante_id = p.id AND cr2.ronda = cr.ronda
                               $where_clause
                               AND p.categoria_id = $categoria_id AND cr.$aparato IS NOT NULL
                               GROUP BY p.id, cr.$aparato
                               ORDER BY cr.$aparato DESC, all_around DESC"
                            : "SELECT p.nombre, cl.nombre AS club, cr.$aparato AS calificacion,
                               SUM(COALESCE(cr2.salto, 0) + COALESCE(cr2.barras, 0) + COALESCE(cr2.viga, 0) + COALESCE(cr2.piso, 0) +
                                   COALESCE(cr2.tumbling, 0) + COALESCE(cr2.arzones, 0) + COALESCE(cr2.anillos, 0) +
                                   COALESCE(cr2.barras_paralelas, 0) + COALESCE(cr2.barra_fija, 0) + COALESCE(cr2.circuitos, 0)) AS all_around
                               FROM calificacion_ronda cr
                               JOIN participante p ON cr.participante_id = p.id
                               JOIN club cl ON p.club_id = cl.id
                               LEFT JOIN calificacion_ronda cr2 ON cr2.participante_id = p.id AND cr2.ronda = cr.ronda
                               $where_clause
                               AND p.categoria_id = $categoria_id AND cr.$aparato IS NOT NULL
                               GROUP BY p.id, cr.$aparato
                               ORDER BY cr.$aparato DESC, all_around DESC";

                        $result_aparato = $conn->query($sql_aparato);

                        $lugar_con_empates = 1;  // 1, 2, 2, 4, 5
                        $lugar_sin_empates = 1;  // 1, 2, 3, 4, 5
                        $lugar_con_empates_2 = 1; // 1, 2, 2, 3, 4
                        $last_score = null;
                        $current_max_lugar = 0;

                        while ($row = $result_aparato->fetch_assoc()) {
                            if ($last_score !== $row['calificacion']) {
                                $lugar_con_empates = $lugar_sin_empates;
                                $lugar_con_empates_2 = $current_max_lugar + 1;
                                $last_score = $row['calificacion'];
                            }

                            echo "<tr>";
                            echo "<td class='con-empates'>$lugar_con_empates</td>";
                            echo "<td class='sin-empates d-none'>$lugar_sin_empates</td>";
                            echo "<td class='con-empates-2 d-none'>$lugar_con_empates_2</td>";
                            echo "<td>{$row['nombre']}</td>";
                            echo "<td>{$row['club']}</td>";
                            echo "<td>{$row['calificacion']}</td>";
                            echo "</tr>";

                            $lugar_sin_empates++;
                            $current_max_lugar = max($current_max_lugar, $lugar_con_empates_2);
                        }

                        echo "</tbody></table></div>";
                    }
                }

                // Tabla All Around
                $sql_all_around = ($forma === 'Porcentaje')
                    ? "SELECT p.nombre, cl.nombre AS club,
                        ROUND(SUM(COALESCE(cr.salto, 0) + COALESCE(cr.barras, 0) + COALESCE(cr.viga, 0) + COALESCE(cr.piso, 0) +
                              COALESCE(cr.tumbling, 0) + COALESCE(cr.arzones, 0) + COALESCE(cr.anillos, 0) +
                              COALESCE(cr.barras_paralelas, 0) + COALESCE(cr.barra_fija, 0) + COALESCE(cr.circuitos, 0)) /
                              NULLIF((CASE WHEN cr.salto IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.barras IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.viga IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.piso IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.tumbling IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.arzones IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.anillos IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.barras_paralelas IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.barra_fija IS NOT NULL THEN 1 ELSE 0 END +
                                      CASE WHEN cr.circuitos IS NOT NULL THEN 1 ELSE 0 END), 0), 2) AS total
                        FROM calificacion_ronda cr
                        JOIN participante p ON cr.participante_id = p.id
                        JOIN club cl ON p.club_id = cl.id
                        $where_clause AND p.categoria_id = $categoria_id
                        GROUP BY p.id
                        ORDER BY total DESC"
                    : "SELECT p.nombre, cl.nombre AS club,
                        SUM(COALESCE(cr.salto, 0) + COALESCE(cr.barras, 0) + COALESCE(cr.viga, 0) + COALESCE(cr.piso, 0) +
                            COALESCE(cr.tumbling, 0) + COALESCE(cr.arzones, 0) + COALESCE(cr.anillos, 0) +
                            COALESCE(cr.barras_paralelas, 0) + COALESCE(cr.barra_fija, 0) + COALESCE(cr.circuitos, 0)) AS total
                        FROM calificacion_ronda cr
                        JOIN participante p ON cr.participante_id = p.id
                        JOIN club cl ON p.club_id = cl.id
                        $where_clause AND p.categoria_id = $categoria_id
                        GROUP BY p.id
                        ORDER BY total DESC";

                $result_all_around = $conn->query($sql_all_around);

                if ($result_all_around->num_rows > 0) {
                    echo "<h4>All Around</h4>";
                    echo "<div class='table-responsive'><table class='table table-bordered'>";
                    echo "<thead><tr>";
                    echo "<th class='con-empates'>Lugar</th>";
                    echo "<th class='sin-empates d-none'>Lugar</th>";
                    echo "<th class='con-empates-2 d-none'>Lugar</th>";
                    echo "<th>Nombre</th><th>Club</th><th>Total</th></tr></thead><tbody>";

                    $lugar_con_empates = 1;  // 1, 2, 2, 4, 5
                    $lugar_sin_empates = 1;  // 1, 2, 3, 4, 5
                    $lugar_con_empates_2 = 1; // 1, 2, 2, 3, 4
                    $last_total = null;
                    $current_max_lugar = 0;

                    while ($row = $result_all_around->fetch_assoc()) {
                        if ($last_total !== $row['total']) {
                            $lugar_con_empates = $lugar_sin_empates;
                            $lugar_con_empates_2 = $current_max_lugar + 1;
                            $last_total = $row['total'];
                        }

                        echo "<tr>";
                        echo "<td class='con-empates'>$lugar_con_empates</td>";
                        echo "<td class='sin-empates d-none'>$lugar_sin_empates</td>";
                        echo "<td class='con-empates-2 d-none'>$lugar_con_empates_2</td>";
                        echo "<td>{$row['nombre']}</td>";
                        echo "<td>{$row['club']}</td>";
                        echo "<td>{$row['total']}</td>";
                        echo "</tr>";

                        $lugar_sin_empates++;
                        $current_max_lugar = max($current_max_lugar, $lugar_con_empates_2);
                    }

                    echo "</tbody></table></div>";
                }
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>