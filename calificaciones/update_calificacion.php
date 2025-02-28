<?php
include('../conexion.php');

// Verificar que los parámetros estén correctamente enviados
if (isset($_POST['id']) && isset($_POST['column']) && isset($_POST['value'])) {
    $id = $_POST['id']; // ID del participante
    $column = $_POST['column']; // Columna que se está actualizando (salto, barras, ronda, etc.)
    $value = $_POST['value']; // Nuevo valor para la columna

    // Determinar la tabla según la ronda seleccionada (GET ronda)
    $ronda_seleccionada = isset($_GET['ronda']) ? (int)$_GET['ronda'] : 0;
    $table = ($ronda_seleccionada == 0) ? 'calificacion' : 'calificacion_ronda';

    // Lista de columnas válidas, incluyendo ronda
    $valid_columns = [
        'salto', 'barras', 'viga', 'piso', 'tumbling', 'arzones', 'anillos', 
        'barras_paralelas', 'barra_fija', 'circuitos', 'panel', 'ronda'
    ];
    if (!in_array($column, $valid_columns)) {
        echo "Columna no válida.";
        exit;
    }

    // Preparar la consulta según el tipo de columna
    if ($column === 'ronda') {
        // Ronda es un entero (INT), puede ser NULL
        if ($value !== "" && !is_numeric($value)) {
            echo "Valor no válido para ronda (debe ser un número).";
            exit;
        }
        $sql = "UPDATE $table SET ronda = ? WHERE participante_id = ?";
        $stmt = $conn->prepare($sql);
        if ($value === "" || $value === null) {
            $stmt->bind_param("si", $value, $id); // "s" para NULL, "i" para id
        } else {
            $stmt->bind_param("ii", $value, $id); // "ii" para entero
        }
    } elseif ($column === 'panel') {
        // Panel es un string (VARCHAR), no puede ser NULL según tu esquema
        if ($value === "" || $value === null) {
            echo "El valor de panel no puede estar vacío.";
            exit;
        }
        $sql = "UPDATE $table SET panel = ? WHERE participante_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $value, $id); // "s" para string
    } else {
        // Otros campos son decimales (DECIMAL), pueden ser NULL
        if ($value !== "" && !is_numeric($value)) {
            echo "Valor no válido (debe ser un número).";
            exit;
        }
        $sql = "UPDATE $table SET $column = ? WHERE participante_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $value, $id); // "d" para decimal
    }

    // Ejecutar la consulta y verificar si fue exitosa
    if ($stmt->execute()) {
        echo "Registro actualizado correctamente.";
    } else {
        echo "Error al actualizar el registro: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Faltan parámetros.";
}

$conn->close();
?>