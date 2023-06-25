


<?php
session_start();

include_once './formu_html.php';
include_once '../modelo/db.php';
require_once '../vista/codigo_html.php';
include_once './funcion_log.php';

if (!isset($_SESSION['admin'])) {
    header("LOCATION: /~aulas/proyecto_final/index.php");
}
echo <<<HTML
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
HTML;
inicio();
menu();

echo <<<HTML
<main>
<div class="cajaPrincipal">
  <h1>Log del sistema</h1>
HTML;

echo "<div class='log'>";

echo '<div id="results">';

logg();

echo '</div>';
echo "</div>";
echo "</div></div>";


barraHTML();

//footerHTML();

 ?>
 
 <style>



h1 {
    text-align: center;
    color: #333;
}

.log {
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table td {
    border: 1px solid #ddd;
    padding: 8px;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #ddd;
}

table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}

</style>
