<?php
// Incluir el archivo de conexión
include('../conexion.php');

// Consulta para obtener todas las calificaciones de todos los participantes
$sql = "
SELECT 
    c.participante_id,
    c.salto, c.barras, c.viga, c.piso, c.tumbling, c.arzones, c.anillos, c.barras_paralelas, c.barra_fija, c.circuitos, 
    cat.aparato_salto, cat.aparato_barras, cat.aparato_viga, cat.aparato_piso, cat.aparato_tumbling, cat.aparato_arzones, 
    cat.aparato_anillos, cat.aparato_barras_paralelas, cat.aparato_barra_fija, cat.aparato_circuitos
FROM calificacion c
JOIN participante p ON c.participante_id = p.id
JOIN categoria cat ON p.categoria_id = cat.id
";

// Ejecutar la consulta
$result = $conn->query($sql);

// Variable para verificar si algún botón debe estar desactivado
$disableButtons = false;

// Verificar si la consulta devuelve resultados
if ($result && $result->num_rows > 0) {
    // Recorrer todas las filas de la consulta
    while ($row = $result->fetch_assoc()) {
        // Array de los aparatos y sus respectivas columnas de calificación
        $aparatos = [
            'aparato_salto' => 'salto',
            'aparato_barras' => 'barras',
            'aparato_viga' => 'viga',
            'aparato_piso' => 'piso',
            'aparato_tumbling' => 'tumbling',
            'aparato_arzones' => 'arzones',
            'aparato_anillos' => 'anillos',
            'aparato_barras_paralelas' => 'barras_paralelas',
            'aparato_barra_fija' => 'barra_fija',
            'aparato_circuitos' => 'circuitos'
        ];

        // Recorremos los aparatos para verificar las condiciones
        foreach ($aparatos as $aparato => $campo_calificacion) {
            // Si el aparato tiene el valor '1' en la categoría y la calificación está vacía (nula)
            if ($row[$aparato] == 1 && is_null($row[$campo_calificacion])) {
                $disableButtons = true;  // Si falta calificación, deshabilitamos los botones
                break 2;  // Salir del bucle más externo, ya que con uno es suficiente
            }
        }
    }
} else {
    $disableButtons = true;  // Si no encontramos participantes o la consulta falla
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Botones con Hover Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: #007bff;
            color: white;
            font-size: 1.5rem;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            color: white;
        }
        .btn-disabled {
            background-color: #b0b0b0;
            pointer-events: none;
            cursor: not-allowed;
        }
        .alert-custom {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación solo una vez -->
    <?php include('../aparatos/navbar.php'); ?>

    <div class="container text-center mt-5">
        <h1 class="mb-4">Selecciona el orden de las calificaciones</h1>
        
        <!-- Mensaje informativo si los botones están deshabilitados -->
        <?php if ($disableButtons): ?>
            <div class="alert alert-custom">
                Faltan calificaciones en algun aparato. 
                Por favor, asegúrate de ingresar todas las calificaciones necesarias para continuar.
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-center gap-3">
            <!-- Botón para porcategoria.php -->
            <a href="porcategoria.php" class="btn btn-custom <?php echo $disableButtons ? 'btn-disabled' : ''; ?>">Por Categoría</a>
            <!-- Botón para porpanel.php -->
            <a href="porpanel.php" class="btn btn-custom <?php echo $disableButtons ? 'btn-disabled' : ''; ?>">Por Panel</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
