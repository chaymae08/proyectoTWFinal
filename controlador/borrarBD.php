<?php


session_start();


include_once './formu_html.php';
include_once '../modelo/db.php';
require_once '../vista/codigo_html.php';



if (!isset($_SESSION['admin'])) {
    header("LOCATION: /~aulas/proyecto_final/index.php");
}
inicio();
menu();
$db=DB_conexion();

// Obtener listado de tablas de la BD
$tablas = array();
$result = mysqli_query($db,'SHOW TABLES');
while ($row = mysqli_fetch_row($result))
    $tablas[] = $row[0];

//Borramos las tablas
$q = 'SET FOREIGN_KEY_CHECKS=0;';
mysqli_query($db,$q);
foreach ($tablas as $t) {
    $q = ' ';
    $q .= 'TRUNCATE TABLE '.$t.';';
    mysqli_query($db,$q);
}
$q = 'SET FOREIGN_KEY_CHECKS=1;';
mysqli_query($db,$q);

DB_log($db,$_SESSION['nombre_usuario'],"Borra la base de datos");
logout();

barraHTML();
footerHTML();

?>