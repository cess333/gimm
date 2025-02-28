<?php
include('../conexion.php');

$ronda = isset($_GET['ronda']) ? (int)$_GET['ronda'] : 0;

if ($ronda == 0) {
    $sql = "
        SELECT 
            SUM(CASE WHEN (cat.aparato_salto = 1 AND c.salto IS NULL) THEN 1 ELSE 0 END) AS faltan_salto,
            SUM(CASE WHEN (cat.aparato_barras = 1 AND c.barras IS NULL) THEN 1 ELSE 0 END) AS faltan_barras,
            SUM(CASE WHEN (cat.aparato_viga = 1 AND c.viga IS NULL) THEN 1 ELSE 0 END) AS faltan_viga,
            SUM(CASE WHEN (cat.aparato_piso = 1 AND c.piso IS NULL) THEN 1 ELSE 0 END) AS faltan_piso,
            SUM(CASE WHEN (cat.aparato_tumbling = 1 AND c.tumbling IS NULL) THEN 1 ELSE 0 END) AS faltan_tumbling,
            SUM(CASE WHEN (cat.aparato_arzones = 1 AND c.arzones IS NULL) THEN 1 ELSE 0 END) AS faltan_arzones,
            SUM(CASE WHEN (cat.aparato_anillos = 1 AND c.anillos IS NULL) THEN 1 ELSE 0 END) AS faltan_anillos,
            SUM(CASE WHEN (cat.aparato_barras_paralelas = 1 AND c.barras_paralelas IS NULL) THEN 1 ELSE 0 END) AS faltan_barras_paralelas,
            SUM(CASE WHEN (cat.aparato_barra_fija = 1 AND c.barra_fija IS NULL) THEN 1 ELSE 0 END) AS faltan_barra_fija,
            SUM(CASE WHEN (cat.aparato_circuitos = 1 AND c.circuitos IS NULL) THEN 1 ELSE 0 END) AS faltan_circuitos
        FROM calificacion c
        JOIN participante p ON c.participante_id = p.id
        JOIN categoria cat ON p.categoria_id = cat.id
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT 
            SUM(CASE WHEN (cat.aparato_salto = 1 AND c.salto IS NULL) THEN 1 ELSE 0 END) AS faltan_salto,
            SUM(CASE WHEN (cat.aparato_barras = 1 AND c.barras IS NULL) THEN 1 ELSE 0 END) AS faltan_barras,
            SUM(CASE WHEN (cat.aparato_viga = 1 AND c.viga IS NULL) THEN 1 ELSE 0 END) AS faltan_viga,
            SUM(CASE WHEN (cat.aparato_piso = 1 AND c.piso IS NULL) THEN 1 ELSE 0 END) AS faltan_piso,
            SUM(CASE WHEN (cat.aparato_tumbling = 1 AND c.tumbling IS NULL) THEN 1 ELSE 0 END) AS faltan_tumbling,
            SUM(CASE WHEN (cat.aparato_arzones = 1 AND c.arzones IS NULL) THEN 1 ELSE 0 END) AS faltan_arzones,
            SUM(CASE WHEN (cat.aparato_anillos = 1 AND c.anillos IS NULL) THEN 1 ELSE 0 END) AS faltan_anillos,
            SUM(CASE WHEN (cat.aparato_barras_paralelas = 1 AND c.barras_paralelas IS NULL) THEN 1 ELSE 0 END) AS faltan_barras_paralelas,
            SUM(CASE WHEN (cat.aparato_barra_fija = 1 AND c.barra_fija IS NULL) THEN 1 ELSE 0 END) AS faltan_barra_fija,
            SUM(CASE WHEN (cat.aparato_circuitos = 1 AND c.circuitos IS NULL) THEN 1 ELSE 0 END) AS faltan_circuitos
        FROM calificacion_ronda c
        JOIN participante p ON c.participante_id = p.id
        JOIN categoria cat ON p.categoria_id = cat.id
        WHERE c.ronda = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ronda);
    $stmt->execute();
    $result = $stmt->get_result();
}

$data = $result->fetch_assoc() ?: [
    'faltan_salto' => 0, 'faltan_barras' => 0, 'faltan_viga' => 0, 'faltan_piso' => 0,
    'faltan_tumbling' => 0, 'faltan_arzones' => 0, 'faltan_anillos' => 0,
    'faltan_barras_paralelas' => 0, 'faltan_barra_fija' => 0, 'faltan_circuitos' => 0
];

echo json_encode($data);

if (isset($stmt)) $stmt->close();
$conn->close();
?>