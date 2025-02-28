<?php
require_once '../conexion.php';
header('Content-Type: application/json');

// Consulta para obtener los clubes y la cantidad de participantes
$sql = "
    SELECT 
        c.id, 
        c.nombre, 
        c.sufijo, 
        c.img, 
        COUNT(p.id) as participantes 
    FROM club c 
    LEFT JOIN participante p ON c.id = p.club_id 
    GROUP BY c.id, c.nombre, c.sufijo, c.img
";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    // Agregar la URL completa de la imagen si existe
    if ($row['img']) {
        $row['img'] = 'logos/' . $row['img'];
    }
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>