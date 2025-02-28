<?php
include '../aparatos/navbar.php';
include '../conexion.php';

// Verificar si hay un mensaje en la URL
if (isset($_GET['mensaje'])) {
    $mensaje = urldecode($_GET['mensaje']);
}

// Manejo del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $rama = $_POST['rama'];
    $ano_nacimiento = $_POST['ano_nacimiento'];
    $club_id = $_POST['club_id'];
    $nivel = $_POST['nivel'];
    $elegido = isset($_POST['elegido']) ? 'si' : 'no'; // Usa 'si' sin tilde como en tu dump
    $participanteId = $_POST['participanteId'];

    if ($rama != "1" && $rama != "2") {
        die("Error: Valor de rama inválido.");
    }

    $categoria_id = null;
    $categoria_query = "SELECT id FROM categoria 
                        WHERE rama = ? 
                          AND nivel = ? 
                          AND ? BETWEEN ano_1 AND ano_2 
                        LIMIT 1";
    $stmt_categoria = $conn->prepare($categoria_query);
    $stmt_categoria->bind_param("isi", $rama, $nivel, $ano_nacimiento);
    $stmt_categoria->execute();
    $stmt_categoria->bind_result($categoria_id);
    $stmt_categoria->fetch();
    $stmt_categoria->close();

    if (!$categoria_id) {
        $mensaje = "No se encontró una categoría adecuada para el participante.";
    } else {
        if (empty($participanteId)) {
            $sql = "INSERT INTO participante (nombre, rama, ano_nacimiento, club_id, categoria_id, elegido) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisis", $nombre, $rama, $ano_nacimiento, $club_id, $categoria_id, $elegido);

            if ($stmt->execute()) {
                $mensaje = "Participante agregado exitosamente con categoría asignada.";
            } else {
                $mensaje = "Error al agregar participante: " . $conn->error;
            }
        } else {
            $sql = "UPDATE participante SET nombre=?, rama=?, ano_nacimiento=?, club_id=?, categoria_id=?, elegido=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisiss", $nombre, $rama, $ano_nacimiento, $club_id, $categoria_id, $elegido, $participanteId);

            if ($stmt->execute()) {
                $mensaje = "Participante actualizado exitosamente con categoría asignada.";
            } else {
                $mensaje = "Error al actualizar participante: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

// Eliminar participante
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_check_calificaciones = "SELECT COUNT(*) FROM calificacion WHERE participante_id=?";
    $stmt_check = $conn->prepare($sql_check_calificaciones);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        $mensaje = "No se puede eliminar el participante porque tiene calificaciones asignadas.";
    } else {
        $sql = "DELETE FROM participante WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "Participante eliminado exitosamente.";
        } else {
            $mensaje = "Error al eliminar participante: " . $conn->error;
        }
        $stmt->close();
    }
}

