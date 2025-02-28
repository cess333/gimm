<!-- Navbar -->
<?php
// Obtener solo el nombre del archivo actual (sin la ruta)
$current_page = basename($_SERVER['REQUEST_URI']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Gimnasia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'aparatos.php' ? 'active' : ''); ?>" href="aparatos.php">Aparatos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_page, 'categorias') !== false ? 'active' : ''); ?>" href="categorias/">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'clubes') !== false ? 'active' : ''); ?>" href="clubes/index.php">Clubes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'tabla_participantes.php' ? 'active' : ''); ?>" href="participantes/tabla_participantes.php">Participantes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_page, 'minmaxnivel') !== false ? 'active' : ''); ?>" href="minmaxnivel">Rango PN, N1 Y N2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_page, 'calificaciones') !== false ? 'active' : ''); ?>" href="calificaciones/">Monitoreo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'resultados.php' ? 'active' : ''); ?>" href="resultados/resultados.php">Resultados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'porequipos.php' ? 'active' : ''); ?>" href="resultados/porequipos.php">Resultados por equipos</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'config.php' ? 'active' : ''); ?>" href="aparatos/config.php">Pantallas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    /* Efecto de hover para los enlaces */
    .navbar-nav .nav-link:hover {
        background-color: #005BBC;
        color: white;
        border-radius: 5px;
    }

    /* Pestaña activa */
    .navbar-nav .nav-link.active {
        background-color: #005BBC;
        color: white;
    }

    .navbar {
        margin-top: 0;
        margin-bottom: 0;
        border-top: none;
        border-bottom: none;
    }
</style>

<?php include 'include/bootandtables.php'; ?>