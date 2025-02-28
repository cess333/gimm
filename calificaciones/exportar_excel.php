<?php
include('../conexion.php');

// Obtener la ronda seleccionada desde GET
$ronda_seleccionada = isset($_GET['ronda']) ? (int)$_GET['ronda'] : 0;

// Consulta para obtener los datos (igual que en index.php)
if ($ronda_seleccionada == 0) {
    $sql = "
        SELECT p.id AS participante_id, p.nombre AS participante, 
               c.salto, c.barras, c.viga, c.piso, c.tumbling, 
               c.arzones, c.anillos, c.barras_paralelas, c.barra_fija, c.circuitos, 
               c.panel, c.ronda
        FROM calificacion c
        JOIN participante p ON c.participante_id = p.id
    ";
} else {
    $sql = "
        SELECT p.id AS participante_id, p.nombre AS participante, 
               c.salto, c.barras, c.viga, c.piso, c.tumbling, 
               c.arzones, c.anillos, c.barras_paralelas, c.barra_fija, c.circuitos, 
               c.panel, c.ronda
        FROM calificacion_ronda c
        JOIN participante p ON c.participante_id = p.id
        WHERE c.ronda = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ronda_seleccionada);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($ronda_seleccionada == 0) {
    $result = $conn->query($sql);
}

if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}

// Configurar las cabeceras para descargar un archivo CSV con UTF-8 y BOM
$filename = 'Calificaciones_Ronda_' . ($ronda_seleccionada == 0 ? 'En_Proceso' : $ronda_seleccionada) . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Agregar BOM al inicio del archivo para que Excel reconozca UTF-8
echo "\xEF\xBB\xBF";

// Abrir la salida como un "archivo" CSV
$output = fopen('php://output', 'w');

// Agregar la fila de encabezados
$headers = [
    'ID Participante', 'Nombre Participante', 'Salto', 'Barras', 'Viga', 'Piso', 
    'Tumbling', 'Arzones', 'Anillos', 'Barras Paralelas', 'Barra Fija', 'Circuitos', 
    'Panel', 'Ronda'
];
fputcsv($output, $headers);

// Agregar los datos
while ($row = $result->fetch_assoc()) {
    $data = [
        $row['participante_id'],
        $row['participante'],
        $row['salto'] ?? '',
        $row['barras'] ?? '',
        $row['viga'] ?? '',
        $row['piso'] ?? '',
        $row['tumbling'] ?? '',
        $row['arzones'] ?? '',
        $row['anillos'] ?? '',
        $row['barras_paralelas'] ?? '',
        $row['barra_fija'] ?? '',
        $row['circuitos'] ?? '',
        $row['panel'] ?? '',
        $row['ronda'] ?? ''
    ];
    fputcsv($output, $data);
}

// Cerrar el archivo y salir
fclose($output);
exit;
?>