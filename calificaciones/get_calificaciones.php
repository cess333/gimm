<?php
include('../conexion.php');

$ronda = isset($_GET['ronda']) ? (int)$_GET['ronda'] : 0;

if ($ronda == 0) {
    $sql = "
        SELECT participante_id, salto, barras, viga, piso, tumbling, 
               arzones, anillos, barras_paralelas, barra_fija, circuitos, panel, ronda
        FROM calificacion
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT participante_id, salto, barras, viga, piso, tumbling, 
               arzones, anillos, barras_paralelas, barra_fija, circuitos, panel, ronda
        FROM calificacion_ronda
        WHERE ronda = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ronda);
    $stmt->execute();
    $result = $stmt->get_result();
}

while ($row = $result->fetch_assoc()) {
    foreach ($row as $column => $value) {
        if ($column != 'participante_id') {
            echo "<td contenteditable='true' class='edit' data-id='{$row['participante_id']}' data-column='$column'>" . ($value ?? '') . "</td>";
        }
    }
}

if (isset($stmt)) $stmt->close();
$conn->close();
?>