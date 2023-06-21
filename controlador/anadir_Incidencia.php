<?php

session_start();

include_once './funciones/formu_html.php';
include_once '../modelo/db.php';
require_once '../vista/codigo_html.php';


//Si no esta logueado, no permitir acceso desde la url
if (!isset($_SESSION['usuario'])) {
    header("LOCATION: /~aulas/proyecto_final/index.php");
}

//Funciones de HTML
inicioHTML();
navHTML();

echo <<<HTML
<main>

<div class="cajaPrincipal">
  <h1>Añadir incidencia</h1>
HTML;


  $datos=false;
  $accion='';

  //Si se rellenado el formulario para añadir incidencia, obtener parametros
  if (isset($_POST['accion']) && $_POST['accion']=='Añadir incidencia') {
      $datos['nombre'] = isset($_POST['incidencia_titulo']) ? $_POST['incidencia_titulo'] : '';
      $datos['estados'] = isset($_POST['estados']) ? $_POST['estados'] : '';
      $datos['descripcion'] = isset($_POST['incidencia_descripcion']) ? $_POST['incidencia_descripcion'] : '';
  
      $datos['imagen'] = isset($_FILES['incidencia_imagen']['tmp_name']) ? addslashes(file_get_contents($_FILES['incidencia_imagen']['tmp_name'])) : '';

      if($_FILES['fotos_procedimiento']['error'][0]==0){ //Si se ha subido al menos una foto del procedimiento, las guardamos en datos
        foreach($_FILES["fotos_procedimiento"]['tmp_name'] as $key => $tmp_name)
          $datos['fotos_procedimiento'][] = addslashes(file_get_contents($_FILES['fotos_procedimiento']['tmp_name'][$key]));
      }
      else {
          $datos['fotos_procedimiento']='';
      }

      $_SESSION['imagen'] = $datos['imagen']; //Guardar la imagen en una variable de sesion para recuperarla despues
      $_SESSION['fotos_descripcion'] = $datos['fotos_descripcion']; //Guardamos todas las fotos del procedimiento para recuperarlas

      //SI hay campos vacios
      if ($datos['nombre']=='' || $datos['categorias']=='' || $datos['descripcion']=='' || $datos['ingredientes']=='' || $datos['preparacion']=='') {
          $info[]='No puede dejar campos vacíos';
      }

      if (!isset($info)) {
          $accion='Añadir';
      }
  }

  //Si hemos confirmado la insercion de la incidencia, obtenemos los parametros para meterlos en la BD
  if (isset($_POST['accion']) && $_POST['accion']=='Confirmar insercion') {
      $datos['nombre'] = isset($_POST['incidencia_titulo']) ? $_POST['incidencia_titulo'] : '';
      $datos['estados'] = isset($_POST['estados']) ? $_POST['estados'] : '';
      $datos['descripcion'] = isset($_POST['incidencia_descripcion']) ? $_POST['incidencia_descripcion'] : '';
   
      $datos['imagen'] = $_SESSION['imagen']; //Recuperamos la imagen
      $datos['fotos_procedimiento']=$_SESSION['fotos_procedimiento']; //Recuperamos las imagenes del procedimiento
      $accion='Confirmar';
  }

  if (isset($_POST['accion']) && $_POST['accion']=='Cancelar') {
      $accion='Cancelar';
  }


  if (!is_string($db=DB_conexion())) {

        $cats = DB_getListadoCategorias($db);

      //Si pulsamos Añadir incidencia, mostrar los datos sin poder editarlos
      if ($accion=='Añadir') {
          $datos['editable']=false;

          FORM_añadirincidencia('Confirme los datos e inserte la foto:', $datos, 'Confirmar insercion',$cats);

      //Si ya hemos confirmado los datos, los metemos en la BD
      } elseif ($accion=='Confirmar') {
          $res = DB_addincidencia($db, $datos);

          if ($res===true) {
              echo "<p>Se ha añadido con éxito la incidencia:<strong> ".htmlentities($datos['nombre'])."</strong>.</p> ";
              echo "<p>Será redirecionado al listado de incidencias en 3 segundos. </p>";
              DB_log($db,$_SESSION['nombre_usuario'],"Ha añadido la incidencia ".$datos['nombre']."");
              header('refresh:5; url=pag_listado.php');
          } else {
              $info[] = $res;
              $info[] = "Cambie el titulo o inserte otra incidencia. Redireccionando...";
              DB_log($db,$_SESSION['nombre_usuario'],"Error al añadir la incidencia ".$datos['nombre']."");
              header('refresh:5; url=pag_anadirincidencia.php');
          }

      //Si pulsamos boton de cancelar, volver a la pagina
      } elseif ($accion=='Cancelar') {
          header('url=pag_anadirincidencia.php');
          $accion='';
      }

      //Si no hay accion, mostrar el formulario
      if($accion=='') {
        FORM_añadirincidencia('Indique los datos:', $datos, 'Añadir incidencia',$cats);
      }

      if (isset($info) && msgCount($info)>0) {
          msgError($info);
      }

      DB_desconexion($db);
  } else {
      msgError($db);
  }

    echo "</div>
      </div>";


asideHTML();
footerHTML();
