<?php
include '../conexion.php';

// Obtener el estado actual de las calificaciones
$sql = "SELECT calificaciones_abiertas FROM configuracion LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$nuevo_estado = $row['calificaciones_abiertas'] ? 0 : 1; // Alternar entre 1 (abierto) y 0 (cerrado)

// Actualizar el estado en la base de datos
$update_sql = "UPDATE configuracion SET calificaciones_abiertas = $nuevo_estado";
if ($conn->query($update_sql)) {
    echo json_encode(["success" => true, "estado" => $nuevo_estado]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
?>
