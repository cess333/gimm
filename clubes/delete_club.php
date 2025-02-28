<?php
require_once '../conexion.php';

$id = $_POST['id'];

$sql = $conn->prepare("DELETE FROM club WHERE id = ?");
$sql->bind_param('i', $id);

if ($sql->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => $conn->error]);
}

$conn->close();
?>
