<?php
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];
    $mensaje = '';
    $errores = []; // Para acumular errores
    
    if (($handle = fopen($archivo, "r")) !== FALSE) {
        $conn->begin_transaction();

        try {
            $fila = 0;
            $hasErrors = false; // Bandera para indicar si hay errores

            while (($datos = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $fila++;
                if ($fila == 1) continue; // Omite la cabecera

                // Validar datos y manejar errores
                $nombre = trim($datos[0]);
                $rama = trim($datos[1]);
                $ano_nacimiento = trim($datos[2]);
                $club_nombre = trim($datos[3]);
                $nivel = trim($datos[4]);
                $elegido = isset($datos[5]) ? trim($datos[5]) : 'no'; // Valor por defecto 'no' si no está presente

                // Reiniciar variables para cada participante
                $club_id = null;
                $categoria_id = null;

                // Validar rama
                if ($rama != 1 && $rama != 2) {
                    $errores[] = "Error en fila $fila: Rama '$rama' no válida para el participante '$nombre'.";
                    $hasErrors = true;
                    continue;
                }

                // Validar club
                $stmt_club = $conn->prepare("SELECT id FROM club WHERE nombre = ?");
                $stmt_club->bind_param("s", $club_nombre);
                $stmt_club->execute();
                $stmt_club->bind_result($club_id);
                $stmt_club->fetch();
                $stmt_club->close();

                if (!$club_id) {
                    $errores[] = "Error en fila $fila: El club '$club_nombre' no existe para el participante '$nombre'.";
                    $hasErrors = true;
                    continue;
                }

                // Validar nivel
                $stmt_nivel = $conn->prepare("SELECT DISTINCT nivel FROM categoria WHERE nivel = ?");
                $stmt_nivel->bind_param("s", $nivel);
                $stmt_nivel->execute();
                $stmt_nivel->store_result();
                if ($stmt_nivel->num_rows == 0) {
                    $errores[] = "Error en fila $fila: Nivel '$nivel' no registrado para el participante '$nombre'.";
                    $hasErrors = true;
                    $stmt_nivel->close();
                    continue;
                }
                $stmt_nivel->close();

                // Validar categoría
                $stmt_categoria = $conn->prepare("SELECT id FROM categoria WHERE rama = ? AND nivel = ? AND ? BETWEEN ano_1 AND ano_2 LIMIT 1");
                $stmt_categoria->bind_param("isi", $rama, $nivel, $ano_nacimiento);
                $stmt_categoria->execute();
                $stmt_categoria->store_result();
                
                if ($stmt_categoria->num_rows === 0) {
                    $errores[] = "Error en fila $fila: No se encontró una categoría para el participante '$nombre'.";
                    $hasErrors = true;
                    $stmt_categoria->close();
                    continue;
                }

                $stmt_categoria->bind_result($categoria_id);
                $stmt_categoria->fetch();
                $stmt_categoria->close();

                // Validar elegido (solo 'sí' o 'no')
                if (!in_array(strtolower($elegido), ['sí', 'si', 'no', ''])) {
                    $errores[] = "Error en fila $fila: Valor '$elegido' no válido para 'elegido'. Use 'sí' o 'no' para el participante '$nombre'.";
                    $hasErrors = true;
                    continue;
                }
                $elegido = (strtolower($elegido) === 'sí' || strtolower($elegido) === 'si') ? 'sí' : 'no'; // Normalizar a 'sí' o 'no'

                // Solo insertar si no hay errores acumulados hasta ahora
                if ($hasErrors) {
                    continue; // Saltar inserción si ya hay errores
                }

                // Insertar con la columna elegido
                $stmt = $conn->prepare("INSERT INTO participante (nombre, rama, ano_nacimiento, club_id, categoria_id, elegido) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisis", $nombre, $rama, $ano_nacimiento, $club_id, $categoria_id, $elegido);

                if (!$stmt->execute()) {
                    $errores[] = "Error en fila $fila: No se pudo insertar al participante '$nombre'.";
                    $hasErrors = true;
                }
                $stmt->close();
            }

            fclose($handle);

            // Si hay errores, lanzar excepción y no commit
            if ($hasErrors) {
                throw new Exception(implode("<br>", $errores));
            }

            $conn->commit();
            $mensaje = "Carga masiva completada con éxito.";
        } catch (Exception $e) {
            $conn->rollback();
            $mensaje = "Error durante la carga masiva: <br>" . $e->getMessage();
        }
    } else {
        $mensaje = "Error al abrir el archivo CSV.";
    }

    // Redirigir con mensaje único
    header("Location: tabla_participantes.php?mensaje=" . urlencode($mensaje));
    exit();
}
?>