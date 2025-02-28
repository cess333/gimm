<?php require_once "../aparatos/navbar.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Clubes</title>

    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    
    <!-- jQuery, Bootstrap JS y DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="d-flex justify-content-between gap-2 mb-3">
            <div class="d-flex gap-2">
                <button id="add-club-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClubModal">Agregar Club</button>
                <button id="bulk-upload-btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">Carga Masiva</button>
                <button id="generate-excel-btn" class="btn btn-info">Generar Excel</button>
            </div>
            <button id="clear-table-btn" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearTableModal">Limpiar tabla</button>
        </div>

        <!-- Modal para carga masiva -->
        <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkUploadModalLabel">Carga Masiva de Clubes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Ejemplo del formato CSV:</strong></p>
                        <ul>
                            <li>Debe contener tres columnas: <strong>Nombre</strong>, <strong>Sufijo</strong> y, opcionalmente, <strong>Imagen</strong>.</li>
                            <li>La primera fila debe ser la cabecera con los títulos de las columnas (por ejemplo, "Nombre,Sufijo,Img").</li>
                            <li>Las columnas deben estar separadas por comas (,).</li>
                            <li>El <strong>Nombre</strong> y el <strong>Sufijo</strong> son obligatorios para cada club.</li>
                            <li>Si un <strong>Nombre</strong> ya existe en la base de datos o aparece varias veces en el CSV, solo se cargará la primera ocurrencia.</li>
                        </ul>
                        <p><strong>Ejemplo:</strong><br>
                            <code>Nombre,Sufijo,Img<br>Club Deportivo,CD,logo1.jpg<br>Club Olímpico,CO,logo2.jpg</code>
                        </p>
                        <form id="bulkUploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="csv-file" class="form-label">Selecciona un archivo CSV:</label>
                                <input type="file" class="form-control" id="csv-file" name="csv-file" accept=".csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Subir CSV</button>
                        </form>
                        <div id="upload-result" class="mt-3"></div>
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
                        <p>¿Estás seguro de que deseas limpiar la tabla "club"? Esta acción eliminará todos los clubes y podría afectar a los participantes asociados.</p>
                        <p>Tiempo restante: <span id="countdown">6</span> segundos</p>
                        <button id="confirm-clear-btn" class="btn btn-danger">Confirmar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <table id="clubs-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Sufijo</th>
                    <th>Logo</th>
                    <th>Participantes</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas generadas dinámicamente por DataTables -->
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar club -->
    <div class="modal fade" id="addClubModal" tabindex="-1" aria-labelledby="addClubModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClubModalLabel">Agregar Club</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="clubForm">
                        <div class="mb-3">
                            <label for="club-name" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="club-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="club-suffix" class="form-label">Sufijo:</label>
                            <input type="text" class="form-control" id="club-suffix">
                        </div>
                        <div class="mb-3">
                            <label for="club-img" class="form-label">Nombre de la Imagen:</label>
                            <input type="text" class="form-control" id="club-img" placeholder="Escribe el nombre del archivo de la imagen (por ejemplo, logo.jpg)">
                        </div>
                        <button type="button" id="save-club-btn" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar club -->
    <div class="modal fade" id="editClubModal" tabindex="-1" aria-labelledby="editClubModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClubModalLabel">Editar Club</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="editClubForm">
                        <input type="hidden" id="edit-club-id">
                        <div class="mb-3">
                            <label for="edit-club-name" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="edit-club-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-club-suffix" class="form-label">Sufijo:</label>
                            <input type="text" class="form-control" id="edit-club-suffix">
                        </div>
                        <div class="mb-3">
                            <label for="edit-club-img" class="form-label">Nombre de la Imagen:</label>
                            <input type="text" class="form-control" id="edit-club-img" placeholder="Escribe el nombre del archivo de la imagen (por ejemplo, logo.jpg)">
                        </div>
                        <button type="button" id="update-club-btn" class="btn btn-primary">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#clubs-table').DataTable({
                ajax: {
                    url: 'get_clubs.php',
                    dataSrc: ''
                },
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'sufijo' },
                    {
                        data: 'img',
                        render: function(data) {
                            return data ? `<img src="${data}" alt="Imagen del club" class="img-thumbnail" style="width: 50px; height: 50px;">` : 'Sin imagen';
                        }
                    },
                    { data: 'participantes' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                            <button class="btn btn-warning btn-sm" onclick="editClub(${row.id}, '${row.nombre}', '${row.sufijo}', '${row.img}')">Editar</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Eliminar</button>
                            `;
                        }
                    }
                ]
            });

            // Agregar club
            $('#save-club-btn').click(function() {
                const clubData = {
                    nombre: $('#club-name').val(),
                    sufijo: $('#club-suffix').val(),
                    img: $('#club-img').val()
                };

                $.ajax({
                    url: 'add_club.php',
                    type: 'POST',
                    data: clubData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#addClubModal').modal('hide');
                            $('#clubForm')[0].reset();
                            table.ajax.reload();
                            alert('Club agregado exitosamente');
                        } else {
                            alert('Error al agregar el club: ' + (response.error || 'Respuesta inesperada del servidor'));
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error al agregar el club: ' + xhr.responseText);
                    }
                });
            });

            // Editar club
            window.editClub = function(id, nombre, sufijo, img) {
                $('#edit-club-id').val(id);
                $('#edit-club-name').val(nombre);
                $('#edit-club-suffix').val(sufijo);
                if (img && img !== 'Sin imagen') {
                    const imageName = img.split('/').pop();
                    $('#edit-club-img').val(imageName);
                } else {
                    $('#edit-club-img').val('');
                }
                $('#editClubModal').modal('show');
            };

            $('#update-club-btn').click(function() {
                const formData = new FormData();
                formData.append('id', $('#edit-club-id').val());
                formData.append('nombre', $('#edit-club-name').val());
                formData.append('sufijo', $('#edit-club-suffix').val());
                formData.append('img', $('#edit-club-img').val());

                $.ajax({
                    url: 'update_club.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editClubModal').modal('hide');
                            table.ajax.reload();
                            alert('Club actualizado exitosamente');
                        } else {
                            alert('Error al actualizar el club: ' + (response.error || 'Respuesta inesperada del servidor'));
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Hubo un error al actualizar el club: ' + xhr.responseText);
                    }
                });
            });

            // Eliminar club
            $('#clubs-table').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('¿Estás seguro de que deseas eliminar este club?')) {
                    $.post('delete_club.php', { id: id }, function(response) {
                        table.ajax.reload();
                    }).fail(function(xhr, status, error) {
                        alert('Error al eliminar el club: ' + xhr.responseText);
                    });
                }
            });

            // Manejar el formulario de carga masiva
            $('#bulkUploadForm').submit(function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'bulk_upload.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#upload-result').html('<div class="alert alert-success">Los datos se cargaron correctamente.</div>');
                            setTimeout(function() {
                                $('#bulkUploadModal').modal('hide');
                                location.reload();
                            }, 2000);
                        } else {
                            $('#upload-result').html('<div class="alert alert-danger">Error: ' + (response.error || 'Respuesta inesperada del servidor') + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#upload-result').html('<div class="alert alert-danger">Hubo un error al procesar el archivo: ' + xhr.responseText + '</div>');
                    }
                });
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
                    url: 'clear_table.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#clearTableModal').modal('hide');
                            alert('La tabla "club" ha sido limpiada correctamente.');
                            table.ajax.reload();
                        } else {
                            alert('Error al limpiar la tabla: ' + (response.error || 'Respuesta inesperada del servidor'));
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Hubo un error al intentar limpiar la tabla: ' + xhr.responseText);
                    }
                });
            });

            // Manejar el botón de generar Excel (genera CSV)
            $('#generate-excel-btn').click(function() {
                window.location.href = 'generate_excel.php';
            });
        });
    </script>
</body>
</html>