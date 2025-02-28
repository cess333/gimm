<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados por Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { color: #000; background: #fff; margin: 0; padding: 0; }
            .button-container, .filter-container, .navbar { display: none; }
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
            toggleLugarDisplay();

            const conEmpatesCheckbox = document.getElementById('checkbox-con-empates');
            const sinEmpatesCheckbox = document.getElementById('checkbox-sin-empates');
            const conEmpates2Checkbox = document.getElementById('checkbox-con-empates-2');
            const cantidadInput = document.getElementById('cantidad-input');
            const minimoInput = document.getElementById('minimo-input');
            const elegidoCheckbox = document.getElementById('checkbox-elegidos');

            conEmpatesCheckbox.addEventListener('change', () => {
                if (conEmpatesCheckbox.checked) {
                    sinEmpatesCheckbox.checked = false;
                    conEmpates2Checkbox.checked = false;
                } else if (!sinEmpatesCheckbox.checked && !conEmpates2Checkbox.checked) {
                    conEmpatesCheckbox.checked = true;
                }
                toggleLugarDisplay();
                filterResults();
            });

            sinEmpatesCheckbox.addEventListener('change', () => {
                if (sinEmpatesCheckbox.checked) {
                    conEmpatesCheckbox.checked = false;
                    conEmpates2Checkbox.checked = false;
                } else if (!conEmpatesCheckbox.checked && !conEmpates2Checkbox.checked) {
                    sinEmpatesCheckbox.checked = true;
                }
                toggleLugarDisplay();
                filterResults();
            });

            conEmpates2Checkbox.addEventListener('change', () => {
                if (conEmpates2Checkbox.checked) {
                    conEmpatesCheckbox.checked = false;
                    sinEmpatesCheckbox.checked = false;
                } else if (!conEmpatesCheckbox.checked && !sinEmpatesCheckbox.checked) {
                    conEmpates2Checkbox.checked = true;
                }
                toggleLugarDisplay();
                filterResults();
            });

            // Validación silenciosa para los inputs
            cantidadInput.addEventListener('change', () => {
                const cantidad = parseInt(cantidadInput.value) || 1;
                const minimo = parseInt(minimoInput.value) || 1;
                if (minimo < cantidad) {
                    minimoInput.value = cantidad; // Ajusta silenciosamente
                }
                filterResults();
            });

            minimoInput.addEventListener('change', () => {
                const cantidad = parseInt(cantidadInput.value) || 1;
                const minimo = parseInt(minimoInput.value) || 1;
                if (minimo < cantidad) {
                    minimoInput.value = cantidad; // Ajusta silenciosamente
                }
                filterResults();
            });

            elegidoCheckbox.addEventListener('change', () => {
                filterResults();
            });
        });

        function printPage() {
            window.print();
        }

        function filterResults() {
            const rama = document.getElementById('rama-select').value;
            const nivel = document.getElementById('nivel-select').value;
            const ronda = document.getElementById('ronda-select').value;
            const categoria = document.getElementById('categoria-select').value;
            const cantidad = parseInt(document.getElementById('cantidad-input').value) || 1;
            const minimo = parseInt(document.getElementById('minimo-input').value) || 1;
            const elegido = document.getElementById('checkbox-elegidos').checked ? 1 : 0;
            let lugar = 'con-empates';
            if (document.getElementById('checkbox-sin-empates').checked) {
                lugar = 'sin-empates';
            } else if (document.getElementById('checkbox-con-empates-2').checked) {
                lugar = 'con-empates-2';
            }

            // Asegurarse de que minimo no sea menor que cantidad (respaldo)
            if (minimo < cantidad) {
                document.getElementById('minimo-input').value = cantidad;
            }

            window.location.href = `porequipos.php?rama=${rama}&nivel=${nivel}&ronda=${ronda}&categoria=${categoria}&cantidad=${cantidad}&minimo=${minimo}&lugar=${lugar}&elegido=${elegido}`;
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

        // Obtener valores únicos para los filtros
        $sql_ramas = "SELECT DISTINCT rama FROM participante ORDER BY rama";
        $result_ramas = $conn->query($sql_ramas);
        $ramas = [];
        while ($row = $result_ramas->fetch_assoc()) {
            $ramas[] = $row['rama'];
        }

        $sql_niveles = "SELECT DISTINCT nivel FROM categoria ORDER BY nivel";
        $result_niveles = $conn->query($sql_niveles);
        $niveles = [];
        while ($row = $result_niveles->fetch_assoc()) {
            $niveles[] = $row['nivel'];
        }

        $sql_rondas = "SELECT DISTINCT ronda FROM calificacion_ronda ORDER BY ronda";
        $result_rondas = $conn->query($sql_rondas);
        $rondas = [];
        while ($row = $result_rondas->fetch_assoc()) {
            $rondas[] = $row['ronda'];
        }

        $sql_categorias = "SELECT DISTINCT categoria FROM categoria ORDER BY categoria";
        $result_categorias = $conn->query($sql_categorias);
        $categorias = [];
        while ($row = $result_categorias->fetch_assoc()) {
            $categorias[] = $row['categoria'];
        }

        // Filtros desde GET o valores por defecto
        $rama_filter = isset($_GET['rama']) && (in_array($_GET['rama'], $ramas) || $_GET['rama'] === 'todos') ? $_GET['rama'] : 'todos';
        $nivel_filter = isset($_GET['nivel']) && (in_array($_GET['nivel'], $niveles) || $_GET['nivel'] === 'todos') ? $_GET['nivel'] : 'todos';
        $ronda_filter = isset($_GET['ronda']) && (in_array($_GET['ronda'], $rondas) || $_GET['ronda'] === 'todos') ? $_GET['ronda'] : 'todos';
        $categoria_filter = isset($_GET['categoria']) && (in_array($_GET['categoria'], $categorias) || $_GET['categoria'] === 'todos') ? $_GET['categoria'] : 'todos';
        $cantidad_filter = isset($_GET['cantidad']) && is_numeric($_GET['cantidad']) && $_GET['cantidad'] > 0 ? (int)$_GET['cantidad'] : 3;
        $minimo_filter = isset($_GET['minimo']) && is_numeric($_GET['minimo']) && $_GET['minimo'] > 0 ? (int)$_GET['minimo'] : 3;
        $lugar_filter = isset($_GET['lugar']) && in_array($_GET['lugar'], ['con-empates', 'sin-empates', 'con-empates-2']) ? $_GET['lugar'] : 'con-empates-2';
        $elegido_filter = isset($_GET['elegido']) && $_GET['elegido'] == 1 ? 1 : 0;

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
            <div class="row justify-content-center mb-3">
                <div class="col-auto">
                    <label for="rama-select">Rama:</label>
                    <select id="rama-select" class="form-control d-inline-block w-auto me-2" onchange="filterResults()">
                        <option value="todos" <?php echo $rama_filter == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <?php foreach ($ramas as $rama) {
                            $selected = $rama == $rama_filter ? 'selected' : '';
                            echo "<option value='$rama' $selected>$rama</option>";
                        } ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="nivel-select">Nivel:</label>
                    <select id="nivel-select" class="form-control d-inline-block w-auto me-2" onchange="filterResults()">
                        <option value="todos" <?php echo $nivel_filter == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <?php foreach ($niveles as $nivel) {
                            $selected = $nivel == $nivel_filter ? 'selected' : '';
                            echo "<option value='$nivel' $selected>$nivel</option>";
                        } ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="ronda-select">Ronda:</label>
                    <select id="ronda-select" class="form-control d-inline-block w-auto me-2" onchange="filterResults()">
                        <option value="todos" <?php echo $ronda_filter == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <?php foreach ($rondas as $ronda) {
                            $selected = $ronda == $ronda_filter ? 'selected' : '';
                            echo "<option value='$ronda' $selected>$ronda</option>";
                        } ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="categoria-select">Categoría:</label>
                    <select id="categoria-select" class="form-control d-inline-block w-auto me-2" onchange="filterResults()">
                        <option value="todos" <?php echo $categoria_filter == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <?php foreach ($categorias as $categoria) {
                            $selected = $categoria == $categoria_filter ? 'selected' : '';
                            echo "<option value='$categoria' $selected>$categoria</option>";
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <label for="cantidad-input">Cantidad para mostrar:</label>
                    <input type="number" id="cantidad-input" class="form-control d-inline-block w-auto me-2" min="1" value="<?php echo $cantidad_filter; ?>" onchange="filterResults()">
                </div>
                <div class="col-auto">
                    <label for="minimo-input">Mínimo de participantes:</label>
                    <input type="number" id="minimo-input" class="form-control d-inline-block w-auto me-2" min="1" value="<?php echo $minimo_filter; ?>" onchange="filterResults()">
                </div>
                <div class="col-auto align-self-end">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="checkbox-elegidos" <?php echo $elegido_filter == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="checkbox-elegidos">Utilizar solo participantes elegidos previamente</label>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h1 class="text-center mb-4">Resultados por Equipos</h1>

            <?php
            $aparatos = ["salto", "barras", "viga", "piso", "tumbling", "arzones", "anillos", "barras_paralelas", "barra_fija", "circuitos"];

            // Construir cláusula WHERE basada en los filtros
            $where_clauses = [];
            if ($rama_filter !== 'todos') {
                $where_clauses[] = "p.rama = '$rama_filter'";
            }
            if ($nivel_filter !== 'todos') {
                $where_clauses[] = "c.nivel = '$nivel_filter'";
            }
            if ($ronda_filter !== 'todos') {
                $where_clauses[] = "cr.ronda = '$ronda_filter'";
            }
            if ($categoria_filter !== 'todos') {
                $where_clauses[] = "c.categoria = '$categoria_filter'";
            }
            if ($elegido_filter == 1) {
                $where_clauses[] = "p.elegido = 'si'";
            }
            $where_clause = empty($where_clauses) ? "" : "WHERE " . implode(" AND ", $where_clauses);

            // Consulta para obtener datos por club
            $sql = "SELECT cl.id AS club_id, cl.nombre AS club_nombre, c.forma,
                           p.nombre AS participante_nombre,
                           cr.salto, cr.barras, cr.viga, cr.piso, cr.tumbling, cr.arzones, cr.anillos,
                           cr.barras_paralelas, cr.barra_fija, cr.circuitos,
                           SUM(COALESCE(cr.salto, 0) + COALESCE(cr.barras, 0) + COALESCE(cr.viga, 0) + COALESCE(cr.piso, 0) +
                               COALESCE(cr.tumbling, 0) + COALESCE(cr.arzones, 0) + COALESCE(cr.anillos, 0) +
                               COALESCE(cr.barras_paralelas, 0) + COALESCE(cr.barra_fija, 0) + COALESCE(cr.circuitos, 0)) AS all_around
                    FROM calificacion_ronda cr
                    JOIN participante p ON cr.participante_id = p.id
                    JOIN club cl ON p.club_id = cl.id
                    JOIN categoria c ON p.categoria_id = c.id
                    $where_clause
                    GROUP BY p.id, cl.id, cl.nombre, c.forma,
                             cr.salto, cr.barras, cr.viga, cr.piso, cr.tumbling, cr.arzones, cr.anillos,
                             cr.barras_paralelas, cr.barra_fija, cr.circuitos
                    ORDER BY cl.id, all_around DESC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $clubs = [];
                while ($row = $result->fetch_assoc()) {
                    $clubs[$row['club_id']]['nombre'] = $row['club_nombre'];
                    $clubs[$row['club_id']]['forma'] = $row['forma'];
                    $clubs[$row['club_id']]['participantes'][] = [
                        'nombre' => $row['participante_nombre'],
                        'calificaciones' => [
                            'salto' => $row['salto'],
                            'barras' => $row['barras'],
                            'viga' => $row['viga'],
                            'piso' => $row['piso'],
                            'tumbling' => $row['tumbling'],
                            'arzones' => $row['arzones'],
                            'anillos' => $row['anillos'],
                            'barras_paralelas' => $row['barras_paralelas'],
                            'barra_fija' => $row['barra_fija'],
                            'circuitos' => $row['circuitos']
                        ],
                        'all_around' => $row['all_around']
                    ];
                }

                // Filtrar clubes con al menos $minimo_filter participantes
                $clubs = array_filter($clubs, function($club) use ($minimo_filter) {
                    return count($club['participantes']) >= $minimo_filter;
                });

                // Ordenar clubes por suma de las mejores $cantidad_filter calificaciones "All Around"
                $club_totals = [];
                foreach ($clubs as $club_id => $club) {
                    $all_arounds = array_column($club['participantes'], 'all_around');
                    rsort($all_arounds);
                    $top_all_arounds = array_slice($all_arounds, 0, $cantidad_filter);
                    $club_totals[$club_id] = array_sum($top_all_arounds);
                }
                arsort($club_totals);

                // Mostrar resultados por club
                $lugar_club = 1;
                $last_total = null;
                foreach ($club_totals as $club_id => $total) {
                    if ($last_total !== null && $total < $last_total) {
                        $lugar_club++;
                    }
                    $last_total = $total;

                    $club = $clubs[$club_id];
                    echo "<h2 class='text-center mt-5'>Lugar $lugar_club: {$club['nombre']} (Total Equipo: $total)</h2>";

                    // Tablas por aparato
                    foreach ($aparatos as $aparato) {
                        $has_data = false;
                        foreach ($club['participantes'] as $participante) {
                            if ($participante['calificaciones'][$aparato] !== null) {
                                $has_data = true;
                                break;
                            }
                        }

                        if ($has_data) {
                            echo "<h4>Aparato: " . ucfirst(str_replace('_', ' ', $aparato)) . "</h4>";
                            echo "<div class='table-responsive'><table class='table table-bordered'>";
                            echo "<thead><tr>";
                            echo "<th class='con-empates'>Lugar</th>";
                            echo "<th class='sin-empates d-none'>Lugar</th>";
                            echo "<th class='con-empates-2 d-none'>Lugar</th>";
                            echo "<th>Nombre</th><th>Club</th><th>Calificación</th></tr></thead><tbody>";

                            // Ordenar participantes por calificación en este aparato y tomar solo $cantidad_filter
                            usort($club['participantes'], function($a, $b) use ($aparato) {
                                $cal_a = $a['calificaciones'][$aparato] ?? -1;
                                $cal_b = $b['calificaciones'][$aparato] ?? -1;
                                return $cal_b <=> $cal_a;
                            });
                            $limited_participants = array_slice($club['participantes'], 0, $cantidad_filter);

                            $lugar_con_empates = 1;
                            $lugar_sin_empates = 1;
                            $lugar_con_empates_2 = 1;
                            $last_score = null;
                            $current_max_lugar = 0;

                            foreach ($limited_participants as $participante) {
                                $calificacion = $participante['calificaciones'][$aparato];
                                if ($calificacion === null) continue;

                                if ($last_score !== $calificacion) {
                                    $lugar_con_empates = $lugar_sin_empates;
                                    $lugar_con_empates_2 = $current_max_lugar + 1;
                                    $last_score = $calificacion;
                                }

                                echo "<tr>";
                                echo "<td class='con-empates'>$lugar_con_empates</td>";
                                echo "<td class='sin-empates d-none'>$lugar_sin_empates</td>";
                                echo "<td class='con-empates-2 d-none'>$lugar_con_empates_2</td>";
                                echo "<td>{$participante['nombre']}</td>";
                                echo "<td>{$club['nombre']}</td>";
                                echo "<td>$calificacion</td>";
                                echo "</tr>";

                                $lugar_sin_empates++;
                                $current_max_lugar = max($current_max_lugar, $lugar_con_empates_2);
                            }

                            echo "</tbody></table></div>";
                        }
                    }

                    // Tabla All Around
                    echo "<h4>All Around</h4>";
                    echo "<div class='table-responsive'><table class='table table-bordered'>";
                    echo "<thead><tr>";
                    echo "<th class='con-empates'>Lugar</th>";
                    echo "<th class='sin-empates d-none'>Lugar</th>";
                    echo "<th class='con-empates-2 d-none'>Lugar</th>";
                    echo "<th>Nombre</th><th>Club</th><th>Total</th></tr></thead><tbody>";

                    // Ordenar participantes por All Around y tomar solo $cantidad_filter
                    usort($club['participantes'], function($a, $b) {
                        return $b['all_around'] <=> $a['all_around'];
                    });
                    $limited_participants = array_slice($club['participantes'], 0, $cantidad_filter);

                    $lugar_con_empates = 1;
                    $lugar_sin_empates = 1;
                    $lugar_con_empates_2 = 1;
                    $last_total_part = null;
                    $current_max_lugar = 0;

                    foreach ($limited_participants as $participante) {
                        $total = $participante['all_around'];
                        if ($last_total_part !== $total) {
                            $lugar_con_empates = $lugar_sin_empates;
                            $lugar_con_empates_2 = $current_max_lugar + 1;
                            $last_total_part = $total;
                        }

                        echo "<tr>";
                        echo "<td class='con-empates'>$lugar_con_empates</td>";
                        echo "<td class='sin-empates d-none'>$lugar_sin_empates</td>";
                        echo "<td class='con-empates-2 d-none'>$lugar_con_empates_2</td>";
                        echo "<td>{$participante['nombre']}</td>";
                        echo "<td>{$club['nombre']}</td>";
                        echo "<td>$total</td>";
                        echo "</tr>";

                        $lugar_sin_empates++;
                        $current_max_lugar = max($current_max_lugar, $lugar_con_empates_2);
                    }

                    echo "</tbody></table></div>";
                }
            } else {
                echo "<p class='text-center'>No hay datos disponibles con los filtros seleccionados.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>