// Obtener participantes con su estatus de calificación en calificacion_ronda
$sql = "SELECT p.id, 
               p.nombre, 
               p.rama, 
               p.ano_nacimiento, 
               p.club_id, 
               c.nombre AS club, 
               ca.nivel AS nivel, 
               ca.categoria AS categoria,
               p.elegido,
               (SELECT COUNT(*) FROM calificacion_ronda cr WHERE cr.participante_id = p.id) > 0 AS tiene_calificacion
        FROM participante p
        LEFT JOIN club c ON p.club_id = c.id
        LEFT JOIN categoria ca ON p.categoria_id = ca.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Participantes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="container mt-3">
        <?php if (isset($mensaje)) echo "<div class='alert alert-info'>$mensaje</div>"; ?>
        <div class="d-flex justify-content-between gap-2 mb-3">
            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#participanteModal">Agregar Participante</button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cargaMasivaModal">Carga Masiva</button>
                <button id="generate-excel-btn" class="btn btn-info">Generar Excel</button>
            </div>
            <button id="clear-table-btn" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearTableModal">Limpiar tabla</button>
        </div>

        <table id="participantesTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Rama</th>
                    <th>Año de Nacimiento</th>
                    <th>Club</th>
                    <th>Nivel</th>
                    <th>Categoría</th>
                    <th>Elegido</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo ($row['rama'] == '1') ? 'Varonil' : 'Femenil'; ?></td>
                        <td><?php echo $row['ano_nacimiento']; ?></td>
                        <td><?php echo $row['club']; ?></td>
                        <td><?php echo $row['nivel']; ?></td>
                        <td><?php echo $row['categoria']; ?></td>
                        <td><?php echo $row['elegido'] === 'si' ? 'Sí' : 'No'; ?></td>
                        <td>
                            <span class="badge <?php echo $row['tiene_calificacion'] ? 'bg-success' : 'bg-danger'; ?> text-white">
                                <?php echo $row['tiene_calificacion'] ? 'Con calificación' : 'Sin calificación'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-warning edit-participante" 
                                data-id="<?php echo $row['id']; ?>" 
                                data-nombre="<?php echo $row['nombre']; ?>" 
                                data-rama="<?php echo $row['rama']; ?>" 
                                data-ano_nacimiento="<?php echo $row['ano_nacimiento']; ?>" 
                                data-club_id="<?php echo $row['club_id']; ?>" 
                                data-nivel="<?php echo $row['nivel']; ?>"
                                data-elegido="<?php echo $row['elegido']; ?>">
                                Editar
                            </button>
                            <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger delete-participante" onclick="return confirm('¿Estás seguro de que deseas eliminar este participante?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para carga masiva -->
    <div class="modal fade" id="cargaMasivaModal" tabindex="-1" role="dialog" aria-labelledby="cargaMasivaLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cargaMasivaLabel">Carga Masiva de Participantes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="procesar_csv.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="archivo_csv">Selecciona un archivo CSV:</label>
                            <input type="file" id="archivo_csv" name="archivo_csv" class="form-control" accept=".csv" required>
                            <small class="form-text text-muted"><br>
                                El archivo CSV debe tener el siguiente formato:<br>
                                <strong>nombre, rama, ano_nacimiento, club_nombre, nivel, elegido</strong><br>
                                <em>Ejemplo:</em><br>
                                Juan Pérez, 1, 2005, Club Deportivo Alpha, Avanzado, si<br>
                                María López, 2, 2007, Club Olímpico, Intermedio, no
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Cargar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para limpiar tabla -->
    <div class="modal fade" id="clearTableModal" tabindex="-1" aria-labelledby="clearTableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clearTableModalLabel">Confirmar limpieza de tabla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas limpiar la tabla "participante"? Esta acción eliminará todos los participantes y podría afectar a las calificaciones asociadas.</p>
                    <p>Tiempo restante: <span id="countdown">6</span> segundos</p>
                    <button id="confirm-clear-btn" class="btn btn-danger">Confirmar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar participante -->
    <div class="modal fade" id="participanteModal" tabindex="-1" role="dialog" aria-labelledby="participanteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="participanteModalLabel">Agregar Participante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="participanteForm">
                    <div class="modal-body">
                        <input type="hidden" id="participanteId" name="participanteId" value="">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="rama">Rama:</label>
                            <select id="rama" name="rama" class="form-control" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="2">Femenil</option>
                                <option value="1">Varonil</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ano_nacimiento">Año de Nacimiento:</label>
                            <input type="number" id="ano_nacimiento" name="ano_nacimiento" class="form-control" min="1900" max="2024" required>
                        </div>
                        <div class="form-group">
                            <label for="club_id">Club:</label>
                            <select id="club_id" name="club_id" class="form-control" required>
                                <option value="" disabled selected>Selecciona un club</option>
                                <?php
                                $club_query = "SELECT id, nombre FROM club";
                                $club_result = $conn->query($club_query);
                                while ($club = $club_result->fetch_assoc()) {
                                    echo "<option value='{$club['id']}'>{$club['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nivel">Nivel:</label>
                            <select id="nivel" name="nivel" class="form-control" required>
                                <option value="" disabled selected>Selecciona un nivel</option>
                                <?php
                                $nivel_query = "SELECT DISTINCT nivel FROM categoria";
                                $nivel_result = $conn->query($nivel_query);
                                while ($nivel = $nivel_result->fetch_assoc()) {
                                    echo "<option value='{$nivel['nivel']}'>{$nivel['nivel']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría:</label>
                            <input type="text" id="categoria" name="categoria" class="form-control bg-light" readonly>
                            <div class="invalid-feedback">No se encontró una categoría adecuada.</div>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" id="elegido" name="elegido" class="form-check-input">
                            <label for="elegido" class="form-check-label">Elegido</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" id="guardarParticipante" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function () {
        var table = $('#participantesTable').DataTable();

        function obtenerCategoria() {
            const rama = $('#rama').val();
            const nivel = $('#nivel').val();
            const ano_nacimiento = $('#ano_nacimiento').val();

            if (rama && nivel && ano_nacimiento) {
                $.ajax({
                    url: 'obtener_categoria.php',
                    type: 'POST',
                    data: { rama: rama, nivel: nivel, ano_nacimiento: ano_nacimiento },
                    success: function (respuesta) {
                        if (respuesta === "Categoría no encontrada") {
                            $('#categoria').val('');
                            $('#categoria').addClass('is-invalid');
                            $('#guardarParticipante').prop('disabled', true);
                        } else {
                            $('#categoria').val(respuesta);
                            $('#Categoria').removeClass('is-invalid');
                            $('#guardarParticipante').prop('disabled', false);
                        }
                    },
                    error: function () {
                        $('#categoria').val('Error al buscar categoría');
                        $('#categoria').addClass('is-invalid');
                        $('#guardarParticipante').prop('disabled', true);
                    }
                });
            } else {
                $('#categoria').val('');
                $('#categoria').addClass('is-invalid');
                $('#guardarParticipante').prop('disabled', true);
            }
        }

        $('#participantesTable').on('click', '.edit-participante', function () {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            const rama = $(this).data('rama');
            const ano_nacimiento = $(this).data('ano_nacimiento');
            const club_id = $(this).data('club_id');
            const nivel = $(this).data('nivel');
            const elegido = $(this).data('elegido');

            $('#participanteId').val(id);
            $('#nombre').val(nombre);
            $('#rama').val(rama);
            $('#ano_nacimiento').val(ano_nacimiento);
            $('#club_id').val(club_id);
            $('#nivel').val(nivel);
            $('#elegido').prop('checked', elegido === 'si'); // Compara con 'si' sin tilde

            obtenerCategoria();

            $('#participanteModalLabel').text('Editar Participante');
            $('#participanteModal').modal('show');
        });

        $('#participanteModal').on('hidden.bs.modal', function () {
            $('#participanteForm')[0].reset();
            $('#categoria').removeClass('is-invalid');
            $('#guardarParticipante').prop('disabled', false);
            $('#participanteModalLabel').text('Agregar Participante');
        });

        $('#rama, #nivel, #ano_nacimiento').on('change', obtenerCategoria);

        // Manejar el botón de generar Excel (genera CSV)
        $('#generate-excel-btn').click(function() {
            window.location.href = 'generate_participants_excel.php';
        });

        // Manejar el modal de limpiar tabla
        $('#clearTableModal').on('show.bs.modal', function() {
            let timeLeft = 6;
            $('#countdown').text(timeLeft);
            $('#confirm-clear-btn').prop('disabled', true);

            const countdownInterval = setInterval(function() {
                timeLeft--;
                $('#countdown').text(timeLeft);
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    $('#confirm-clear-btn').prop('disabled', false);
                }
            }, 1000);
        });

        // Manejar el botón de confirmar limpieza
        $('#confirm-clear-btn').click(function() {
            $.ajax({
                url: 'clear_participants_table.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#clearTableModal').modal('hide');
                        alert('La tabla "participante" ha sido limpiada correctamente.');
                        location.reload(); // Recargar la página completa
                    } else {
                        alert('Error al limpiar la tabla: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Hubo un error al intentar limpiar la tabla: ' + error);
                }
            });
        });
    });
    </script>
</body>
</html>