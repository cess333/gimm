<?php
session_start();
include 'navbar.php';

// Definir valores predeterminados si no existen en la sesión
if (!isset($_SESSION['config'])) {
    $_SESSION['config'] = [
        'pantalla4' => [
            'background_color' => '#6436A9',
            'animations' => true,
            'zoom' => 80  // Zoom predeterminado a 80 sin el símbolo %
        ],
        'pantalla' => [
            'background_color' => '#000428',
            'animations' => true,
            'zoom' => 80,  // Zoom predeterminado a 80 sin el símbolo %
            'hide_info_after' => 10 * 60 * 1000, // 10 minutos en milisegundos
            'hide_info_after_unit' => 'minutos'
        ]
    ];
}

// Procesar cambios si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['config']['pantalla4'] = [
        'background_color' => $_POST['pantalla4_background_color'] ?? '#6436A9',
        'animations' => isset($_POST['pantalla4_animations']),
        'zoom' => intval($_POST['pantalla4_zoom'] ?? 80) // Aseguramos que sea un número entero sin %
    ];
    
    $hide_info_after_value = intval($_POST['pantalla_hide_info_after']);

    $_SESSION['config']['pantalla'] = [
        'background_color' => $_POST['pantalla_background_color'] ?? '#000428',
        'animations' => isset($_POST['pantalla_animations']),
        'zoom' => intval($_POST['pantalla_zoom'] ?? 80), // Aseguramos que sea un número entero sin %
        'hide_info_after' => $hide_info_after_value * 60 * 1000, // Convertir minutos a milisegundos
        'hide_info_after_unit' => 'minutos'
    ];
    
    header("Location: config.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Efecto hover en los botones */
        .btn-aparato {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .btn-aparato:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
        }
        .config-section {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .config-section h4 {
            margin-top: 0;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Selecciona un Aparato</h2>

    <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
        <a href="pantalla.php?aparato=Salto" class="btn btn-primary btn-lg btn-aparato" target="_blank">Salto</a>
        <a href="pantalla.php?aparato=Barras" class="btn btn-success btn-lg btn-aparato" target="_blank">Barras</a>
        <a href="pantalla.php?aparato=Piso" class="btn btn-danger btn-lg btn-aparato" target="_blank">Piso</a>
        <a href="pantalla.php?aparato=Viga" class="btn btn-info btn-lg btn-aparato" target="_blank">Viga</a>
        <a href="pantalla4.php" class="btn btn-warning btn-lg btn-aparato" target="_blank">Todos</a>
    </div>

    <!-- Botón separado debajo de los demás -->
    <div class="d-flex justify-content-center mt-4">
        <button class="btn btn-dark btn-lg btn-aparato" onclick="abrirTodasLasPantallas()">Abrir Todas</button>
    </div>

    <h3 class="mt-5 text-center">Configuración</h3>
    <form method="POST" class="mt-4">
        <div class="row">
            <!-- Configuración para pantalla4.php -->
            <div class="col-md-6">
                <div class="config-section">
                    <h4>Configuración para Pantalla de 4 Calificaciones</h4>
                    <div class="mb-3">
                        <label for="pantalla4_background_color" class="form-label">Color de fondo:</label>
                        <input type="color" id="pantalla4_background_color" name="pantalla4_background_color" class="form-control form-control-color" 
                               value="<?= htmlspecialchars($_SESSION['config']['pantalla4']['background_color']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="pantalla4_zoom" class="form-label">Zoom (%):</label>
                        <input type="number" id="pantalla4_zoom" name="pantalla4_zoom" class="form-control"
                               value="<?= intval($_SESSION['config']['pantalla4']['zoom']); ?>" min="50" max="150">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" id="pantalla4_animations" name="pantalla4_animations" class="form-check-input" 
                               <?= $_SESSION['config']['pantalla4']['animations'] ? 'checked' : ''; ?>>
                        <label for="pantalla4_animations" class="form-check-label">Activar Animaciones</label>
                    </div>
                </div>
            </div>

            <!-- Configuración para pantalla.php -->
            <div class="col-md-6">
                <div class="config-section">
                    <h4>Configuración para Pantalla de Piso</h4>
                    <div class="mb-3">
                        <label for="pantalla_background_color" class="form-label">Color de fondo:</label>
                        <input type="color" id="pantalla_background_color" name="pantalla_background_color" class="form-control form-control-color" 
                               value="<?= htmlspecialchars($_SESSION['config']['pantalla']['background_color']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="pantalla_zoom" class="form-label">Zoom (%):</label>
                        <input type="number" id="pantalla_zoom" name="pantalla_zoom" class="form-control"
                               value="<?= intval($_SESSION['config']['pantalla']['zoom']); ?>" min="50" max="150">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" id="pantalla_animations" name="pantalla_animations" class="form-check-input" 
                               <?= $_SESSION['config']['pantalla']['animations'] ? 'checked' : ''; ?>>
                        <label for="pantalla_animations" class="form-check-label">Activar Animaciones</label>
                    </div>

                    <div class="mb-3">
                        <label for="pantalla_hide_info_after" class="form-label">Tiempo para Ocultar Info (minutos):</label>
                        <input type="number" id="pantalla_hide_info_after" name="pantalla_hide_info_after" class="form-control"
                               value="<?= intval($_SESSION['config']['pantalla']['hide_info_after'] / 60000) ?>" min="1" max="60">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-3">Guardar Configuración</button>
    </form>
</div>

<script>
    function abrirTodasLasPantallas() {
        window.open("pantalla.php?aparato=Salto", "_blank");
        window.open("pantalla.php?aparato=Barras", "_blank");
        window.open("pantalla.php?aparato=Piso", "_blank");
        window.open("pantalla.php?aparato=Viga", "_blank");
        window.open("pantalla4.php", "_blank");
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>