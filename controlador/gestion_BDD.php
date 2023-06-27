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

echo <<<HTML
<main>
<div class="cajaPrincipal">
  <h1>Gestión de la Base de Datos</h1>
  <p>Aquí gestionar la base de datos tal y como está en este momento.
 </p>
HTML;

echo <<< HTML

  <form action='descargaBD.php' method='POST' enctype='multipart/form-data'>
  <input type="submit" value="Descargar copia de seguridad" class="BUTTON_BK">
  </form>

  <form method="post" action="./restaurarBD.php" enctype="multipart/form-data">
  <input type="submit" value="Restaurar" class="BUTTON_BK" id="btn_restaurar">
  </form>

  <p>Pulsar el botón para eliminar la base de datos completamente .</p>

  <form action='./borrarBD.php' method='POST' enctype='multipart/form-data'>
  <input type="submit" value="Borrar base de datos" class="BUTTON_BK" id="btn_borrar">
  </form>
HTML;
echo "</div></div>";
barraHTML();
//footerHTML();
?>
