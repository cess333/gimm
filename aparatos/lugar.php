<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Around - Gimnasia</title>
    <script src="https://cdn.jsdelivr.net/npm/socket.io-client@4.4.1/dist/socket.io.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #222;
            color: white;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid white;
        }
        th {
            background-color: #444;
        }
        .highlight {
            background-color: #4CAF50;
            transition: background-color 1s ease;
        }
    </style>
</head>
<body>
    <h1>Clasificación All Around</h1>
    <label for="categoria">Filtrar por Categoría:</label>
    <select id="categoria" onchange="actualizarTabla()">
        <option value="">Todas</option>
    </select>
    <table>
        <thead>
            <tr>
                <th>Puesto</th>
                <th>Nombre</th>
                <th>Puntaje Total</th>
            </tr>
        </thead>
        <tbody id="tabla-body">
        </tbody>
    </table>
     <script>
    function actualizarTabla() {
        const categoria = document.getElementById('categoria').value;
        $.ajax({
            url: 'obtener_clasificacion.php',
            type: 'GET',
            data: { categoria },
            dataType: 'json',
            success: function (data) {
                const tbody = document.getElementById('tabla-body');
                tbody.innerHTML = '';

                data.forEach((row, index) => {
                    let tr = document.createElement('tr');
                    tr.innerHTML = `<td>${index + 1}</td><td>${row.nombre}</td><td>${row.puntaje}</td>`;
                    tr.classList.add('animate__animated', 'animate__fadeIn');
                    tbody.appendChild(tr);
                });
            }
        });
    }

    // Cargar datos cada 5 segundos
    setInterval(actualizarTabla, 5000);
</script>


</body>
</html>
