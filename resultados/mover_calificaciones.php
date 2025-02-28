<?php
include '../conexion.php'; // Conexión a la base de datos

try {
    // Iniciar una transacción para garantizar consistencia
    $conn->begin_transaction();

    // Mover los datos de la tabla `calificacion` a `calificacion_old`
    $sql_insert = "INSERT INTO calificacion_old (participante_id, salto, barras, viga, piso, tumbling, arzones, anillos, barras_paralelas, barra_fija, circuitos)
                   SELECT participante_id, salto, barras, viga, piso, tumbling, arzones, anillos, barras_paralelas, barra_fija, circuitos
                   FROM calificacion";
    $conn->query($sql_insert);

    // Verificar si la consulta de inserción fue exitosa
    if ($conn->affected_rows > 0) {
        // Eliminar los datos de la tabla `calificacion`
        $sql_delete = "DELETE FROM calificacion";
        $conn->query($sql_delete);

        // Confirmar la transacción
        $conn->commit();
        echo json_encode(['message' => 'Los resultados se han eliminado correctamente.']);
    } else {
        throw new Exception("No se encontraron datos para eliminar.");
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(['message' => 'Error al mover las calificaciones: ' . $e->getMessage()]);
}

// Cerrar la conexión
$conn->close();
?>
