<?php
// $servername = 'localhost';
// $dbname = 'eddfigpi_db';
// $username = 'root';
// $password = '';
// db_info.php del servidor ccom
$servername = '136.145.29.193';
$username = 'eddfigpi';
$password = 'edd84023';
$dbname = 'eddfigpi_db';
// //verificar conexión
$dbc = new mysqli($servername, $username, $password, $dbname);
if ($dbc->connect_error) {
    die("<p>La conexión al servidor falló. Error: ".
    $dbc->connect_error)."</p>";
}
$dbc->query("SET NAMES 'utf8'");
?>