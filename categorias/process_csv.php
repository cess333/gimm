<?php
// Incluir el archivo de conexión a la base de datos
include('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv-file'])) {
    $file = $_FILES['csv-file'];
    
    // Verificar que el archivo sea CSV
    if ($file['type'] !== 'text/csv') {
        echo json_encode(['status' => 'error', 'message' => 'El archivo debe ser un CSV.']);
        exit;
    }

    // Leer el archivo CSV
    if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
        // Omitir la primera línea (encabezados)
        fgetcsv($handle, 1000, ',');
        
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // Procesar cada fila del CSV
            if (count($data) == 17) { // Asegúrate de que cada fila tenga 17 elementos
                $categoria = $data[0];
                $ano_1 = (int)$data[1];
                $ano_2 = (int)$data[2];
                $nivel = $data[3];
                $forma = $data[4];
                $rama = (int)$data[5];
                $aparato_salto = (int)$data[6];
                $aparato_barras = (int)$data[7];
                $aparato_viga = (int)$data[8];
                $aparato_piso = (int)$data[9];
                $aparato_tumbling = (int)$data[10];
                $aparato_arzones = (int)$data[11];
                $aparato_anillos = (int)$data[12];
                $aparato_barras_paralelas = (int)$data[13];
                $aparato_barra_fija = (int)$data[14];
                $aparato_circuitos = (int)$data[15];
                $max = (int)$data[16];

                // Verificar si ya existe una categoría con el mismo nombre, año inicio y año fin
                $check_sql = "SELECT COUNT(*) FROM categoria WHERE categoria = ? AND ano_1 = ? AND ano_2 = ?";
                $stmt = $conn->prepare($check_sql);
                $stmt->bind_param("sii", $categoria, $ano_1, $ano_2);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count > 0) {
                    // Si ya existe, cerrar el archivo y devolver un error
                    fclose($handle);
                    echo json_encode([
                        'status' => 'error',
                        'message' => "La categoría '$categoria' con años $ano_1-$ano_2 ya existe en la base de datos."
                    ]);
                    $conn->close();
                    exit;
                }

                // Si no existe, proceder con la inserción usando consulta preparada
                $insert_sql = "INSERT INTO categoria (categoria, ano_1, ano_2, nivel, forma, rama, aparato_salto, aparato_barras, aparato_viga, aparato_piso, aparato_tumbling, aparato_arzones, aparato_anillos, aparato_barras_paralelas, aparato_barra_fija, aparato_circuitos, max) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("siissiiiiiiiiiiii", 
                    $categoria, $ano_1, $ano_2, $nivel, $forma, $rama, 
                    $aparato_salto, $aparato_barras, $aparato_viga, $aparato_piso, 
                    $aparato_tumbling, $aparato_arzones, $aparato_anillos, 
                    $aparato_barras_paralelas, $aparato_barra_fija, $aparato_circuitos, $max
                );

                if ($stmt->execute() !== TRUE) {
                    // Si ocurre un error en el insert, mostrarlo
                    $error = $stmt->error;
                    $stmt->close();
                    fclose($handle);
                    echo json_encode(['status' => 'error', 'message' => 'Error al insertar datos en la base de datos: ' . $error]);
                    $conn->close();
                    exit;
                }
                $stmt->close();
            } else {
                // Si la fila no tiene la cantidad esperada de campos, mostrar un error
                fclose($handle);
                echo json_encode(['status' => 'error', 'message' => 'La fila tiene un número incorrecto de campos.']);
                $conn->close();
                exit;
            }
        }
        fclose($handle);
        
        echo json_encode(['status' => 'success', 'message' => 'Archivo procesado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al leer el archivo CSV.']);
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>