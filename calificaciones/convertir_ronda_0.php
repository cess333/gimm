<?php
include('../conexion.php');

// Obtener la ronda seleccionada desde GET
$ronda = isset($_GET['ronda']) ? (int)$_GET['ronda'] : 0;

// Verificar que no sea Ronda 0
if ($ronda == 0) {
    echo json_encode(['success' => false, 'error' => 'No se puede convertir Ronda 0 a sí misma.']);
    exit;
}

// Iniciar una transacción
$conn->begin_transaction();

try {
    // Verificar si calificacion (Ronda 0) está vacía
    $sql_check = "SELECT COUNT(*) AS count FROM calificacion";
    $result_check = $conn->query($sql_check);
    $count = $result_check->fetch_assoc()['count'];

    if ($count > 0) {
        throw new Exception('No se puede convertir: Ronda 0 ya tiene calificaciones.');
    }

    // Mover los datos de calificacion_ronda a calificacion, estableciendo ronda como NULL
    $sql_move = "
        INSERT INTO calificacion (
            participante_id, salto, barras, viga, piso, tumbling, 
            arzones, anillos, barras_paralelas, barra_fija, circuitos, 
            panel, ronda
        )
        SELECT 
            participante_id, salto, barras, viga, piso, tumbling, 
            arzones, anillos, barras_paralelas, barra_fija, circuitos, 
            panel, NULL
        FROM calificacion_ronda
        WHERE ronda = ?
    ";
    $stmt = $conn->prepare($sql_move);
    $stmt->bind_param("i", $ronda);
    $stmt->execute();

    // Eliminar los datos de calificacion_ronda después de moverlos
    $sql_delete = "DELETE FROM calificacion_ronda WHERE ronda = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $ronda);
    $stmt_delete->execute();

    // Confirmar la transacción
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

if (isset($stmt)) $stmt->close();
if (isset($stmt_delete)) $stmt_delete->close();
$conn->close();
?>