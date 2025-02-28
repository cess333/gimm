<?php
include('../conexion.php');

// Consultar los datos de los participantes y sus calificaciones
$sql = "
    SELECT p.id AS participante_id, p.nombre AS participante, 
           c.salto, c.barras, c.viga, c.piso, c.tumbling, 
           c.arzones, c.anillos, c.barras_paralelas, c.barra_fija, c.circuitos, 
           c.panel,
           cat.aparato_salto, cat.aparato_barras, cat.aparato_viga, cat.aparato_piso, 
           cat.aparato_tumbling, cat.aparato_arzones, cat.aparato_anillos, 
           cat.aparato_barras_paralelas, cat.aparato_barra_fija, cat.aparato_circuitos
    FROM calificacion c
    JOIN participante p ON c.participante_id = p.id
    JOIN categoria cat ON p.categoria_id = cat.id
";
$result = $conn->query($sql);

// Verificar si la consulta SQL fue exitosa
if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}

// Crear un array para almacenar las filas completas de la tabla
$html = '';

// Recorrer los resultados y construir las filas de la tabla
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row['participante_id'] . '</td>';
    $html .= '<td>' . $row['participante'] . '</td>';

    // Generar celdas de calificación basadas en la lógica de habilitación/deshabilitación
    $html .= generateEditableCell($row, 'salto', 'aparato_salto');
    $html .= generateEditableCell($row, 'barras', 'aparato_barras');
    $html .= generateEditableCell($row, 'viga', 'aparato_viga');
    $html .= generateEditableCell($row, 'piso', 'aparato_piso');
    $html .= generateEditableCell($row, 'tumbling', 'aparato_tumbling');
    $html .= generateEditableCell($row, 'arzones', 'aparato_arzones');
    $html .= generateEditableCell($row, 'anillos', 'aparato_anillos');
    $html .= generateEditableCell($row, 'barras_paralelas', 'aparato_barras_paralelas');
    $html .= generateEditableCell($row, 'barra_fija', 'aparato_barra_fija');
    $html .= generateEditableCell($row, 'circuitos', 'aparato_circuitos');

    // Celda para el panel, siempre editable
    $html .= '<td contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="panel">';
    $html .= isset($row['panel']) ? $row['panel'] : '';
    $html .= '</td>';

    $html .= '</tr>';
}

// Función para generar celdas editables basadas en si el aparato está habilitado
function generateEditableCell($row, $column, $category_column) {
    // Si el aparato está habilitado (valor 1), hacer que la celda sea editable
    $editable = (isset($row[$category_column]) && $row[$category_column] == 1) ? 
                'contenteditable="true" class="edit" data-id="' . $row['participante_id'] . '" data-column="' . $column . '"' : 
                'class="disabled"';
    // Asignar el valor de la calificación, si existe
    $value = isset($row[$column]) ? $row[$column] : '';
    return '<td ' . $editable . '>' . $value . '</td>';
}

// Devolver las filas completas
echo $html;
?>
