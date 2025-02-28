<?php
header('Content-Type: application/json');
require_once '../conexion.php';

global $conn;

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit;
}

try {
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
    if ($conn->query("TRUNCATE TABLE club") === TRUE) {
        $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>