<?php
include('conexion.php');

if (isset($_POST['id'])) {
    $id = $_POST['id']; // ID del participante
    $sql = "DELETE FROM calificacion WHERE participante_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Registro eliminado exitosamente";
    } else {
        echo "Error al eliminar el registro: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>
