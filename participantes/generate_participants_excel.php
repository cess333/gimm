<?php
require_once '../conexion.php';

global $conn;

if ($conn->connect_error) {
    die('Conexión fallida: ' . $conn->connect_error);
}

// Consulta para obtener todos los datos de la tabla "participante" con información relacionada
$query = "SELECT p.id, 
                 p.nombre, 
                 p.rama, 
                 p.ano_nacimiento, 
                 c.nombre AS club, 
                 ca.nivel AS nivel, 
                 ca.categoria AS categoria
          FROM participante p
          LEFT JOIN club c ON p.club_id = c.id
          LEFT JOIN categoria ca ON p.categoria_id = ca.id";
$result = $conn->query($query);

if (!$result) {
    die('Error en la consulta: ' . $conn->error);
}

// Configurar cabeceras para descargar el archivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="participantes_' . date('Y-m-d_H-i-s') . '.csv"');

// Abrir la salida como un "archivo" para escribir el CSV
$output = fopen('php://output', 'w');

// Agregar el BOM para UTF-8 (necesario para que Excel interprete correctamente los caracteres especiales)
fwrite($output, "\xEF\xBB\xBF");

// Escribir la fila de encabezados
fputcsv($output, ['ID', 'Nombre', 'Rama', 'Año de Nacimiento', 'Club', 'Nivel', 'Categoría']);

// Escribir los datos de la tabla
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['nombre'],
        $row['rama'] == '1' ? 'Varonil' : 'Femenil',
        $row['ano_nacimiento'],
        $row['club'],
        $row['nivel'],
        $row['categoria']
    ]);
}

// Cerrar el resultado y la conexión
$result->free();
$conn->close();

// Cerrar el "archivo" CSV
fclose($output);
exit;
?>