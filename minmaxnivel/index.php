
<?php include '../aparatos/navbar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Calificaciones</title>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .config-label {
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Configuración de Calificaciones para Lugares</h2>
    <form action="guardar_configuracion.php" method="POST">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Lugar</th>
                    <th>Rango Mínimo</th>
                    <th>Rango Máximo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include '../conexion.php';

                $sql = "SELECT * FROM configuracion_lugares_unificada ORDER BY lugar";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td class='align-middle'>Lugar {$row['lugar']}</td>
                            <td>
                                <label class='config-label' for='rango_min_{$row['id']}'>Calificación mínima para Lugar {$row['lugar']}</label>
                                <input type='number' step='0.01' name='rango_min[{$row['id']}]' 
                                       id='rango_min_{$row['id']}' value='{$row['rango_min']}' 
                                       class='form-control'>
                            </td>
                            <td>
                                <label class='config-label' for='rango_max_{$row['id']}'>Calificación máxima para Lugar {$row['lugar']}</label>
                                <input type='number' step='0.01' name='rango_max[{$row['id']}]' 
                                       id='rango_max_{$row['id']}' value='{$row['rango_max']}' 
                                       class='form-control'>
                            </td>
                          </tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

</body>
</html>
