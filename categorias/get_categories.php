<?php
include '../conexion.php';

$sql = "SELECT * FROM categoria";
$result = $conn->query($sql);

$categorias = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Convertir valores de los aparatos y rama a enteros
        $row['aparato_salto'] = (int)$row['aparato_salto'];
        $row['aparato_barras'] = (int)$row['aparato_barras'];
        $row['aparato_viga'] = (int)$row['aparato_viga'];
        $row['aparato_piso'] = (int)$row['aparato_piso'];
        $row['aparato_tumbling'] = (int)$row['aparato_tumbling'];
        $row['aparato_arzones'] = (int)$row['aparato_arzones'];
        $row['aparato_anillos'] = (int)$row['aparato_anillos'];
        $row['aparato_barras_paralelas'] = (int)$row['aparato_barras_paralelas'];
        $row['aparato_barra_fija'] = (int)$row['aparato_barra_fija'];
        $row['aparato_circuitos'] = (int)$row['aparato_circuitos'];
        $row['rama'] = (int)$row['rama']; // Convertir rama a entero

        $categorias[] = $row;
    }
}


// Configurar encabezado y devolver datos como JSON
header('Content-Type: application/json');
echo json_encode($categorias, JSON_PRETTY_PRINT);
exit;

$conn->close();
?>
