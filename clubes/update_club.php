<?php
require_once '../conexion.php';

// Obtener los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$sufijo = $_POST['sufijo'];
$img = $_POST['img'];  // Aquí recibimos el nombre de la imagen

// Preparar la consulta para actualizar el club
$sql = $conn->prepare("UPDATE club SET nombre = ?, sufijo = ?, img = ? WHERE id = ?");
$sql->bind_param('sssi', $nombre, $sufijo, $img, $id);

// Ejecutar la consulta
if ($sql->execute()) {
    // Si la actualización es exitosa, responder con éxito
    echo json_encode(['success' => true]);
} else {
    // Si hay un error en la base de datos, responder con un mensaje de error
    echo json_encode(['error' => 'Error en la base de datos: ' . $conn->error]);
}


?>
