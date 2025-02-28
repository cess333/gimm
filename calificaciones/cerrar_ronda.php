<?php
include('../conexion.php');

// Iniciar una transacción
$conn->begin_transaction();

try {
    // Obtener el número más alto de ronda en calificacion_ronda
    $sql_max_ronda = "SELECT MAX(ronda) AS max_ronda FROM calificacion_ronda";
    $result_max_ronda = $conn->query($sql_max_ronda);
    $max_ronda = $result_max_ronda->fetch_assoc()['max_ronda'] ?? 0;

    // Transferir calificaciones de calificacion a calificacion_ronda
    $sql_transfer = "
        INSERT INTO calificacion_ronda (
            participante_id, salto, barras, viga, piso, tumbling, 
            arzones, anillos, barras_paralelas, barra_fija, circuitos, 
            panel, ronda
        )
        SELECT 
            participante_id, salto, barras, viga, piso, tumbling, 
            arzones, anillos, barras_paralelas, barra_fija, circuitos, 
            panel, COALESCE(ronda, ?)
        FROM calificacion
    ";
    $nueva_ronda = $max_ronda + 1;
    $stmt = $conn->prepare($sql_transfer);
    $stmt->bind_param("i", $nueva_ronda);
    $stmt->execute();

    // Limpiar la tabla calificacion después de transferir
    $sql_clear = "DELETE FROM calificacion";
    $conn->query($sql_clear);

    // Confirmar la transacción
    $conn->commit();

    echo json_encode(['success' => true, 'nueva_ronda' => $nueva_ronda]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>