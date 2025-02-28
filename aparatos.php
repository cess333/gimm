<?php 
include('navbar.php'); 
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gimnasia - Aparatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <style>
        .aparato-card {
            background-color: #333;
            color: #ffd700;
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            font-weight: bold;
            transition: transform 0.3s;
        }
        .aparato-card:hover {
            transform: scale(1.05);
        }
        .aparato-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .aparato-card a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body class="bg-dark text-light">

<div class="container text-center mt-3">
    <!-- Input Select -->
    <div class="mb-4">
        <label for="panelSelect" class="form-label">Selecciona un panel:</label>
        <select id="panelSelect" class="form-select w-auto mx-auto">
            <option value="panel1" selected>Panel 1</option>
            <option value="panel2">Panel 2</option>
        </select>
    </div>
    
    <!-- Contenedor de aparatos -->
    <div id="aparatosContainer" class="row row-cols-2 row-cols-md-4 g-4">
        <!-- Aquí se cargará dinámicamente el contenido -->
    </div>
</div>

<script>
    // Configuración inicial de los enlaces para los paneles
    const panels = {
        panel1: [
            { name: "SALTO", link: "aparatos/calificacion_panel1.php?aparato=salto", img: "img/salto.jpg" },
            { name: "BARRAS", link: "aparatos/calificacion_panel1.php?aparato=barras", img: "img/barras.jpg" },
            { name: "VIGA", link: "aparatos/calificacion_panel1.php?aparato=viga", img: "img/viga.jpg" },
            { name: "PISO", link: "aparatos/calificacion_panel1.php?aparato=piso", img: "img/piso.jpg" },
            //{ name: "TUMBLING", link: "tumbling.html", img: "img/mante.jpg" },
            //{ name: "ARZONES", link: "arzones.html", img: "img/mante.jpg" },
            //{ name: "ANILLOS", link: "anillos.html", img: "img/mante.jpg" },
            //{ name: "BARRAS PARALELAS", link: "barras-paralelas.html", img: "img/mante.jpg" },
            //{ name: "BARRA FIJA", link: "barra-fija.html", img: "img/mante.jpg" },
            //{ name: "CIRCUITOS", link: "circuitos.html", img: "img/mante.jpg" }
        ],
        panel2: [
            { name: "SALTO", link: "aparatos/calificacion_panel2.php?aparato=salto", img: "img/salto.jpg" },
            { name: "BARRAS", link: "aparatos/calificacion_panel2.php?aparato=barras", img: "img/barras.jpg" },
            { name: "VIGA", link: "aparatos/calificacion_panel2.php?aparato=viga", img: "img/viga.jpg" },
            { name: "PISO", link: "aparatos/calificacion_panel2.php?aparato=piso", img: "img/piso.jpg" },
            //{ name: "TUMBLING", link: "tumbling_panel2.html", img: "img/mante.jpg" },
            //{ name: "ARZONES", link: "arzones_panel2.html", img: "img/mante.jpg" },
            //{ name: "ANILLOS", link: "anillos_panel2.html", img: "img/mante.jpg" },
            //{ name: "BARRAS PARALELAS", link: "barras-paralelas_panel2.html", img: "img/mante.jpg" },
            //{ name: "BARRA FIJA", link: "barra-fija_panel2.html", img: "img/mante.jpg" },
            //{ name: "CIRCUITOS", link: "circuitos_panel2.html", img: "img/mante.jpg" }
        ]
    };

    const aparatosContainer = document.getElementById('aparatosContainer');
    const panelSelect = document.getElementById('panelSelect');

    // Función para renderizar los aparatos según el panel seleccionado
    function renderAparatos(panel) {
        aparatosContainer.innerHTML = ''; // Limpiar contenido previo
        panels[panel].forEach(aparato => {
            const col = document.createElement('div');
            col.classList.add('col');

            col.innerHTML = `
                <div class="aparato-card">
                    <a href="${aparato.link}" onclick="return confirmPanel('${panel}', '${aparato.name}')">
                        <img src="${aparato.img}" alt="${aparato.name}">
                        <div class="p-3">${aparato.name}</div>
                    </a>
                </div>
            `;
            aparatosContainer.appendChild(col);
        });
    }

    // Función para confirmar el panel antes de proceder
    function confirmPanel(panel, aparato) {
        return confirm(`¿Estás seguro que quieres acceder al aparato ${aparato} en ${panel.toUpperCase()}?`);
    }

    // Evento para cambiar contenido al seleccionar un panel
    panelSelect.addEventListener('change', (e) => {
        renderAparatos(e.target.value);
    });

    // Renderizado inicial del panel 1
    renderAparatos('panel1');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
