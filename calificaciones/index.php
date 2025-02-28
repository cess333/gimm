<?php
include('../aparatos/navbar.php');
include('../conexion.php');

// Verificar si las calificaciones están abiertas o cerradas
$sql_estado = "SELECT calificaciones_abiertas FROM configuracion LIMIT 1";
$estado_result = $conn->query($sql_estado);
$estado_data = $estado_result->fetch_assoc();
$calificaciones_abiertas = $estado_data['calificaciones_abiertas'] ?? 1;

// Obtener la ronda seleccionada desde GET (por defecto 0 para calificacion)
$ronda_seleccionada = isset($_GET['ronda']) ? (int)$_GET['ronda'] : 0;

// Obtener las rondas existentes en calificacion_ronda
$sql_rondas = "SELECT DISTINCT ronda FROM calificacion_ronda ORDER BY ronda";
$rondas_result = $conn->query($sql_rondas);
$rondas = [];
while ($row = $rondas_result->fetch_assoc()) {
    $rondas[] = $row['ronda'];
}
$ronda_maxima = !empty($rondas) ? max($rondas) : 0;

// Contar el total de participantes y los que no tienen calificaciones completas
$total_participantes_sql = "SELECT COUNT(*) as total FROM participante";
$total_participantes_result = $conn->query($total_participantes_sql);
$total_participantes = $total_participantes_result->fetch_assoc()['total'];

// Contar participantes sin calificaciones completas, basado solo en calificacion_ronda
$sin_calificaciones_sql = "
    SELECT COUNT(DISTINCT p.id) as sin_calificaciones
    FROM participante p
    LEFT JOIN calificacion_ronda c ON p.id = c.participante_id
    JOIN categoria cat ON p.categoria_id = cat.id
    WHERE (
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
    )";
$sin_calificaciones_result = $conn->query($sin_calificaciones_sql);
$participantes_sin_calificaciones = $sin_calificaciones_result->fetch_assoc()['sin_calificaciones'];

