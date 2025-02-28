<?php
include('../conexion.php');

// Establecer el encabezado para asegurar que siempre devolvamos JSON
header('Content-Type: application/json');

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Obtener y validar los parámetros enviados
$participante_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$ronda = isset($_POST['ronda']) ? (int)$_POST['ronda'] : 0;

if ($participante_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de participante inválido']);
    exit;
}

// Determinar la tabla y la cláusula WHERE según la ronda
$table = ($ronda == 0) ? 'calificacion' : 'calificacion_ronda';
$where_clause = ($ronda == 0) ? '' : " AND ronda = ?";

// Preparar y ejecutar la consulta de eliminación
$sql = "DELETE FROM $table WHERE participante_id = ?" . $where_clause;
$stmt = $conn->prepare($sql);

if ($ronda == 0) {
    $stmt->bind_param("i", $participante_id);
} else {
    $stmt->bind_param("ii", $participante_id, $ronda);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

// Cerrar la declaración y la conexión
$stmt->close();
$conn->close();
?>