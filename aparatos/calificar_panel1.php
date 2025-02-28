<?php
include '../conexion.php';

$aparato = isset($_GET['aparato']) ? $_GET['aparato'] : null;
$panel = isset($_GET['panel']) ? $_GET['panel'] : 'Panel 1';
$judge_count = isset($_GET['jueces']) ? intval($_GET['jueces']) : 3; // Número de jueces por defecto

$aparatos_validos = ['salto', 'barras', 'viga', 'piso', 'tumbling', 'arzones', 'anillos', 'barras-paralelas', 'barra-fija', 'circuitos'];
if (!in_array($aparato, $aparatos_validos)) {
    die("Aparato no válido.");
}

$mensaje = "";
if (isset($_POST['calificar']) && isset($_POST['notas']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $notas = json_decode($_POST['notas'], true); // Array con ND y DED por juez
    $neutrals = floatval($_POST['neutrals'] ?? 0);
    $bonus = floatval($_POST['bonus'] ?? 0);

    $sql_estado = "SELECT calificaciones_abiertas FROM configuracion LIMIT 1";
    $estado_result = $conn->query($sql_estado);
    $estado_data = $estado_result->fetch_assoc();
    $calificaciones_abiertas = $estado_data['calificaciones_abiertas'] ?? 1;

    if (!$calificaciones_abiertas) {
        $mensaje = "Error: Las calificaciones están cerradas.";
    } else {
        $sql_ronda_check = "SELECT id FROM calificacion_ronda WHERE participante_id = ? AND $aparato IS NOT NULL";
        $stmt_ronda_check = $conn->prepare($sql_ronda_check);
        $stmt_ronda_check->bind_param("i", $id);
        $stmt_ronda_check->execute();
        $ronda_result = $stmt_ronda_check->get_result();

        if ($ronda_result->num_rows > 0) {
            $mensaje = "Error: Este participante ya tiene una calificación final en esta ronda.";
        } else {
            $pre_scores = [];
            foreach ($notas as $juez => $data) {
                $nd = floatval($data['ND']);
                $ded = floatval($data['DED']);
                $pre_scores[$juez] = $nd - $ded; // Cálculo de PRE
            }

            // Cálculo del promedio según el número de jueces
            $pre_count = count($pre_scores);
            if ($pre_count <= 3) {
                $promedio = array_sum($pre_scores) / $pre_count;
            } else {
                $sorted_pre = $pre_scores;
                sort($sorted_pre);
                array_shift($sorted_pre); // Quitar el menor
                array_pop($sorted_pre);   // Quitar el mayor
                $promedio = array_sum($sorted_pre) / count($sorted_pre);
            }

            $nota_final = $promedio - $neutrals + $bonus;

            $sql_check = "SELECT id FROM calificacion WHERE participante_id = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            $notas_json = json_encode($notas);
            if ($result_check->num_rows > 0) {
                $sql_update = "UPDATE calificacion SET $aparato = ?, notas_json = ?, panel = ? WHERE participante_id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("dssi", $nota_final, $notas_json, $panel, $id);
                if ($stmt_update->execute()) {
                    $mensaje = "Calificación actualizada correctamente.";
                } else {
                    $mensaje = "Error al actualizar: " . $stmt_update->error;
                }
                $stmt_update->close();
            } else {
                $sql_insert = "INSERT INTO calificacion (participante_id, $aparato, notas_json, panel) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("idss", $id, $nota_final, $notas_json, $panel);
                if ($stmt_insert->execute()) {
                    $mensaje = "Calificación asignada correctamente.";
                } else {
                    $mensaje = "Error al asignar: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            }
            $stmt_check->close();
        }
        $stmt_ronda_check->close();
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "
        SELECT p.id, p.nombre, p.ano_nacimiento AS edad, p.rama AS genero, 
        c.$aparato AS calificacion_actual, c.notas_json AS notas_json, c.panel AS panel_actual,
        cl.nombre AS nombre_del_club, cat.nivel AS nivel, cat.categoria AS categoria, 
        cat.max AS max_calificacion, cat.aparato_$aparato AS habilitado,
        cancion.nombre AS cancion_nombre, cancion.ruta AS cancion_ruta,
        cp.nombre AS cancion_predeterminada_nombre, cp.ruta AS cancion_predeterminada_ruta,
        cr.$aparato AS calificacion_ronda
        FROM participante p
        LEFT JOIN calificacion c ON p.id = c.participante_id
        LEFT JOIN calificacion_ronda cr ON p.id = cr.participante_id
        LEFT JOIN club cl ON p.club_id = cl.id
        LEFT JOIN categoria cat ON p.categoria_id = cat.id
        LEFT JOIN canciones cancion ON p.cancion_id = cancion.id
        LEFT JOIN canciones_predeterminadas cp ON 1=1
        WHERE p.id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $participante = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($participante);
    } else {
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    $stmt->close();
    $conn->close();
    exit;
}

$conn->close();
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificación de Jueces - <?php echo ucfirst($aparato); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            color: white;
            margin-right: 10px;
        }
        .status-open { background-color: green; }
        .status-closed { background-color: red; }
        .status-error { background-color: yellow; color: black; }
        .status-container {
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .judge-inputs { margin-bottom: 15px; }
        .alert { text-align: center; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">
            Calificación de <?php echo strtoupper($aparato); ?> - <?php echo htmlspecialchars($panel); ?> (<?php echo $judge_count; ?> Jueces)
        </h2>

        <div id="estado-calificaciones" class="status-container mb-4">
            <span class="status-circle" id="status-circle"></span>
            <span>Estado de las calificaciones:</span>
            <span id="status-text"></span>
        </div>
        <?php if ($mensaje): ?>
            <div id="mensaje" class="alert alert-info"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form id="form-busqueda" class="mb-4">
            <div class="form-group">
                <label for="participante_id" class="form-label">ID Participante</label>
                <input type="number" name="participante_id" id="participante_id" class="form-control" placeholder="Ingrese ID del participante" required>
            </div>
        </form>

        <div id="participante-info" class="card mb-4" style="display: none;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5 class="card-title">Datos del Participante</h5>
                        <p><strong>Nombre:</strong> <span id="nombre"></span></p>
                        <p><strong>Nivel:</strong> <span id="nivel"></span></p>
                        <p><strong>Categoría:</strong> <span id="categoria"></span></p>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">Calificación Final</h5>
                        <p><span id="calificacion_actual" style="font-size: 36px; font-weight: bold"></span></p>
                        <button id="cambiar-calificacion" class="btn btn-warning" style="display: none;">Cambiar Calificación</button>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">Música</h5>
                        <p><strong>Canción Asociada:</strong> <span id="cancion-nombre">No asignada</span></p>
                        <div id="reproductor"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="error-message" class="alert alert-danger text-center" style="display: none;"></div>

        <form method="POST" id="form-calificacion" style="display: none;">
            <input type="hidden" name="id" id="id-participante">
            <div id="judge-inputs" class="mb-3">
                <?php for ($i = 1; $i <= $judge_count; $i++): ?>
                    <div class="judge-inputs row">
                        <h5>Juez <?php echo $i; ?></h5>
                        <div class="col">
                            <label for="nd_<?php echo $i; ?>">Nota de Partida (ND)</label>
                            <input type="number" step="0.1" min="0" name="nd_<?php echo $i; ?>" id="nd_<?php echo $i; ?>" class="form-control judge-nd" required>
                        </div>
                        <div class="col">
                            <label for="ded_<?php echo $i; ?>">Deducciones (DED)</label>
                            <input type="number" step="0.1" min="0" name="ded_<?php echo $i; ?>" id="ded_<?php echo $i; ?>" class="form-control" required>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="neutrals">Neutrales</label>
                    <input type="number" step="0.1" min="0" name="neutrals" id="neutrals" class="form-control" value="0">
                </div>
                <div class="col">
                    <label for="bonus">Bonificaciones (+)</label>
                    <input type="number" step="0.1" min="0" name="bonus" id="bonus" class="form-control" value="0">
                </div>
            </div>
            <button type="submit" name="calificar" class="btn btn-success w-100 mt-3">Guardar Calificación</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const participanteInput = document.getElementById('participante_id');
            participanteInput.focus();

            function checkCalificacionesEstado() {
                fetch('check_status.php')
                    .then(response => response.json())
                    .then(data => {
                        const statusCircle = document.getElementById('status-circle');
                        const statusText = document.getElementById('status-text');
                        if (data.calificaciones_abiertas === 0) {
                            statusCircle.className = 'status-circle status-closed';
                            statusCircle.innerText = '🔒';
                            statusText.innerText = 'cerradas';
                            document.getElementById('form-calificacion').style.display = 'none';
                            document.getElementById('cambiar-calificacion').style.display = 'none';
                        } else {
                            statusCircle.className = 'status-circle status-open';
                            statusCircle.innerText = '🔓';
                            statusText.innerText = 'abiertas';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const statusCircle = document.getElementById('status-circle');
                        statusCircle.className = 'status-circle status-error';
                        statusCircle.innerText = '!';
                        statusText.innerText = 'error';
                    });
            }

            checkCalificacionesEstado();
            setInterval(checkCalificacionesEstado, 2000);

            participanteInput.addEventListener('input', function () {
                const id = this.value;
                if (id) {
                    fetch('?aparato=<?php echo $aparato; ?>&id=' + id + '&jueces=<?php echo $judge_count; ?>')
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('nombre').innerText = data.nombre || 'No asignado';
                                document.getElementById('nivel').innerText = data.nivel || 'No asignado';
                                document.getElementById('categoria').innerText = data.categoria || 'No asignado';
                                document.getElementById('calificacion_actual').innerText = data.calificacion_actual !== null ? data.calificacion_actual : 'No asignada';
                                document.getElementById('id-participante').value = data.id;

                                const cancionNombre = data.cancion_nombre || 'No asignada';
                                const cancionRuta = data.cancion_ruta || '';
                                document.getElementById('cancion-nombre').innerText = cancionNombre;
                                document.getElementById('reproductor').innerHTML = cancionRuta ? 
                                    `<audio controls><source src="${cancionRuta}" type="audio/mp3">Tu navegador no soporta el reproductor.</audio>` : 
                                    'No hay canción asociada.';

                                if (!data.habilitado) {
                                    document.getElementById('error-message').innerText = 'El aparato no está habilitado para este participante.';
                                    document.getElementById('error-message').style.display = 'block';
                                    document.getElementById('form-calificacion').style.display = 'none';
                                    document.getElementById('cambiar-calificacion').style.display = 'none';
                                } else if (data.calificacion_ronda !== null) {
                                    document.getElementById('error-message').innerText = 'Este participante ya tiene una calificación en una ronda cerrada.';
                                    document.getElementById('error-message').style.display = 'block';
                                    document.getElementById('form-calificacion').style.display = 'none';
                                    document.getElementById('cambiar-calificacion').style.display = 'none';
                                } else {
                                    document.getElementById('participante-info').style.display = 'block';
                                    document.getElementById('error-message').style.display = 'none';
                                    if (data.calificacion_actual === null) {
                                        document.getElementById('form-calificacion').style.display = 'block';
                                        document.getElementById('cambiar-calificacion').style.display = 'none';
                                    } else {
                                        document.getElementById('form-calificacion').style.display = 'none';
                                        document.getElementById('cambiar-calificacion').style.display = 'block';
                                        if (data.notas_json) {
                                            const notas = JSON.parse(data.notas_json);
                                            for (let i = 1; i <= <?php echo $judge_count; ?>; i++) {
                                                const juez = `J${i}`;
                                                if (notas[juez]) {
                                                    document.getElementById(`nd_${i}`).value = notas[juez].ND;
                                                    document.getElementById(`ded_${i}`).value = notas[juez].DED;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                document.getElementById('participante-info').style.display = 'none';
                                document.getElementById('error-message').innerText = 'El participante no existe.';
                                document.getElementById('error-message').style.display = 'block';
                                document.getElementById('form-calificacion').style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Error fetching participante:', error));
                }
            });

            document.getElementById('cambiar-calificacion').addEventListener('click', function () {
                document.getElementById('form-calificacion').style.display = 'block';
                this.style.display = 'none';
            });

            document.getElementById('form-calificacion').addEventListener('submit', function (e) {
                e.preventDefault();
                const notas = {};
                for (let i = 1; i <= <?php echo $judge_count; ?>; i++) {
                    const nd = document.getElementById(`nd_${i}`).value;
                    const ded = document.getElementById(`ded_${i}`).value;
                    notas[`J${i}`] = { ND: nd, DED: ded };
                }
                const notasInput = document.createElement('input');
                notasInput.type = 'hidden';
                notasInput.name = 'notas';
                notasInput.value = JSON.stringify(notas);
                this.appendChild(notasInput);

                const neutralsInput = document.createElement('input');
                neutralsInput.type = 'hidden';
                neutralsInput.name = 'neutrals';
                neutralsInput.value = document.getElementById('neutrals').value;
                this.appendChild(neutralsInput);

                const bonusInput = document.createElement('input');
                bonusInput.type = 'hidden';
                bonusInput.name = 'bonus';
                bonusInput.value = document.getElementById('bonus').value;
                this.appendChild(bonusInput);

                this.submit();
            });
        });
    </script>
</body>
</html>