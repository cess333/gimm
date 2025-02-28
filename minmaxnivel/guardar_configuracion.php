<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rangos_min = $_POST['rango_min'];
    $rangos_max = $_POST['rango_max'];

    foreach ($rangos_min as $id => $rango_min) {
        $rango_max = $rangos_max[$id];
        $sql = "UPDATE configuracion_lugares_unificada SET rango_min = ?, rango_max = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ddi", $rango_min, $rango_max, $id);
        $stmt->execute();
    }
}

$conn->close();
header("Location: index.php");
exit();
?>
