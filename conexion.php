<?php
// Configuración de la base de datos
$host = 'localhost'; // Cambia esto si tu base de datos está en otro servidor
$user = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña de la base de datos
$database = 'gimnasia'; // Nombre de tu base de datos

//$host = 'mysql.webcindario.com'; // Cambia esto si tu base de datos está en otro servidor
//$user = 'cali'; // Usuario de la base de datos
//$password = 'nrmWuBb7w'; // Contraseña de la base de datos
//$database = 'cali'; // Nombre de tu base de datos

// Conectar a la base de datos
$conn = new mysqli($host, $user, $password, $database);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
