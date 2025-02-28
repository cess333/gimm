<?php
include '../conexion.php';

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$sql_estado = "SELECT calificaciones_abiertas FROM configuracion LIMIT 1";
$estado_result = $conn->query($sql_estado);
if ($estado_result === false) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
    exit;
}
$estado_data = $estado_result->fetch_assoc();
$calificaciones_abiertas = $estado_data['calificaciones_abiertas'] ?? 1;

header('Content-Type: application/json');
echo json_encode(['calificaciones_abiertas' => (int)$calificaciones_abiertas]); // Forzamos a entero

$conn->close();
?>