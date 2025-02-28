<?php
header('Content-Type: application/json'); // Especificar que la respuesta es JSON
require_once '../conexion.php';

global $conn;

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit;
}

try {
    $nombre = $_POST['nombre'] ?? '';
    $sufijo = $_POST['sufijo'] ?? '';
    $img = $_POST['img'] ?? null;

    // Validar que el nombre no esté vacío
    if (empty($nombre)) {
        throw new Exception('El nombre del club es obligatorio');
    }

    // Validar que el sufijo no esté vacío
    if (empty($sufijo)) {
        throw new Exception('El sufijo del club es obligatorio');
    }

    // Verificar si ya existe un club con el mismo nombre
    $checkSql = $conn->prepare("SELECT COUNT(*) FROM club WHERE nombre = ?");
    $checkSql->bind_param('s', $nombre);
    $checkSql->execute();
    $checkSql->bind_result($count);
    $checkSql->fetch();
    $checkSql->close();

    if ($count > 0) {
        throw new Exception('Ya existe un club con el nombre "' . $nombre . '"');
    }

    // Proceder con la inserción
    $sql = $conn->prepare("INSERT INTO club (nombre, sufijo, img) VALUES (?, ?, ?)");
    $sql->bind_param('sss', $nombre, $sufijo, $img);

    if ($sql->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>