// Consulta dinámica basada en la ronda seleccionada
if ($ronda_seleccionada == 0) {
    $sql = "
        SELECT p.id AS participante_id, p.nombre AS participante, 
               c.salto, c.barras, c.viga, c.piso, c.tumbling, 
               c.arzones, c.anillos, c.barras_paralelas, c.barra_fija, c.circuitos, 
               c.panel, c.ronda,
               cat.aparato_salto, cat.aparato_barras, cat.aparato_viga, cat.aparato_piso, 
               cat.aparato_tumbling, cat.aparato_arzones, cat.aparato_anillos, 
               cat.aparato_barras_paralelas, cat.aparato_barra_fija, cat.aparato_circuitos
        FROM calificacion c
        JOIN participante p ON c.participante_id = p.id
        JOIN categoria cat ON p.categoria_id = cat.id
    ";
    $missing_sql = "
        SELECT 
            SUM(CASE WHEN (cat.aparato_salto = 1 AND c.salto IS NULL) THEN 1 ELSE 0 END) AS faltan_salto,
            SUM(CASE WHEN (cat.aparato_barras = 1 AND c.barras IS NULL) THEN 1 ELSE 0 END) AS faltan_barras,
            SUM(CASE WHEN (cat.aparato_viga = 1 AND c.viga IS NULL) THEN 1 ELSE 0 END) AS faltan_viga,
            SUM(CASE WHEN (cat.aparato_piso = 1 AND c.piso IS NULL) THEN 1 ELSE 0 END) AS faltan_piso,
            SUM(CASE WHEN (cat.aparato_tumbling = 1 AND c.tumbling IS NULL) THEN 1 ELSE 0 END) AS faltan_tumbling,
            SUM(CASE WHEN (cat.aparato_arzones = 1 AND c.arzones IS NULL) THEN 1 ELSE 0 END) AS faltan_arzones,
            SUM(CASE WHEN (cat.aparato_anillos = 1 AND c.anillos IS NULL) THEN 1 ELSE 0 END) AS faltan_anillos,
            SUM(CASE WHEN (cat.aparato_barras_paralelas = 1 AND c.barras_paralelas IS NULL) THEN 1 ELSE 0 END) AS faltan_barras_paralelas,
            SUM(CASE WHEN (cat.aparato_barra_fija = 1 AND c.barra_fija IS NULL) THEN 1 ELSE 0 END) AS faltan_barra_fija,
            SUM(CASE WHEN (cat.aparato_circuitos = 1 AND c.circuitos IS NULL) THEN 1 ELSE 0 END) AS faltan_circuitos,
            SUM(CASE WHEN (cat.aparato_salto = 1 AND c.panel IS NULL) THEN 1 ELSE 0 END) AS faltan_panel
        FROM calificacion c
        JOIN participante p ON c.participante_id = p.id
        JOIN categoria cat ON p.categoria_id = cat.id
    ";
} else {
    $sql = "
        SELECT p.id AS participante_id, p.nombre AS participante, 
               c.salto, c.barras, c.viga, c.piso, c.tumbling, 
               c.arzones, c.anillos, c.barras_paralelas, c.barra_fija, c.circuitos, 
               c.panel, c.ronda,
               cat.aparato_salto, cat.aparato_barras, cat.aparato_viga, cat.aparato_piso, 
               cat.aparato_tumbling, cat.aparato_arzones, cat.aparato_anillos, 
               cat.aparato_barras_paralelas, cat.aparato_barra_fija, cat.aparato_circuitos
        FROM calificacion_ronda c
        JOIN participante p ON c.participante_id = p.id
        JOIN categoria cat ON p.categoria_id = cat.id
        WHERE c.ronda = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ronda_seleccionada);
    $stmt->execute();
    $result = $stmt->get_result();

    $missing_sql = "
        SELECT 
            SUM(CASE WHEN (cat.aparato_salto = 1 AND c.salto IS NULL) THEN 1 ELSE 0 END) AS faltan_salto,
            SUM(CASE WHEN (cat.aparato_barras = 1 AND c.barras IS NULL) THEN 1 ELSE 0 END) AS faltan_barras,
            SUM(CASE WHEN (cat.aparato_viga = 1 AND c.viga IS NULL) THEN 1 ELSE 0 END) AS faltan_viga,
            SUM(CASE WHEN (cat.aparato_piso = 1 AND c.piso IS NULL) THEN 1 ELSE 0 END) AS faltan_piso,
            SUM(CASE WHEN (cat.aparato_tumbling = 1 AND c.tumbling IS NULL) THEN 1 ELSE 0 END) AS faltan_tumbling,
            SUM(CASE WHEN (cat.aparato_arzones = 1 AND c.arzones IS NULL) THEN 1 ELSE 0 END) AS faltan_arzones,
            SUM(CASE WHEN (cat.aparato_anillos = 1 AND c.anillos IS NULL) THEN 1 ELSE 0 END) AS faltan_anillos,
            SUM(CASE WHEN (cat.aparato_barras_paralelas = 1 AND c.barras_paralelas IS NULL) THEN 1 ELSE 0 END) AS faltan_barras_paralelas,
            SUM(CASE WHEN (cat.aparato_barra_fija = 1 AND c.barra_fija IS NULL) THEN 1 ELSE 0 END) AS faltan_barra_fija,
            SUM(CASE WHEN (cat.aparato_circuitos = 1 AND c.circuitos IS NULL) THEN 1 ELSE 0 END) AS faltan_circuitos,
            SUM(CASE WHEN (cat.aparato_salto = 1 AND c.panel IS NULL) THEN 1 ELSE 0 END) AS faltan_panel
        FROM calificacion_ronda c
        JOIN participante p ON c.participante_id = p.id
        JOIN categoria cat ON p.categoria_id = cat.id
        WHERE c.ronda = ?
    ";
    $missing_stmt = $conn->prepare($missing_sql);
    $missing_stmt->bind_param("i", $ronda_seleccionada);
    $missing_stmt->execute();
    $missing_result = $missing_stmt->get_result();
}
if ($ronda_seleccionada == 0) {
    $result = $conn->query($sql);
}

// Verificar si la consulta SQL fue exitosa
if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}

if ($ronda_seleccionada == 0) {
    $missing_result = $conn->query($missing_sql);
}

