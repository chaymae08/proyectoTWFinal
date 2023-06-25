<?php

session_start();

include_once './formu_html.php';
include_once '../modelo/db.php';
require_once '../vista/codigo_html.php';



//Funciones de HTML
inicio();
menu();

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
      //$datos['estados'] = isset($_POST['estados']) ? $_POST['estados'] : '';
      $datos['descripcion'] = isset($_POST['incidencia_descripcion']) ? $_POST['incidencia_descripcion'] : '';
      $datos['lugar'] = isset($_POST['incidencia_lugar']) ? $_POST['incidencia_lugar'] : '';
      $datos['fecha'] = isset($_POST['incidencia_fecha']) ? $_POST['incidencia_fecha'] : '';
      $datos['palabraC'] = isset($_POST['incidencia_palabraC']) ? $_POST['incidencia_palabraC'] : '';





        //Para las imagenes 
      $datos['imagen'] = isset($_FILES['incidencia_imagen']['tmp_name']) ? addslashes(file_get_contents($_FILES['incidencia_imagen']['tmp_name'])) : '';

    if(isset($_FILES['fotos_descripcion']) && $_FILES['fotos_descripcion']['error'][0]==0){ //Si se ha subido al menos una fotos de la descruipcion, las guardamos en datos
        foreach($_FILES["fotos_descripcion"]['tmp_name'] as $key => $tmp_name)
            $datos['fotos_descripcion'][] = addslashes(file_get_contents($_FILES['fotos_descripcion']['tmp_name'][$key]));
    }
     else {
        $datos['fotos_descripcion']='';
    }
      $_SESSION['imagen'] = $datos['imagen'];
      $_SESSION['fotos_descripcion'] = $datos['fotos_descripcion']; 

      //SI hay campos vacios
      if ($datos['nombre']=='' || $datos['palabraC']=='' || $datos['descripcion']=='' || $datos['lugar']=='' || $datos['fecha']=='') {
          $info[]='No puede dejar campos vacíos';
      }

      if (!isset($info)) {
          $accion='Añadir';
      }
  }

  //Si hemos confirmado la insercion de la incidencia, obtenemos los parametros para meterlos en la BD
  if (isset($_POST['accion']) && $_POST['accion']=='Confirmar insercion') {
      $datos['nombre'] = isset($_POST['incidencia_titulo']) ? $_POST['incidencia_titulo'] : '';
      //$datos['estados'] = isset($_POST['estados']) ? $_POST['estados'] : '';
      $datos['descripcion'] = isset($_POST['incidencia_descripcion']) ? $_POST['incidencia_descripcion'] : '';
      $datos['lugar'] = isset($_POST['incidencia_lugar']) ? $_POST['incidencia_lugar'] : '';
      $datos['fecha'] = isset($_POST['incidencia_fecha']) ? $_POST['incidencia_fecha'] : '';
      $datos['palabraC'] = isset($_POST['incidencia_palabraC']) ? $_POST['incidencia_palabraC'] : '';




   
      $datos['imagen'] = $_SESSION['imagen']; //Recuperamos la imagen
      $datos['fotos_descripcion']=$_SESSION['fotos_descripcion']; //Recuperamos las imagenes de la descripcion
      $accion='Confirmar';
  }

  if (isset($_POST['accion']) && $_POST['accion']=='Cancelar') {
      $accion='Cancelar';
  }


  if (!is_string($db=DB_conexion())) {

        $estados = DB_getListadoEstados($db);

      //Si pulsamos Añadir incidencia, mostrar los datos sin poder editarlos
      if ($accion=='Añadir') {
         $datos['editable']=false;

          FORM_anadirIncidencia('Confirme los datos e inserte la foto:', $datos, 'Confirmar insercion',$estados);

      //Si ya hemos confirmado los datos, los metemos en la BD
      } elseif ($accion=='Confirmar') {
          $res = DB_anadirIncidencia($db, $datos);

          if ($res===true) {
              echo "<p>Se ha añadido con éxito la incidencia:<strong> ".htmlentities($datos['nombre'])."</strong>.</p> ";
              echo "<p>Será redirecionado al listado de incidencias en 5 segundos. </p>";
              DB_log($db,$_SESSION['nombre_usuario'],"Ha añadido la incidencia ".$datos['nombre']."");
              header('refresh:5; url=listado.php');
          } else {
              $info[] = $res;
              $info[] = "Cambie el titulo o inserte otra incidencia. Redireccionando...";
              DB_log($db,$_SESSION['nombre_usuario'],"Error al añadir la incidencia ".$datos['nombre']."");
              header('refresh:5; url=anadir_Incidencia.php');
          }

      //Si pulsamos boton de cancelar, volver a la pagina
      } elseif ($accion=='Cancelar') {
          header('url=anadir_Incidencia.php');
          $accion='';
      }

      //Si no hay accion, mostrar el formulario
      if($accion=='') {
        FORM_anadirIncidencia('Indique los datos:', $datos, 'Añadir incidencia',$estados);
      }

      if (isset($info)) {
          msgError($info);
      }

      DB_desconexion($db);
  } else {
      msgError($db);
  }

    echo "</div>
      </div>";


barraHTML();
#footerHTML();
