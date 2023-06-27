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

$db = DB_conexion();
if (! empty($_FILES)) {
  //Validar formato sql
    if (! in_array(strtolower(pathinfo($_FILES["backup_file"]["name"], PATHINFO_EXTENSION)), array(
        "sql"
    ))) {
        $salida ="Formato incorrecto";
    } else {
        if (is_uploaded_file($_FILES["backup_file"]["tmp_name"])) {
            move_uploaded_file($_FILES["backup_file"]["tmp_name"], $_FILES["backup_file"]["name"]);
            $salida = DB_Restaurar_base( $db,$_FILES["backup_file"]["name"]);
        }
    }
}


echo <<< HTML


<main>
<div class="cajaPrincipal">
  <h1>Gestión de la Base de Datos</h1>
  <p>Restaurar la base de datos.</p>




    <form method="post" action="" enctype="multipart/form-data">
          <div>Elegir archivo</div>
          <input type="file" name="backup_file" class="arch">
        <div>
          <input type="submit" name="restore" value="Restaurar" class="btn-rest">
        </div>
      </div>
    </form>
HTML;

DB_log($db,$_SESSION['nombre_usuario'],"Restaura la base de datos");

echo "</div></div>";
barraHTML();
footerHTML();

?>
