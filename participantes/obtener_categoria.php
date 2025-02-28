<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir datos del cliente
    $rama = $_POST['rama']; // "1" o "2" directamente desde el formulario
    $nivel = $_POST['nivel']; // Nivel seleccionado del formulario
    $ano_nacimiento = intval($_POST['ano_nacimiento']); // Convertir el año a entero

    // Validar los valores recibidos
    if (!in_array($rama, ["1", "2"]) || empty($nivel) || empty($ano_nacimiento)) {
        echo "Datos inválidos"; // Mensaje en caso de datos incorrectos
        exit;
    }

    // Consulta para encontrar la categoría
    $categoria_query = "SELECT categoria 
                        FROM categoria 
                        WHERE rama = ? 
                          AND nivel = ? 
                          AND ? BETWEEN ano_1 AND ano_2 
                        LIMIT 1";

    $stmt = $conn->prepare($categoria_query);
    $stmt->bind_param("isi", $rama, $nivel, $ano_nacimiento);
    $stmt->execute();
    $stmt->bind_result($categoria);

    // Validar si se encontró una categoría
    if ($stmt->fetch()) {
        echo $categoria; // Devolver la categoría encontrada
    } else {
        echo "Categoría no encontrada"; // Mensaje en caso de no encontrar categoría
    }

    $stmt->close();
    $conn->close();
}
?>
