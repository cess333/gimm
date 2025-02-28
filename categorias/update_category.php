<?php
include '../conexion.php';

// Obtener datos enviados
$data = json_decode(file_get_contents('php://input'), true);

// Recuperar cada campo
$id = $data['id'];
$categoria = $data['name'];
$ano_1 = $data['ano_1'];
$ano_2 = $data['ano_2'];
$nivel = $data['nivel'];
$forma = $data['forma'];
$rama = $data['rama']; // Agregar rama
$aparato_salto = $data['aparato_salto'];
$aparato_barras = $data['aparato_barras'];
$aparato_viga = $data['aparato_viga'];
$aparato_piso = $data['aparato_piso'];
$aparato_tumbling = $data['aparato_tumbling'];
$aparato_arzones = $data['aparato_arzones'];
$aparato_anillos = $data['aparato_anillos'];
$aparato_barras_paralelas = $data['aparato_barras_paralelas'];
$aparato_barra_fija = $data['aparato_barra_fija'];
$aparato_circuitos = $data['aparato_circuitos'];
$max = isset($data['max']) ? $data['max'] : null; // Asegúrate de obtener el valor de max

// Actualizar en la base de datos
$sql = "UPDATE categoria SET 
    categoria = '$categoria', 
    ano_1 = $ano_1, 
    ano_2 = $ano_2, 
    nivel = '$nivel', 
    forma = '$forma', 
    rama = $rama, -- Agregar rama a la consulta
    aparato_salto = $aparato_salto, 
    aparato_barras = $aparato_barras, 
    aparato_viga = $aparato_viga, 
    aparato_piso = $aparato_piso, 
    aparato_tumbling = $aparato_tumbling, 
    aparato_arzones = $aparato_arzones, 
    aparato_anillos = $aparato_anillos, 
    aparato_barras_paralelas = $aparato_barras_paralelas, 
    aparato_barra_fija = $aparato_barra_fija, 
    aparato_circuitos = $aparato_circuitos, 
    max = $max
    WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$conn->close();
?>
