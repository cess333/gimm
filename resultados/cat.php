<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones de Gimnasia</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php
// Incluir archivo de conexión
include '../conexion.php';

$aparatos = ["salto", "barras", "viga", "piso", "tumbling", "arzones", "anillos", "barras_paralelas", "barra_fija", "circuitos"];

// Obtener categorías y participantes
$sql_categorias = "SELECT id, categoria, nivel FROM categoria ORDER BY nivel, categoria";
$result_categorias = $conn->query($sql_categorias);

while($categoria = $result_categorias->fetch_assoc()) {
    // Verificar si hay participantes con calificaciones en esta categoría
    $sql_check_participantes = "SELECT p.id 
        FROM participante p 
        JOIN calificacion c ON p.id = c.participante_id 
        WHERE p.categoria_id = " . $categoria['id'] . " 
        AND (" . implode(" IS NOT NULL OR ", array_map(function($a) { return "c.$a IS NOT NULL"; }, $aparatos)) . " IS NOT NULL)";
    
    $result_check_participantes = $conn->query($sql_check_participantes);

    if($result_check_participantes->num_rows > 0) {
        echo "<h2>Nivel: " . $categoria['nivel'] . " - Categoría: " . $categoria['categoria'] . "</h2>";

        echo "<table>";
        echo "<tr><th>Participante</th>";

        // Encabezados de aparatos
        foreach($aparatos as $aparato) {
            echo "<th>" . ucfirst($aparato) . "</th>";
        }
        echo "<th>Lugar con Empates</th><th>Lugar sin Empates</th><th>Lugar con Empates Saltando</th></tr>";

        $participantes_data = [];
        $sql_participantes = "SELECT p.id, p.nombre 
            FROM participante p 
            JOIN calificacion c ON p.id = c.participante_id 
            WHERE p.categoria_id = " . $categoria['id'] . " 
            AND (" . implode(" IS NOT NULL OR ", array_map(function($a) { return "c.$a IS NOT NULL"; }, $aparatos)) . " IS NOT NULL) 
            GROUP BY p.id";

        $result_participantes = $conn->query($sql_participantes);

        while($participante = $result_participantes->fetch_assoc()) {
            $sql_calificacion = "SELECT " . implode(", ", $aparatos) . " FROM calificacion WHERE participante_id = " . $participante['id'];
            $result_calificacion = $conn->query($sql_calificacion);
            $calificacion = $result_calificacion->fetch_assoc();

            // Calcular la sumatoria solo de los valores no nulos para determinar si el participante debería mostrarse
            $sumatoria = array_sum(array_filter($calificacion, function($value) { return $value !== null; }));

            if ($sumatoria > 0) {  // Solo mostramos participantes con al menos una calificación
                $participantes_data[] = [
                    'id' => $participante['id'],
                    'nombre' => $participante['nombre'],
                    'calificaciones' => $calificacion,
                    'sumatoria' => $sumatoria
                ];

                echo "<tr><td>" . $participante['nombre'] . "</td>";
                foreach($aparatos as $aparato) {
                    echo "<td>" . ($calificacion[$aparato] ?? '-') . "</td>";
                }
                echo "<td>TBD</td><td>TBD</td><td>TBD</td></tr>"; // Lugares a definir
            }
        }

        // Calcular lugares
        usort($participantes_data, function($a, $b) {
            return $b['sumatoria'] <=> $a['sumatoria']; // Ordena por sumatoria descendente
        });

        $lugar_con_empates = 1;
        $lugar_sin_empates = 1;
        $lugar_saltando = 1;
        $last_sum = null;
        $empate_count = 0;

        foreach($participantes_data as $i => $data) {
            if ($data['sumatoria'] != $last_sum) {
                $lugar_con_empates = $i + 1 - $empate_count;
                $lugar_saltando = $i + 1;
                $empate_count = 0;
            } else {
                $empate_count++;
            }
            $last_sum = $data['sumatoria'];

            echo "<script>
                var row = document.getElementsByTagName('tr')[" . ($i + 1) . "];
                row.children[row.children.length - 3].innerHTML = '" . $lugar_con_empates . "';
                row.children[row.children.length - 2].innerHTML = '" . $lugar_sin_empates . "';
                row.children[row.children.length - 1].innerHTML = '" . $lugar_saltando . "';
            </script>";

            $lugar_sin_empates++;
        }

        echo "</table>";
    }
}

$conn->close();
?>

</body>
</html>