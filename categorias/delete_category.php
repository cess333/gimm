<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); // Asegúrate de recibir el ID desde POST

    // Verificar si existen participantes asociados a la categoría
    $check_sql = "SELECT COUNT(*) as count FROM participante WHERE categoria_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Hay participantes asociados a la categoría
        echo json_encode([
            'status' => 'error',
            'message' => 'No se puede eliminar la categoría porque tiene participantes asignados.'
        ]);
    } else {
        // Proceder con la eliminación si no hay participantes asociados
        $sql = "DELETE FROM categoria WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        $response = [];
        if ($stmt->execute()) {
            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error al eliminar la categoría: ' . $stmt->error;
        }

        echo json_encode($response);

        $stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
