<?php
require_once '../conexion.php';

header('Content-Type: application/json');

// Asegurar que la conexión use UTF-8
$conn->set_charset("utf8mb4");

// Verificar si el archivo fue enviado
if (isset($_FILES['csv-file']) && $_FILES['csv-file']['error'] == 0) {
    $fileTmpPath = $_FILES['csv-file']['tmp_name'];

    // Abrir el archivo CSV con la codificación correcta
    if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
        $conn->begin_transaction();
        $error = false;
        $processedNames = []; // Arreglo para rastrear nombres procesados en el CSV

        // Leer fila por fila
        $firstRow = true;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Omitir la cabecera (primera fila)
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            // Validar los datos y convertirlos a UTF-8
            $nombre = mb_convert_encoding(trim($data[0]), 'UTF-8', 'ISO-8859-1');
            $sufijo = isset($data[1]) ? mb_convert_encoding(trim($data[1]), 'UTF-8', 'ISO-8859-1') : null;
            $img = isset($data[2]) ? mb_convert_encoding(trim($data[2]), 'UTF-8', 'ISO-8859-1') : null;

            // Validar que nombre no esté vacío
            if (empty($nombre)) {
                $error = true;
                break;
            }

            // Verificar si el nombre ya fue procesado en este CSV
            if (in_array($nombre, $processedNames)) {
                continue; // Saltar este registro si el nombre ya está en el CSV
            }

            // Verificar si ya existe un club con el mismo nombre en la base de datos
            $checkSql = $conn->prepare("SELECT COUNT(*) FROM club WHERE nombre = ?");
            $checkSql->bind_param('s', $nombre);
            $checkSql->execute();
            $checkSql->bind_result($count);
            $checkSql->fetch();
            $checkSql->close();

            if ($count > 0) {
                continue; // Saltar este registro si el nombre ya existe en la base de datos
            }

            // Agregar el nombre al arreglo de procesados
            $processedNames[] = $nombre;

            // Insertar datos en la base de datos
            $sql = $conn->prepare("INSERT INTO club (nombre, sufijo, img) VALUES (?, ?, ?)");
            $sql->bind_param('sss', $nombre, $sufijo, $img);
            if (!$sql->execute()) {
                $error = true;
                break;
            }
        }

        fclose($handle);

        // Manejar éxito o error
        if ($error) {
            $conn->rollback();
            echo json_encode(['error' => 'Hubo un error al cargar los datos.']);
        } else {
            $conn->commit();
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['error' => 'No se pudo abrir el archivo.']);
    }
} else {
    echo json_encode(['error' => 'No se seleccionó ningún archivo o el archivo tiene un error.']);
}

$conn->close();
?>