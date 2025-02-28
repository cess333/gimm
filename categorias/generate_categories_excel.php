<?php
require_once '../conexion.php';

global $conn;

if ($conn->connect_error) {
    die('Conexión fallida: ' . $conn->connect_error);
}

// Consulta para obtener todos los datos de la tabla "categoria"
$query = "SELECT id, categoria, ano_1, ano_2, nivel, forma, rama, 
                 aparato_salto, aparato_barras, aparato_viga, aparato_piso, 
                 aparato_tumbling, aparato_arzones, aparato_anillos, 
                 aparato_barras_paralelas, aparato_barra_fija, aparato_circuitos, max 
          FROM categoria";
$result = $conn->query($query);

if (!$result) {
    die('Error en la consulta: ' . $conn->error);
}

// Configurar cabeceras para descargar el archivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="categorias_' . date('Y-m-d_H-i-s') . '.csv"');

// Abrir la salida como un "archivo" para escribir el CSV
$output = fopen('php://output', 'w');

// Agregar el BOM para UTF-8 (necesario para que Excel interprete correctamente los caracteres especiales)
fwrite($output, "\xEF\xBB\xBF");

// Escribir la fila de encabezados
fputcsv($output, [
    'ID', 'Categoría', 'Año Inicio', 'Año Fin', 'Nivel', 'Forma', 'Rama', 
    'Salto', 'Barras', 'Viga', 'Piso', 'Tumbling', 'Arzones', 'Anillos', 
    'Barras Paralelas', 'Barra Fija', 'Circuitos', 'Max'
]);

// Escribir los datos de la tabla
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['categoria'],
        $row['ano_1'],
        $row['ano_2'],
        $row['nivel'],
        $row['forma'],
        $row['rama'] == 1 ? 'Varonil' : 'Femenil',
        $row['aparato_salto'] ? 'Sí' : 'No',
        $row['aparato_barras'] ? 'Sí' : 'No',
        $row['aparato_viga'] ? 'Sí' : 'No',
        $row['aparato_piso'] ? 'Sí' : 'No',
        $row['aparato_tumbling'] ? 'Sí' : 'No',
        $row['aparato_arzones'] ? 'Sí' : 'No',
        $row['aparato_anillos'] ? 'Sí' : 'No',
        $row['aparato_barras_paralelas'] ? 'Sí' : 'No',
        $row['aparato_barra_fija'] ? 'Sí' : 'No',
        $row['aparato_circuitos'] ? 'Sí' : 'No',
        $row['max']
    ]);
}

// Cerrar el resultado y la conexión
$result->free();
$conn->close();

// Cerrar el "archivo" CSV
fclose($output);
exit;
?>