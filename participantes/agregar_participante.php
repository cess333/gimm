<?php
include '../conexion.php';

// Manejo del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add_participant') {
        // Obtener los datos del formulario
        $nombre = $_POST['nombre'];
        $rama = $_POST['rama'];
        $ano_nacimiento = $_POST['ano_nacimiento'];
        $club_id = $_POST['club_id'];
        $categoria_id = $_POST['categoria_id'];

        // Insertar en la base de datos
        $sql = "INSERT INTO participante (nombre, rama, ano_nacimiento, club_id, categoria_id) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $nombre, $rama, $ano_nacimiento, $club_id, $categoria_id);

        if ($stmt->execute()) {
            $mensaje = "Participante agregado exitosamente.";
        } else {
            $mensaje = "Error al agregar participante: " . $conn->error;
        }

        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'upload_csv') {
        // Manejo de carga de CSV
        if (isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file']['tmp_name'];
            if (($handle = fopen($file, 'r')) !== FALSE) {
                // Saltar la primera fila si contiene encabezados
                fgetcsv($handle);

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $nombre = $data[1]; // Asumiendo que el nombre es la segunda columna
                    $rama = $data[2]; // Asumiendo que la rama es la tercera columna
                    $ano_nacimiento = $data[3]; // Asumiendo que el año de nacimiento es la cuarta columna
                    
                    // Insertar en la base de datos
                    $sql = "INSERT INTO participante (nombre, rama, ano_nacimiento) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $nombre, $rama, $ano_nacimiento);
                    $stmt->execute();
                }
                fclose($handle);
                $mensaje = "Datos cargados exitosamente desde el archivo CSV.";
            } else {
                $mensaje = "Error al abrir el archivo CSV.";
            }
        }
    }
}

// Búsqueda de clubes y categorías
if (isset($_GET['term']) && isset($_GET['type'])) {
    $term = $_GET['term'];
    $type = $_GET['type'];

    if ($type === 'club') {
        $sql = "SELECT id, nombre FROM club WHERE nombre LIKE ? LIMIT 10";
    } elseif ($type === 'categoria') {
        $sql = "SELECT id, categoria FROM categoria WHERE categoria LIKE ? LIMIT 10";
    } else {
        exit();
    }

    $stmt = $conn->prepare($sql);
    $likeTerm = '%' . $term . '%';
    $stmt->bind_param('s', $likeTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }

    $stmt->close();
    echo json_encode($suggestions);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Participantes</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Administrar Participantes</h1>
        <?php if (isset($mensaje)) echo "<div class='alert alert-info'>$mensaje</div>"; ?>

        <!-- Formulario para agregar participante -->
        <form method="POST" class="mt-4" id="participanteForm">
            <input type="hidden" name="action" value="add_participant">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="rama">Rama:</label>
                <select id="rama" name="rama" class="form-control" required>
                    <option value="" disabled selected>Selecciona una opción</option>
                    <option value="femenil">Femenil</option>
                    <option value="baronil">Baronil</option>
                </select>
            </div>
            <div class="form-group">
                <label for="ano_nacimiento">Año de Nacimiento:</label>
                <input type="number" id="ano_nacimiento" name="ano_nacimiento" class="form-control" min="1900" max="2024" required>
            </div>
            <div class="form-group">
                <label for="club_id">ID del Club:</label>
                <input type="text" id="club_id" name="club_id" class="form-control" required autocomplete="off">
                <ul id="club-suggestions" class="list-group mt-2" style="display: none;"></ul>
            </div>
            <div class="form-group">
                <label for="categoria_id">ID de la Categoría:</label>
                <input type="text" id="categoria_id" name="categoria_id" class="form-control" required autocomplete="off">
                <ul id="categoria-suggestions" class="list-group mt-2" style="display: none;"></ul>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Agregar Participante</button>
        </form>

        <!-- Formulario para cargar CSV -->
        <form method="POST" enctype="multipart/form-data" class="mt-4">
            <input type="hidden" name="action" value="upload_csv">
            <div class="form-group">
                <label for="csv_file">Cargar archivo CSV:</label>
                <input type="file" id="csv_file" name="csv_file" class="form-control-file" accept=".csv" required>
            </div>

            <button type="submit" class="btn btn-success btn-block">Cargar Participantes</button>

        </form>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Función para buscar clubes
        $('#club_id').on('input', function() {
            let query = $(this).val();
            if (query.length > 0) {
                $.ajax({
                    url: '',
                    method: 'GET',
                    data: { term: query, type: 'club' },
                    success: function(data) {
                        $('#club-suggestions').empty();
                        if (data.length > 0) {
                            $.each(data, function(i, club) {
                                $('#club-suggestions').append('<li class="list-group-item club-item" data-id="' + club.id + '">' + club.nombre + '</li>');
                            });
                            $('#club-suggestions').show();
                        } else {
                            $('#club-suggestions').hide();
                        }
                    }
                });
            } else {
                $('#club-suggestions').hide();
            }
        });

        // Seleccionar club de la lista
        $(document).on('click', '.club-item', function() {
            $('#club_id').val($(this).text());
            $('#club-suggestions').hide();
        });

        // Función para buscar categorías
        $('#categoria_id').on('input', function() {
            let query = $(this).val();
            if (query.length > 0) {
                $.ajax({
                    url: '',
                    method: 'GET',
                    data: { term: query, type: 'categoria' },
                    success: function(data) {
                        $('#categoria-suggestions').empty();
                        if (data.length > 0) {
                            $.each(data, function(i, categoria) {
                                $('#categoria-suggestions').append('<li class="list-group-item categoria-item" data-id="' + categoria.id + '">' + categoria.categoria + '</li>');
                            });
                            $('#categoria-suggestions').show();
                        } else {
                            $('#categoria-suggestions').hide();
                        }
                    }
                });
            } else {
                $('#categoria-suggestions').hide();
            }
        });

        // Seleccionar categoría de la lista
        $(document).on('click', '.categoria-item', function() {
            $('#categoria_id').val($(this).text());
            $('#categoria-suggestions').hide();
        });
    </script>
</body>
</html>
