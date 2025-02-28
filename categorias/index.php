<?php require_once "../aparatos/navbar.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Categorías</title>

    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    
    <!-- jQuery, Bootstrap JS y DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style type="text/css">
        /* Fijar el navbar en la parte superior */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000; /* Para asegurarse de que el navbar quede sobre otros elementos */
        }
    </style>
</head>
<body class="bg-light">

    <br>

    <div class="my-5" style="margin: 0 20px;">
        <div class="d-flex justify-content-between gap-2 mb-3">
            <div class="d-flex gap-2">
                <button id="add-category-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Agregar Categoría</button>
                <button id="mass-upload-btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadCsvModal">Cargar CSV</button>
                <button id="generate-excel-btn" class="btn btn-info">Generar Excel</button>
            </div>
            <button id="clear-table-btn" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearTableModal">Limpiar tabla</button>
        </div>

        <!-- Modal para cargar CSV -->
        <div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadCsvModalLabel">Cargar archivo CSV</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="csvUploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="csv-file" class="form-label">Selecciona un archivo CSV:</label>
                                <input type="file" class="form-control" id="csv-file" name="csv-file" accept=".csv" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Formato esperado del CSV:</label>
                                <pre class="bg-light p-2 border rounded">
categoria,ano_1,ano_2,nivel,forma,rama,aparato_salto,aparato_barras,aparato_viga,aparato_piso,aparato_tumbling,aparato_arzones,aparato_anillos,aparato_barras_paralelas,aparato_barra_fija,aparato_circuitos,max
2000-2000,2000,2000,N3,Sumatoria,1,1,1,1,1,0,0,0,0,0,0,10
2001-2001,2001,2001,N2,Porcentaje,2,1,1,1,1,0,0,0,0,0,0,10
                                </pre>
                                <small class="text-muted">
                                    Nota: Los valores de los aparatos deben ser 1 (activo) o 0 (inactivo). Rama: 1 (Varonil) o 2 (Femenil).
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary">Subir archivo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para limpiar tabla -->
        <div class="modal fade" id="clearTableModal" tabindex="-1" aria-labelledby="clearTableModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clearTableModalLabel">Confirmar limpieza de tabla</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas limpiar la tabla "categoria"? Esta acción eliminará todas las categorías y podría afectar a los participantes asociados.</p>
                        <p>Tiempo restante: <span id="countdown">6</span> segundos</p>
                        <button id="confirm-clear-btn" class="btn btn-danger">Confirmar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <table id="categories-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Categoría</th>
                    <th>Año Inicio</th>
                    <th>Año Fin</th>
                    <th>Nivel</th>
                    <th>Forma</th>
                    <th>Rama</th>
                    <th>Salto</th>
                    <th>Barras</th>
                    <th>Viga</th>
                    <th>Piso</th>
                    <th>Tumbling</th>
                    <th>Arzones</th>
                    <th>Anillos</th>
                    <th>Barras Paralelas</th>
                    <th>Barra Fija</th>
                    <th>Circuitos</th>
                    <th>Max</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Las filas se llenarán automáticamente con DataTables -->
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar una categoría -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Agregar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <div class="mb-3">
                            <label for="generated-category" class="form-label">Categoría (Generada automáticamente):</label>
                            <input type="text" class="form-control" id="generated-category" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="ano_1" class="form-label">Año Inicio:</label>
                            <input type="number" class="form-control" id="ano_1" required>
                        </div>
                        <div class="mb-3">
                            <label for="ano_2" class="form-label">Año Fin:</label>
                            <input type="number" class="form-control" id="ano_2" required>
                        </div>
                        <div class="mb-3">
                            <label for="nivel" class="form-label">Nivel:</label>
                            <select class="form-control" id="nivel" required>
                                <option value="" selected disabled>Selecciona nivel</option>
                                <option value="PN">PN</option>
                                <option value="N1">N1</option>
                                <option value="N2">N2</option>
                                <option value="N3">N3</option>
                                <option value="N4">N4</option>
                                <option value="N5">N5</option>
                                <option value="N6">N6</option>
                                <option value="N7">N7</option>
                                <option value="N8">N8</option>
                                <option value="N9">N9</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="forma" class="form-label">Forma:</label>
                            <select class="form-control" id="forma" required>
                                <option value="Porcentaje">Porcentaje</option>
                                <option value="Sumatoria">Sumatoria</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rama" class="form-label">Rama:</label>
                            <select class="form-control" id="rama" required>
                                <option value="1">Varonil</option>
                                <option value="2">Femenil</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="max" class="form-label">Max:</label>
                            <input type="number" class="form-control" id="max">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Aparatos:</label>
                            <div>
                                <input type="checkbox" id="aparato_salto" checked> Salto
                                <input type="checkbox" id="aparato_barras" checked> Barras
                                <input type="checkbox" id="aparato_viga" checked> Viga
                                <input type="checkbox" id="aparato_piso" checked> Piso
                                <input type="checkbox" id="aparato_tumbling" checked> Tumbling
                                <input type="checkbox" id="aparato_arzones" checked> Arzones
                                <input type="checkbox" id="aparato_anillos" checked> Anillos
                                <input type="checkbox" id="aparato_barras_paralelas" checked> Barras Paralelas
                                <input type="checkbox" id="aparato_barra_fija" checked> Barra Fija
                                <input type="checkbox" id="aparato_circuitos" checked> Circuitos
                            </div>
                        </div>
                        <button type="button" id="save-category-btn" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar una categoría -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="edit-category-id">
                        <div class="mb-3">
                            <label for="edit-generated-category" class="form-label">Categoría (Generada automáticamente):</label>
                            <input type="text" class="form-control" id="edit-generated-category" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="edit-ano_1" class="form-label">Año Inicio:</label>
                            <input type="number" class="form-control" id="edit-ano_1" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-ano_2" class="form-label">Año Fin:</label>
                            <input type="number" class="form-control" id="edit-ano_2" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-nivel" class="form-label">Nivel:</label>
                            <input type="text" class="form-control" id="edit-nivel" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-forma" class="form-label">Forma:</label>
                            <select class="form-control" id="edit-forma" required>
                                <option value="Porcentaje">Porcentaje</option>
                                <option value="Sumatoria">Sumatoria</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-rama" class="form-label">Rama:</label>
                            <select class="form-control" id="edit-rama" required>
                                <option value="1">Varonil</option>
                                <option value="2">Femenil</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-max" class="form-label">Max:</label>
                            <input type="number" class="form-control" id="edit-max">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Aparatos:</label>
                            <div>
                                <input type="checkbox" id="edit-aparato_salto"> Salto
                                <input type="checkbox" id="edit-aparato_barras"> Barras
                                <input type="checkbox" id="edit-aparato_viga"> Viga
                                <input type="checkbox" id="edit-aparato_piso"> Piso
                                <input type="checkbox" id="edit-aparato_tumbling"> Tumbling
                                <input type="checkbox" id="edit-aparato_arzones"> Arzones
                                <input type="checkbox" id="edit-aparato_anillos"> Anillos
                                <input type="checkbox" id="edit-aparato_barras_paralelas"> Barras Paralelas
                                <input type="checkbox" id="edit-aparato_barra_fija"> Barra Fija
                                <input type="checkbox" id="edit-aparato_circuitos"> Circuitos
                            </div>
                        </div>
                        <button type="button" id="update-category-btn" class="btn btn-primary">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editAnoInicioInput = document.getElementById('edit-ano_1');
        const editAnoFinInput = document.getElementById('edit-ano_2');
        const editGeneratedCategoryInput = document.getElementById('edit-generated-category');

        function updateEditCategory() {
            const anoInicio = editAnoInicioInput.value;
            const anoFin = editAnoFinInput.value;
            if (anoInicio && anoFin) {
                editGeneratedCategoryInput.value = `${anoInicio}-${anoFin}`;
            } else {
                editGeneratedCategoryInput.value = '';
            }
        }

        editAnoInicioInput.addEventListener('input', updateEditCategory);
        editAnoFinInput.addEventListener('input', updateEditCategory);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const anoInicioInput = document.getElementById('ano_1');
        const anoFinInput = document.getElementById('ano_2');
        const generatedCategoryInput = document.getElementById('generated-category');

        function updateCategory() {
            const anoInicio = anoInicioInput.value;
            const anoFin = anoFinInput.value;
            if (anoInicio && anoFin) {
                generatedCategoryInput.value = `${anoInicio}-${anoFin}`;
            } else {
                generatedCategoryInput.value = '';
            }
        }

        anoInicioInput.addEventListener('input', updateCategory);
        anoFinInput.addEventListener('input', updateCategory);
    });

    $(document).ready(function() {
        var table = $('#categories-table').DataTable({
            ajax: {
                url: 'get_categories.php',
                dataSrc: ''
            },
            columns: [
                { data: 'id' },
                { data: 'categoria' },
                { data: 'ano_1' },
                { data: 'ano_2' },
                { data: 'nivel' },
                { data: 'forma' },
                { 
                    data: 'rama',
                    render: function(data) {
                        return data === 1 ? 'Varonil' : data === 2 ? 'Femenil' : 'Desconocido';
                    }
                },
                { data: 'aparato_salto', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_barras', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_viga', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_piso', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_tumbling', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_arzones', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_anillos', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_barras_paralelas', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_barra_fija', render: data => (data ? 'Sí' : 'No') },
                { data: 'aparato_circuitos', render: data => (data ? 'Sí' : 'No') },
                { data: 'max' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm" onclick="editCategory(${row.id}, '${row.categoria}', ${row.ano_1}, ${row.ano_2}, '${row.nivel}', '${row.forma}', ${row.rama}, ${row.aparato_salto}, ${row.aparato_barras}, ${row.aparato_viga}, ${row.aparato_piso}, ${row.aparato_tumbling}, ${row.aparato_arzones}, ${row.aparato_anillos}, ${row.aparato_barras_paralelas}, ${row.aparato_barra_fija}, ${row.aparato_circuitos}, ${row.max})">Editar</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Eliminar</button>
                        `;
                    }
                }
            ]
        });

        // Agregar nueva categoría
        $('#save-category-btn').click(function() {
            const button = $(this);
            button.prop('disabled', true);

            const categoryData = {
                name: $('#generated-category').val(),
                ano_1: $('#ano_1').val(),
                ano_2: $('#ano_2').val(),
                nivel: $('#nivel').val(),
                forma: $('#forma').val(),
                rama: $('#rama').val(),
                aparato_salto: $('#aparato_salto').is(':checked') ? 1 : 0,
                aparato_barras: $('#aparato_barras').is(':checked') ? 1 : 0,
                aparato_viga: $('#aparato_viga').is(':checked') ? 1 : 0,
                aparato_piso: $('#aparato_piso').is(':checked') ? 1 : 0,
                aparato_tumbling: $('#aparato_tumbling').is(':checked') ? 1 : 0,
                aparato_arzones: $('#aparato_arzones').is(':checked') ? 1 : 0,
                aparato_anillos: $('#aparato_anillos').is(':checked') ? 1 : 0,
                aparato_barras_paralelas: $('#aparato_barras_paralelas').is(':checked') ? 1 : 0,
                aparato_barra_fija: $('#aparato_barra_fija').is(':checked') ? 1 : 0,
                aparato_circuitos: $('#aparato_circuitos').is(':checked') ? 1 : 0,
                max: $('#max').val()
            };

            $.ajax({
                url: 'add_category.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(categoryData),
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('#addCategoryModal').modal('hide');
                        $('#categoryForm')[0].reset();
                        table.ajax.reload();
                        alert('Categoría agregada exitosamente');
                    } else {
                        alert('Error: ' + res.message);
                    }
                },
                error: function() {
                    alert('Error al agregar la categoría. Inténtalo de nuevo.');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        });

        // Abrir modal de edición con los datos de la categoría
        window.editCategory = function(id, categoria, ano_1, ano_2, nivel, forma, rama, aparato_salto, aparato_barras, aparato_viga, aparato_piso, aparato_tumbling, aparato_arzones, aparato_anillos, aparato_barras_paralelas, aparato_barra_fija, aparato_circuitos, max) {
            $('#edit-category-id').val(id);
            $('#edit-ano_1').val(ano_1);
            $('#edit-ano_2').val(ano_2);
            $('#edit-nivel').val(nivel);
            $('#edit-forma').val(forma);
            $('#edit-rama').val(rama);
            $('#edit-max').val(max);
            $('#edit-generated-category').val(`${ano_1}-${ano_2}`);
            $('#edit-aparato_salto').prop('checked', aparato_salto === 1);
            $('#edit-aparato_barras').prop('checked', aparato_barras === 1);
            $('#edit-aparato_viga').prop('checked', aparato_viga === 1);
            $('#edit-aparato_piso').prop('checked', aparato_piso === 1);
            $('#edit-aparato_tumbling').prop('checked', aparato_tumbling === 1);
            $('#edit-aparato_arzones').prop('checked', aparato_arzones === 1);
            $('#edit-aparato_anillos').prop('checked', aparato_anillos === 1);
            $('#edit-aparato_barras_paralelas').prop('checked', aparato_barras_paralelas === 1);
            $('#edit-aparato_barra_fija').prop('checked', aparato_barra_fija === 1);
            $('#edit-aparato_circuitos').prop('checked', aparato_circuitos === 1);

            $('#editCategoryModal').modal('show');
        };

        // Actualizar categoría
        $('#update-category-btn').click(function() {
            const categoryData = {
                id: $('#edit-category-id').val(),
                name: $('#edit-generated-category').val(),
                ano_1: $('#edit-ano_1').val(),
                ano_2: $('#edit-ano_2').val(),
                nivel: $('#edit-nivel').val(),
                forma: $('#edit-forma').val(),
                rama: $('#edit-rama').val(),
                max: $('#edit-max').val(),
                aparato_salto: $('#edit-aparato_salto').is(':checked') ? 1 : 0,
                aparato_barras: $('#edit-aparato_barras').is(':checked') ? 1 : 0,
                aparato_viga: $('#edit-aparato_viga').is(':checked') ? 1 : 0,
                aparato_piso: $('#edit-aparato_piso').is(':checked') ? 1 : 0,
                aparato_tumbling: $('#edit-aparato_tumbling').is(':checked') ? 1 : 0,
                aparato_arzones: $('#edit-aparato_arzones').is(':checked') ? 1 : 0,
                aparato_anillos: $('#edit-aparato_anillos').is(':checked') ? 1 : 0,
                aparato_barras_paralelas: $('#edit-aparato_barras_paralelas').is(':checked') ? 1 : 0,
                aparato_barra_fija: $('#edit-aparato_barra_fija').is(':checked') ? 1 : 0,
                aparato_circuitos: $('#edit-aparato_circuitos').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: 'update_category.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(categoryData),
                success: function() {
                    $('#editCategoryModal').modal('hide');
                    table.ajax.reload();
                },
                error: function() {
                    alert('Error al actualizar la categoría');
                }
            });
        });

        // Evento delegado para eliminar categoría
        $('#categories-table').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
                $.ajax({
                    url: 'delete_category.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            table.ajax.reload();
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function() {
                        alert('Error al intentar eliminar la categoría.');
                    }
                });
            }
        });

        // Manejar el botón de generar Excel (genera CSV)
        $('#generate-excel-btn').click(function() {
            window.location.href = 'generate_categories_excel.php';
        });

        // Manejar el modal de limpiar tabla
        $('#clearTableModal').on('show.bs.modal', function() {
            let timeLeft = 6;
            $('#countdown').text(timeLeft);
            $('#confirm-clear-btn').prop('disabled', true);

            const countdownInterval = setInterval(function() {
                timeLeft--;
                $('#countdown').text(timeLeft);
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    $('#confirm-clear-btn').prop('disabled', false);
                }
            }, 1000);
        });

        // Manejar el botón de confirmar limpieza
        $('#confirm-clear-btn').click(function() {
            $.ajax({
                url: 'clear_categories_table.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#clearTableModal').modal('hide');
                        alert('La tabla "categoria" ha sido limpiada correctamente.');
                        table.ajax.reload();
                    } else {
                        alert('Error al limpiar la tabla: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Hubo un error al intentar limpiar la tabla: ' + error);
                }
            });
        });

        // Mostrar el ejemplo del CSV al abrir el modal (no es necesario JS adicional aquí, ya está en el HTML)
    });

    // Manejo del formulario de carga de CSV
    $('#csvUploadForm').submit(function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = $('button[type="submit"]');
        submitButton.prop('disabled', true);

        $.ajax({
            url: 'process_csv.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert('Archivo CSV cargado exitosamente.');
                        $('#uploadCsvModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Error: ' + res.message);
                    }
                } catch (error) {
                    alert('Error al procesar la respuesta del servidor.');
                    console.error('Error en JSON.parse:', error, response);
                }
            },
            error: function() {
                alert('Error al cargar el archivo CSV. Intenta de nuevo.');
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });
    </script>
</body>
</html>