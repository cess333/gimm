<?php
header('Content-Type: application/json');
include '../conexion.php'; // Asegúrate de que la ruta a 'conexion.php' sea correcta

// Obtener el aparato desde la URL
$aparato = isset($_GET['aparato']) ? $_GET['aparato'] : '';
$aparatos_validos = ['Salto', 'Barras', 'Piso', 'Viga'];

if (!in_array($aparato, $aparatos_validos)) {
    echo json_encode(["error" => "Aparato no válido"]);
    exit;
}

// Consulta para obtener la última calificación del aparato seleccionado
$sql = "
    SELECT p.nombre, c.$aparato AS calificacion, cl.nombre AS nombre_del_club, cat.nivel, cat.categoria
    FROM calificacion c
    JOIN participante p ON p.id = c.participante_id
    LEFT JOIN club cl ON p.club_id = cl.id
    LEFT JOIN categoria cat ON p.categoria_id = cat.id
    WHERE c.$aparato IS NOT NULL
    ORDER BY c.id DESC LIMIT 1
";

$result = $conn->query($sql);

// Si hay un error en la consulta, mostrarlo
if (!$result) {
    echo json_encode(["error" => "Error en la consulta SQL", "detalle" => $conn->error]);
    exit;
}

$calificacion = $result->fetch_assoc();
$conn->close();

// Si no se encontró ninguna calificación, devolver un mensaje
if (!$calificacion) {
    echo json_encode(["error" => "No hay calificaciones registradas"]);
} else {
    echo json_encode($calificacion);
}
?>
