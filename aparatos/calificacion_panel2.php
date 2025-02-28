<?php
include '../conexion.php';

$aparato = isset($_GET['aparato']) ? $_GET['aparato'] : null;
$panel = isset($_GET['panel']) ? $_GET['panel'] : 'Panel 2';

$aparatos_validos = ['salto', 'barras', 'viga', 'piso', 'tumbling', 'arzones', 'anillos', 'barras-paralelas', 'barra-fija', 'circuitos'];
if (!in_array($aparato, $aparatos_validos)) {
    die("Aparato no válido.");
}

$mensaje = "";
if (isset($_POST['calificar']) && isset($_POST['calificacion']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $calificacion = floatval($_POST['calificacion']);
    $panel = "2";

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
            $sql_max = "SELECT cat.max AS max_calificacion, cat.aparato_$aparato AS habilitado 
                        FROM participante p JOIN categoria cat ON p.categoria_id = cat.id WHERE p.id = ?";
            $stmt_max = $conn->prepare($sql_max);
            $stmt_max->bind_param("i", $id);
            $stmt_max->execute();
            $result_max = $stmt_max->get_result();
            $data_max = $result_max->fetch_assoc();
            $max_calificacion = $data_max['max_calificacion'] ?? null;
            $habilitado = $data_max['habilitado'] ?? false;
            $stmt_max->close();

            if (!$habilitado) {
                $mensaje = "Error: El aparato $aparato no está habilitado.";
            } elseif ($max_calificacion !== null && $calificacion > $max_calificacion) {
                $mensaje = "Error: La calificación excede el máximo de $max_calificacion.";
            } else {
                $sql_check = "SELECT id FROM calificacion WHERE participante_id = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("i", $id);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows > 0) {
                    $sql_update = "UPDATE calificacion SET $aparato = ?, panel = ? WHERE participante_id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("dsi", $calificacion, $panel, $id);
                    if ($stmt_update->execute()) {
                        $mensaje = "Calificación actualizada correctamente.";
                    } else {
                        $mensaje = "Error al actualizar: " . $stmt_update->error;
                    }
                    $stmt_update->close();
                } else {
                    $sql_insert = "INSERT INTO calificacion (participante_id, $aparato, panel) VALUES (?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("ids", $id, $calificacion, $panel);
                    if ($stmt_insert->execute()) {
                        $mensaje = "Calificación asignada correctamente.";
                    } else {
                        $mensaje = "Error al asignar: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                }
                $stmt_check->close();
            }
        }
        $stmt_ronda_check->close();
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "
        SELECT p.id, p.nombre, p.ano_nacimiento AS edad, p.rama AS genero, 
        c.$aparato AS calificacion_actual, c.panel AS panel_actual,
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
    <title>Calificación de <?php echo ucfirst($aparato); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            width: 30px; /* Aumentado para mejor visibilidad */
            height: 30px; /* Aumentado para mejor visibilidad */
            font-size: 16px; /* Ajustado para que el emoji sea más visible */
            color: white;
            margin-right: 10px;
            transition: background-color 0.3s ease; /* Transición suave para el cambio de color */
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
        .alert { text-align: center; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">
            Calificación de <?php echo strtoupper($aparato); ?> - <?php echo htmlspecialchars($panel); ?>
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
                        <h5 class="card-title">Calificación</h5>
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
            <div class="form-group">
                <label for="calificacion" class="form-label">Calificación para <?php echo ucfirst($aparato); ?> (máx: <span id="max-calificacion"></span>)</label>
                <input type="number" name="calificacion" id="calificacion" class="form-control" step="0.01" min="0" required>
                <input type="hidden" name="id" id="id-participante">
            </div>
            <button type="submit" name="calificar" class="btn btn-success w-100 mt-3">Guardar Calificación</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const participanteInput = document.getElementById('participante_id');
            const calificacionInput = document.getElementById('calificacion');
            participanteInput.focus();

            // Verificar estado de calificaciones en tiempo real
            function checkCalificacionesEstado() {
                fetch('check_status.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error HTTP: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Estado recibido:', data); // Depuración
                        const statusCircle = document.getElementById('status-circle');
                        const statusText = document.getElementById('status-text');
                        const estadoDiv = document.getElementById('estado-calificaciones');

                        if (data.calificaciones_abiertas === 0) {
                            statusCircle.className = 'status-circle status-closed';
                            statusCircle.innerText = '🔒'; // Candado cerrado
                            statusText.innerText = 'cerradas';
                            document.getElementById('form-calificacion').style.display = 'none';
                            document.getElementById('cambiar-calificacion').style.display = 'none';
                        } else {
                            statusCircle.className = 'status-circle status-open';
                            statusCircle.innerText = '🔓'; // Candado abierto
                            statusText.innerText = 'abiertas';
                        }
                        estadoDiv.style.display = 'flex';
                    })
                    .catch(error => {
                        console.error('Error al consultar estado:', error);
                        const statusCircle = document.getElementById('status-circle');
                        const statusText = document.getElementById('status-text');
                        statusCircle.className = 'status-circle status-error';
                        statusCircle.innerText = '!';
                        statusText.innerText = 'error';
                        document.getElementById('estado-calificaciones').innerText += ' (Error: ' + error.message + ')';
                    });
            }

            // Ejecutar inmediatamente y luego cada 2 segundos
            checkCalificacionesEstado();
            setInterval(checkCalificacionesEstado, 2000); // Reduje a 2 segundos para pruebas

            participanteInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    calificacionInput.focus();
                }
            });

            participanteInput.addEventListener('input', function () {
                const id = this.value;
                if (id) {
                    fetch('?aparato=<?php echo $aparato; ?>&id=' + id)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Datos participante:', data);
                            if (data) {
                                document.getElementById('nombre').innerText = data.nombre || 'No asignado';
                                document.getElementById('nivel').innerText = data.nivel || 'No asignado';
                                document.getElementById('categoria').innerText = data.categoria || 'No asignado';
                                document.getElementById('calificacion_actual').innerText = data.calificacion_actual !== null ? data.calificacion_actual : 'No asignada';
                                document.getElementById('id-participante').value = data.id;

                                const cancionNombre = data.cancion_nombre || 'No asignada';
                                const cancionRuta = data.cancion_ruta || '';
                                document.getElementById('cancion-nombre').innerText = cancionNombre;

                                if (cancionRuta) {
                                    document.getElementById('reproductor').innerHTML = `<audio controls><source src="${cancionRuta}" type="audio/mp3">Tu navegador no soporta el reproductor.</audio>`;
                                } else {
                                    document.getElementById('reproductor').innerHTML = 'No hay canción asociada.';
                                }

                                const maxCal = data.max_calificacion || 'No definido';
                                document.getElementById('max-calificacion').innerText = maxCal;
                                calificacionInput.setAttribute('max', maxCal);
                                calificacionInput.setAttribute('placeholder', `Máx: ${maxCal}`);

                                if (!data.habilitado) {
                                    document.getElementById('error-message').innerText = 'El aparato no está habilitado para este participante.';
                                    document.getElementById('error-message').style.display = 'block';
                                    document.getElementById('form-calificacion').style.display = 'none';
                                    document.getElementById('cambiar-calificacion').style.display = 'none';
                                } else if (data.calificacion_ronda !== null) {
                                    document.getElementById('error-message').innerText = 'Este participante ya tiene una calificación final en esta ronda.';
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
        });
    </script>
</body>
</html>