<?php
include('../conexion.php');

// Mover las calificaciones actuales a calificacion_old antes de limpiar
$sql_move = "INSERT INTO calificacion_old (participante_id, salto, barras, viga, piso, tumbling, arzones, anillos, barras_paralelas, barra_fija, circuitos, moved_at)
             SELECT participante_id, salto, barras, viga, piso, tumbling, arzones, anillos, barras_paralelas, barra_fija, circuitos, NOW()
             FROM calificacion";
$conn->query($sql_move);

// Limpiar la tabla calificacion
$sql_delete = "DELETE FROM calificacion";
if ($conn->query($sql_delete)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>