// Verificar si la consulta SQL de contadores fue exitosa
if (!$missing_result) {
    die("Error en la consulta SQL para contadores: " . $conn->error);
}

$missing_data = $missing_result->fetch_assoc();

// Obtener el número de participantes
$participantes_count = $result->num_rows;

// Determinar si hay calificaciones faltantes o no hay participantes para habilitar "Cerrar Ronda"
$calificaciones_completas = $participantes_count > 0;
foreach ($missing_data as $faltan) {
    if ($faltan > 0) {
        $calificaciones_completas = false;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <style type="text/css">
        .disabled {
            background-color: #d3d3d3;
            color: #6c757d;
            pointer-events: none;
        }
        #countdown {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6200; /* Naranja brillante */
            display: inline-block;
            padding: 0.5rem;
            border-radius: 50%;
            background-color: rgba(255, 98, 0, 0.1);
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .pulse-animation {
            animation: pulse 1s infinite;
        }
    </style>
</head>
<body>
    <div style="margin: 0 20px;">
        <h2>Calificaciones de los Participantes</h2>

        <button id="toggle-calificaciones" class="btn <?php echo $calificaciones_abiertas ? 'btn-success' : 'btn-danger'; ?>">
            <?php echo $calificaciones_abiertas ? "🔓 Calificaciones Abiertas" : "🔒 Calificaciones Cerradas"; ?>
        </button>

        <button id="buscar-participantes" class="btn btn-primary" style="margin-left: 10px;">
            🔍 Buscar Participantes
        </button>

        <button id="exportar-excel" class="btn btn-success" style="margin-left: 10px;" onclick="exportarExcel(<?php echo $ronda_seleccionada; ?>)">
            📊 Exportar a Excel
        </button>

        <?php if ($ronda_seleccionada == 0) { ?>
            <button id="cerrar-ronda" class="btn btn-warning" style="margin-left: 10px;" <?php echo $calificaciones_completas ? '' : 'disabled'; ?>>
                🚧 Cerrar Ronda
            </button>
        <?php } ?>

        <?php if ($ronda_seleccionada != 0) { ?>
            <button id="convertir-ronda-0" class="btn btn-info" style="margin-left: 10px;" <?php echo $ronda_seleccionada == $ronda_maxima ? '' : 'disabled'; ?>>
                📤 Convertir Ronda Actual a Ronda en Proceso
            </button>
        <?php } ?>

        <button id="limpiar-rondas" class="btn btn-danger" style="margin-left: 10px; float: right;">
            🧹 Limpiar Rondas
        </button>

        <div style="margin: 20px 0;">
            <label for="select-ronda">Seleccionar Ronda: </label>
            <select id="select-ronda" name="ronda" class="form-select" style="display: inline-block; width: auto;" onchange="cambiarRonda(this.value)">
                <option value="0" <?php echo $ronda_seleccionada == 0 ? 'selected' : ''; ?>>Ronda en Proceso</option>
                <?php foreach ($rondas as $ronda) { ?>
                    <option value="<?php echo $ronda; ?>" <?php echo $ronda_seleccionada == $ronda ? 'selected' : ''; ?>>
                        Ronda <?php echo $ronda; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="modal fade" id="limpiarRondasModal" tabindex="-1" aria-labelledby="limpiarRondasModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="limpiarRondasModalLabel">Confirmar Limpieza de Rondas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¡ATENCIÓN! Estás a punto de eliminar TODAS las calificaciones de las rondas pasadas. Esta acción es irreversible.</p>
                        <p>Por favor, espera <span id="countdown" class="pulse-animation">10</span> segundos para confirmar.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="confirmar-limpiar" class="btn btn-danger" disabled>Eliminar Todo</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nuevo contador de participantes totales y sin calificaciones (basado solo en calificacion_ronda) -->
        <div style="margin: 20px 0;">
            <h4>Participantes Registrados: 
                <span class="badge bg-primary text-white" style="font-weight: bold;">
                    <?php echo $total_participantes; ?>
                </span> | 
                Participantes sin Ronda Asignada: 
                <span class="badge bg-danger text-white" style="font-weight: bold;">
                    <?php echo $participantes_sin_calificaciones; ?>
                </span>
            </h4>
        </div>

        <div style="margin: 20px 0;">
            <h4>Partcipantes para la ronda <?php echo $ronda_seleccionada == 0 ? 'en proceso' : $ronda_seleccionada; ?>: 
                <span class="badge bg-info text-white" style="font-weight: bold;">
                    <?php echo $participantes_count; ?>
                </span>
            </h4>
        </div>

        <div class="missing-counter">
            <h4>Calificaciones faltantes</h4>
            <div class="row">
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_salto"><?php echo $missing_data['faltan_salto']; ?></span> Salto
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_barras"><?php echo $missing_data['faltan_barras']; ?></span> Barras
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_viga"><?php echo $missing_data['faltan_viga']; ?></span> Viga
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_piso"><?php echo $missing_data['faltan_piso']; ?></span> Piso
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_tumbling"><?php echo $missing_data['faltan_tumbling']; ?></span> Tumbling
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_arzones"><?php echo $missing_data['faltan_arzones']; ?></span> Arzones
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_anillos"><?php echo $missing_data['faltan_anillos']; ?></span> Anillos
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_barras_paralelas"><?php echo $missing_data['faltan_barras_paralelas']; ?></span> Barras Paralelas
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_barra_fija"><?php echo $missing_data['faltan_barra_fija']; ?></span> Barra Fija
                </div>
                <div class="col-md-2">
                    <span class="badge bg-warning" id="faltan_circuitos"><?php echo $missing_data['faltan_circuitos']; ?></span> Circuitos
                </div>
            </div>
        </div>

        <table id="calificacionesTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Participante</th>
                    <th>Nombre Participante</th>
                    <th>Salto</th>
                    <th>Barras</th>
                    <th>Viga</th>
                    <th>Piso</th>
                    <th>Tumbling</th>
                    <th>Arzones</th>
                    <th>Anillos</th>
                    <th>Barras Paralelas</th>
                    <th>Barra Fija</th>
                    <th>Circuitos</th>
                    <th>Panel</th>
                    <th>Ronda</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['participante_id']; ?></td>
                        <td><?php echo $row['participante']; ?></td>
                        <td <?php echo $row['aparato_salto'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="salto"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['salto']) ? $row['salto'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_barras'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="barras"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['barras']) ? $row['barras'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_viga'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="viga"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['viga']) ? $row['viga'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_piso'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="piso"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['piso']) ? $row['piso'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_tumbling'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="tumbling"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['tumbling']) ? $row['tumbling'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_arzones'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="arzones"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['arzones']) ? $row['arzones'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_anillos'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="anillos"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['anillos']) ? $row['anillos'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_barras_paralelas'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="barras_paralelas"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['barras_paralelas']) ? $row['barras_paralelas'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_barra_fija'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="barra_fija"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['barra_fija']) ? $row['barra_fija'] : ''; ?>
                        </td>
                        <td <?php echo $row['aparato_circuitos'] == 1 ? 'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="circuitos"' : 'class="disabled"'; ?>>
                            <?php echo isset($row['circuitos']) ? $row['circuitos'] : ''; ?>
                        </td>
                        <td contenteditable="true" class="edit" data-id="<?php echo $row['participante_id']; ?>" data-column="panel">
                            <?php echo isset($row['panel']) ? $row['panel'] : ''; ?>
                        </td>
                        <td contenteditable="true" class="edit" data-id="<?php echo $row['participante_id']; ?>" data-column="ronda">
                            <?php echo isset($row['ronda']) ? $row['ronda'] : ''; ?>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-row" data-id="<?php echo $row['participante_id']; ?>" data-ronda="<?php echo $ronda_seleccionada; ?>">
                                🗑️ Eliminar
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#calificacionesTable').DataTable();

            $(document).off('keydown', '.edit').on('keydown', '.edit', function(e) {
                var id = $(this).data('id');
                var column = $(this).data('column');
                var value = $(this).text().trim();
                var rondaSeleccionada = '<?php echo $ronda_seleccionada; ?>';

                if (e.keyCode === 13) {
                    e.preventDefault();

                    if (column === 'ronda') {
                        if (value === '' || isNaN(value) || parseInt(value) <= 0) {
                            alert('El valor de la ronda debe ser un número mayor a 1.');
                            $(this).text(rondaSeleccionada || '');
                            return;
                        }
                    }

                    if (value === '') value = null;

                    $.ajax({
                        url: 'update_calificacion.php?ronda=' + rondaSeleccionada,
                        type: 'POST',
                        data: {id: id, column: column, value: value},
                        success: function(response) {
                            alert('Calificación actualizada correctamente.');
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error: ' + error);
                        }
                    });
                }
            });

            $(document).off('click', '.delete-row').on('click', '.delete-row', function(e) {
                e.preventDefault();
                var participante_id = $(this).data('id');
                var ronda = $(this).data('ronda');
                var $row = $(this).closest('tr');

                if (confirm('¿Estás seguro de eliminar este participante y todas sus calificaciones? Esta acción no se puede deshacer.')) {
                    $.ajax({
                        url: 'delete_participante.php',
                        type: 'POST',
                        data: {
                            id: participante_id,
                            ronda: ronda
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                alert('Participante eliminado correctamente');
                                table.row($row).remove().draw();
                                var currentCount = parseInt($('.badge.bg-info').text());
                                $('.badge.bg-info').text(currentCount - 1);
                            } else {
                                alert('Error al eliminar: ' + (data.error || 'Error desconocido'));
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error en la solicitud: ' + xhr.responseText);
                        }
                    });
                }
            });

            var selectRonda = document.getElementById('select-ronda');
            var cerrarRondaBtn = document.getElementById('cerrar-ronda');
            var convertirRonda0Btn = document.getElementById('convertir-ronda-0');
            var rondaMaxima = <?php echo $ronda_maxima; ?>;

            function actualizarBotones() {
                var ronda = parseInt(selectRonda.value);
                if (ronda === 0) {
                    if (cerrarRondaBtn) cerrarRondaBtn.style.display = 'inline-block';
                    if (convertirRonda0Btn) convertirRonda0Btn.style.display = 'none';
                } else {
                    if (cerrarRondaBtn) cerrarRondaBtn.style.display = 'none';
                    if (convertirRonda0Btn) {
                        convertirRonda0Btn.style.display = 'inline-block';
                        convertirRonda0Btn.disabled = (ronda !== rondaMaxima);
                    }
                }
            }
            actualizarBotones();
            selectRonda.addEventListener('change', actualizarBotones);

            function verificarCalificaciones() {
                if (cerrarRondaBtn) {
                    var participantes = <?php echo $participantes_count; ?>;
                    var faltantes = [
                        parseInt($('#faltan_salto').text()),
                        parseInt($('#faltan_barras').text()),
                        parseInt($('#faltan_viga').text()),
                        parseInt($('#faltan_piso').text()),
                        parseInt($('#faltan_tumbling').text()),
                        parseInt($('#faltan_arzones').text()),
                        parseInt($('#faltan_anillos').text()),
                        parseInt($('#faltan_barras_paralelas').text()),
                        parseInt($('#faltan_barra_fija').text()),
                        parseInt($('#faltan_circuitos').text())
                    ];
                    var totalFaltantes = faltantes.reduce((a, b) => a + b, 0);
                    cerrarRondaBtn.disabled = (participantes === 0 || totalFaltantes > 0);
                }
            }
            verificarCalificaciones();
            setInterval(verificarCalificaciones, 5000);
        });
    </script>

    <script>
        document.getElementById('toggle-calificaciones').addEventListener('click', function () {
            fetch('toggle_calificaciones.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let btn = document.getElementById('toggle-calificaciones');
                    if (data.estado) {
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-success');
                        btn.innerHTML = "🔓 Calificaciones Abiertas";
                    } else {
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-danger');
                        btn.innerHTML = "🔒 Calificaciones Cerradas";
                    }
                    alert(data.estado ? "Calificaciones abiertas" : "Calificaciones cerradas");
                } else {
                    alert("Error al cambiar el estado.");
                }
            })
            .catch(console.error);
        });
    </script>

    <script>
        if (document.getElementById('cerrar-ronda')) {
            document.getElementById('cerrar-ronda').addEventListener('click', function () {
                if (confirm('¿Estás seguro de cerrar la ronda? Esto transferirá las calificaciones a calificacion_ronda.')) {
                    fetch('cerrar_ronda.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Ronda cerrada. Calificaciones transferidas a ronda ' + data.nueva_ronda);
                            location.reload();
                        } else {
                            alert('Error al cerrar la ronda: ' + data.error);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }

        if (document.getElementById('convertir-ronda-0')) {
            document.getElementById('convertir-ronda-0').addEventListener('click', function () {
                var rondaActual = document.getElementById('select-ronda').value;
                if (rondaActual == 0) {
                    alert('Por favor, selecciona una ronda distinta a Ronda en Proceso para convertir.');
                    return;
                }
                if (confirm('¿Estás seguro de convertir la ronda ' + rondaActual + ' a Ronda en Proceso? Esto moverá las calificaciones a la tabla actual si está vacía.')) {
                    fetch('convertir_ronda_0.php?ronda=' + rondaActual)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Ronda ' + rondaActual + ' convertida a Ronda en Proceso exitosamente.');
                            window.location.href = '?ronda=0';
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
    </script>

    <script>
        function cambiarRonda(ronda) {
            window.location.href = '?ronda=' + ronda;
        }
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $.ajax({
                    url: 'get_calificaciones.php?ronda=<?php echo $ronda_seleccionada; ?>',
                    type: 'GET',
                    success: function(response) {
                        $(response).each(function() {
                            var updatedCell = $(this);
                            var id = updatedCell.data('id');
                            var column = updatedCell.data('column');
                            $('td[data-id="' + id + '"][data-column="' + column + '"]').html(updatedCell.html());
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error al obtener las calificaciones: ' + error);
                    }
                });
            }, 19000);
        });
    </script>

    <script>
        $(document).ready(function() {
            setInterval(function() {
                $.ajax({
                    url: 'get_missing_counts.php?ronda=<?php echo $ronda_seleccionada; ?>',
                    type: 'GET',
                    success: function(response) {
                        var missingData = JSON.parse(response);
                        $('#faltan_salto').text(missingData.faltan_salto);
                        $('#faltan_barras').text(missingData.faltan_barras);
                        $('#faltan_viga').text(missingData.faltan_viga);
                        $('#faltan_piso').text(missingData.faltan_piso);
                        $('#faltan_tumbling').text(missingData.faltan_tumbling);
                        $('#faltan_arzones').text(missingData.faltan_arzones);
                        $('#faltan_anillos').text(missingData.faltan_anillos);
                        $('#faltan_barras_paralelas').text(missingData.faltan_barras_paralelas);
                        $('#faltan_barra_fija').text(missingData.faltan_barra_fija);
                        $('#faltan_circuitos').text(missingData.faltan_circuitos);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error al obtener los contadores: ' + error);
                    }
                });
            }, 5000);
        });
    </script>

    <script>
        document.getElementById('buscar-participantes').addEventListener('click', function () {
            location.reload();
        });
    </script>

    <script>
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>

    <script>
        var rondaMaxima = <?php echo json_encode($ronda_maxima); ?>;
    </script>

    <script>
        function exportarExcel(ronda) {
            window.location.href = 'exportar_excel.php?ronda=' + ronda;
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#limpiar-rondas').on('click', function() {
                $('#limpiarRondasModal').modal('show');
                
                var countdown = 10;
                $('#countdown').text(countdown);
                $('#countdown').addClass('pulse-animation');
                $('#confirmar-limpiar').prop('disabled', true);

                var timer = setInterval(function() {
                    countdown--;
                    $('#countdown').text(countdown);
                    if (countdown <= 0) {
                        clearInterval(timer);
                        $('#confirmar-limpiar').prop('disabled', false);
                        $('#countdown').removeClass('pulse-animation');
                        $('#countdown').css('color', '#28a745');
                    }
                }, 1000);
            });

            $('#confirmar-limpiar').on('click', function() {
                $.ajax({
                    url: 'limpiar_rondas.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            alert('Todas las rondas pasadas han sido eliminadas correctamente.');
                            $('#limpiarRondasModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error al limpiar las rondas: ' + (data.error || 'Error desconocido'));
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la solicitud: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>