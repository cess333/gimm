<?php
// Incluir conexión a la base de datos
include '../conexion.php';

// Configurar errores para depuración (puedes desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Decodificar el JSON recibido
$data = json_decode(file_get_contents("php://input"), true);

// Validar datos recibidos
if (
    empty($data['name']) ||
    empty($data['ano_1']) ||
    empty($data['ano_2']) ||
    empty($data['nivel']) ||
    empty($data['forma'])
) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

// Recuperar los valores del JSON con valores predeterminados
$categoria = $data['name'];
$ano_1 = intval($data['ano_1']);
$ano_2 = intval($data['ano_2']);
$nivel = $data['nivel'];
$forma = $data['forma'];
$rama = isset($data['rama']) ? intval($data['rama']) : 1; // Valor predeterminado para rama (1: Varonil)
$max = isset($data['max']) ? intval($data['max']) : 10;    // Valor predeterminado para max

// Aparatos con valores predeterminados
$aparato_salto = isset($data['aparato_salto']) ? intval($data['aparato_salto']) : 0;
$aparato_barras = isset($data['aparato_barras']) ? intval($data['aparato_barras']) : 0;
$aparato_viga = isset($data['aparato_viga']) ? intval($data['aparato_viga']) : 0;
$aparato_piso = isset($data['aparato_piso']) ? intval($data['aparato_piso']) : 0;
$aparato_tumbling = isset($data['aparato_tumbling']) ? intval($data['aparato_tumbling']) : 0;
$aparato_arzones = isset($data['aparato_arzones']) ? intval($data['aparato_arzones']) : 0;
$aparato_anillos = isset($data['aparato_anillos']) ? intval($data['aparato_anillos']) : 0;
$aparato_barras_paralelas = isset($data['aparato_barras_paralelas']) ? intval($data['aparato_barras_paralelas']) : 0;
$aparato_barra_fija = isset($data['aparato_barra_fija']) ? intval($data['aparato_barra_fija']) : 0;
$aparato_circuitos = isset($data['aparato_circuitos']) ? intval($data['aparato_circuitos']) : 0;

// Verificar si ano_1 o ano_2 ya existen con la misma rama
$checkQuery = "SELECT ano_1, ano_2, rama FROM categoria WHERE (ano_1 = ? OR ano_2 = ?) AND rama = ?";
$stmtCheck = $conn->prepare($checkQuery);
$stmtCheck->bind_param("iii", $ano_1, $ano_2, $rama);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    $duplicatedYears = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['ano_1'] == $ano_1) {
            $duplicatedYears[] = $ano_1;
        }
        if ($row['ano_2'] == $ano_2) {
            $duplicatedYears[] = $ano_2;
        }
    }
    $duplicatedYears = implode(", ", array_unique($duplicatedYears));
    echo json_encode(['status' => 'error', 'message' => "El(los) año(s) $duplicatedYears ya están asociado(s) a otra categoría con la misma rama"]);
    exit;
}


// Preparar la consulta SQL
$sql = "INSERT INTO categoria (
    categoria, 
    ano_1, 
    ano_2, 
    nivel, 
    forma, 
    rama, 
    aparato_salto, 
    aparato_barras, 
    aparato_viga, 
    aparato_piso, 
    aparato_tumbling, 
    aparato_arzones, 
    aparato_anillos, 
    aparato_barras_paralelas, 
    aparato_barra_fija, 
    aparato_circuitos, 
    max
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
    exit;
}

// Asociar parámetros
$stmt->bind_param(
    "siissiiiiiiiiiiii",
    $categoria,
    $ano_1,
    $ano_2,
    $nivel,
    $forma,
    $rama,
    $aparato_salto,
    $aparato_barras,
    $aparato_viga,
    $aparato_piso,
    $aparato_tumbling,
    $aparato_arzones,
    $aparato_anillos,
    $aparato_barras_paralelas,
    $aparato_barra_fija,
    $aparato_circuitos,
    $max
);

// Ejecutar y manejar la respuesta
$response = [];
if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['id'] = $stmt->insert_id; // Obtener el ID insertado
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error en la ejecución de la consulta: ' . $stmt->error;
}

// Enviar la respuesta como JSON
echo json_encode($response);

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
