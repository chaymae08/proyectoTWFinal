<?php
session_start();



include_once './formu_html.php';
include_once '../modelo/db.php';
require_once '../vista/codigo_html.php';


/*
if (!isset($_SESSION['usuario'])) {
    header("LOCATION: /~aulas/proyecto_final/index.php");
}*/

//Funciones de HTML
inicio();
menu();

echo <<<HTML
<main>

<div class="cajaPrincipal">
  <h1>Incidencias de
HTML;
  echo " "; echo $_SESSION['nombre_usuario'];
echo<<<HTML
</h1>
HTML;


$accion='';
$imagen='';
$valorada=false;

if (isset($_POST['accion'])) {

    switch ($_POST['accion']) {

        case 'Borrar': // Presentar formulario y pedir confirmación
          $accion = 'Borrar';
          $id_incidencias = $_POST['id_incidencias'];
        break;

        case 'Editar': // Presentar formulario y pedir confirmación
          $accion = 'Editar';
          $id_incidencias = $_POST['id_incidencias'];
        break;

        case 'Confirmar Borrado': // Borrado confirmado
          $accion = 'BorrarOk';
          $id_incidencias = $_POST['id_incidencias'];
        break;

        case 'Modificar Datos': // Modificación realizada, comprobando que no haya campos vacios
          $id_incidencias = $_POST['id_incidencias'];
          if ($_FILES['incidencia_img']['error']==0) { //Si se ha subido imagen,la guardamos
              $imagen=addslashes(file_get_contents($_FILES['incidencia_img']['tmp_name']));
          }
          $_SESSION['imagen'] = $imagen; //Guardamos la imagen para despues recuperarla

          if ($_POST['incidencia_titulo']=='' || $_POST['incidencia_autor']=='' || $_POST['incidencia_estado']==''|| $_POST['incidencia_descripcion']=='' || $_POST['incidencia_lugar']=='' || $_POST['incidencia_fecha']=='') {
              $info_vacios="No puede modificar la incidencia con campos vacios.";
              echo "<p class='msg_error'>$info_vacios</p>";
              $accion='Editar';
          }

          if (!isset($info_vacios)) {
              $accion='Modificar';
          }
        break;

        case 'Confirmar modificacion': //Si se ha confirmado la modificacion, recuperamos la imagen
          $id_incidencias = $_POST['id_incidencias'];
          $imagen=  $_SESSION['imagen'];
          $accion = 'ModificarOk';
        break;

        case 'Ver': // Visualizaremos una incidencia
          $accion = 'Ver';
          $id_incidencias = $_POST['id_incidencias'];

        break;

        case 'Enviar comentario': // Enviamos comentario
            $accion = 'Comentario_enviado';
            $id_incidencias = $_POST['id_incidencias'];
            if(!isset($_POST['autor_comentario']))
              $_POST['autor_comentario']=NULL;

        break;

        case 'Borrar comentario': // Borramos comentario
          $accion = 'Comentario_borrado';
          $id_incidencias = $_POST['id_incidencias'];
        break;

        case 'Cancelar':
          $accion='Cancelar';
        break;
    }
}


if (!is_string($db=DB_conexion())) {

        $incidencias_totales=DB_getListadoMisincidencias($db);

    if ($accion=='') { //Si todavia no se ha especificado accion todas las incidencias y la búsqueda
        Vista_listadoIncidencias($incidencias_totales);
    }

    $cats = DB_getListadoEstados($db);

    switch ($accion) {


      case 'Ver': //Al pulsar el boton de ver, procederemos a visualizar la incidencia de forma no editable
        $incidencia = DB_getIncidencias($db, $id_incidencias);
          
        $incidencia['editable']=false;
        $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']); //Obtenemos el autor de la incidencia
        $comentarios=DB_getComentarios($db,$id_incidencias); //Obtenemos los comentarios de la incidencia
        $estados = DB_getEstadosIncidencia($db,$id_incidencias); //Obtenemos el listado de estados 
        $incidencia['estado']=$estados;

        if(isset($_SESSION['id_usuario'])) //Si el usuario esta registrado, comprobamos si ya ha valorado la 
          $valorada=DB_getValoradaUsuario($db, $id_incidencias, $_SESSION['id_usuario']);
          
       
         Vista_verIncidencia ($db,$incidencia,$comentarios,$valorada);
      break;

      case 'Comentario_enviado': //Si escribimos la incidencia, volvemos a ver la incidencia
        if(DB_añadir_comentario($db, ['id_incidencias'=>$id_incidencias,'autor_comentario'=>$_POST['autor_comentario'],'fecha_comentario'=>$_POST['fecha_comentario'],'comentario'=>$_POST['comentario']])){
            echo "<p class='error'>Comentario insertado  </p>";
            
            if(!isset($_SESSION['nombre_usuario']))
              $autor_c = 'ANÓNIMO';  
            else {
              $autor_c = $_SESSION['nombre_usuario'];
            }
            DB_log($db,$autor_c,"Ha añadido un comentario");
        }else {
            echo "<p class='error'>No se ha podido insertar el comentario  </p>";
            DB_log($db,$_POST['autor_comentario'],"Error al insertar comentario");
          }
          $incidencia = DB_getIncidencias($db, $id_incidencias);
          $incidencia['editable']=false;
          $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
          $comentarios=DB_getComentarios($db,$id_incidencias);
          $estados = DB_getEstadosIncidencia($db,$id_incidencias);
          $incidencia['estado']=$estados;
          if(isset($_SESSION['id_usuario']))
            $valorada=DB_getValoradaUsuario($db, $id_incidencias, $_SESSION['id_usuario']);
                       
        
        // $valorada=NULL;

          Vista_verIncidencia($db,$incidencia,$comentarios,$valorada);
  
      
      break;
        /*
      case 'Comentario_borrado': //Si borramos comentario, volvemos a ver la incidencia
        if(DB_borrarComentario($db, $_POST['id_comentario'])){
          echo "<p class='error'>Comentario borrado  </p>";
          DB_log($db,$_SESSION['nombre_usuario'],"Ha eleminido un comentario");
        }
        else {
          echo "<p class='error'>No se ha podido borrar el comentario  </p>";
          DB_log($db,$_SESSION['nombre_usuario'],"Error al intentar borrar un comentario");
        }
         $incidencia = DB_getIncidencia($db, $id_incidencias);
        $incidencia['editable']=false;
        $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
        $comentarios=DB_getComentarios($db,$id_incidencias);
        $estados= DB_getEstadosIncidencia($db,$id_incidencias);
        $incidencia['estados']=$estados;
        $estados = DB_getEstadosIncidencia($db,$id_incidencias);
        $incidencia['estados']=$estados;
        $valorada=DB_getValoradaUsuario($db, $id_incidencias, $_SESSION['id_usuario']);
        Vista_listadoIncidencias($db,$incidencia,$comentarios,$valorada);
        
      break;


      case 'Borrar': //Al pulsar el boton de borrar, procederemos a visualizar la incidencia de forma no editable
        $incidencia = DB_getIncidencia($db, $id_incidencias);
        $incidencia['editable']=false;
        $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
       // FORM_editarincidencia('Confirme borrado de esta incidencia:', $incidencia, 'Confirmar Borrado',$cats);
      break;

      case 'BorrarOk': //Si confirmamos el borrado, procedemos a borrar la incidencia de la BD y redireccionar a la pagina de listado
        if (DB_borrarIncidencia($db, $id_incidencias)) {
            $info[] = 'La incidencia '.$_POST['incidencia_titulo'].' ha sido borrada';
            DB_log($db,$_SESSION['nombre_usuario'],'Ha eliminado la incidencia '.$_POST['incidencia_titulo'].' ');
        } else {
            $info[] = 'No se ha podido borrar '.$_POST['incidencia_titulo'];
            DB_log($db,$_SESSION['nombre_usuario'],'Error al eliminar la incidencia'.$_POST['incidencia_titulo'].'');
        }
        header('refresh: 5; url=pag_listado.php');
        echo "Redireccionado en 5 seg..";
      break;

        case 'Editar': //Veremos el cuestionario de edicion
          $incidencia = DB_getIncidencia($db, $id_incidencias);
          $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
          FORM_editarincidencia('Edite los datos:', $incidencia, 'Modificar Datos',$cats);
        break;

        case 'Modificar': //Si realizamos la edicion, mostraremos el formulario de forma no editable
          FORM_editarIncidencia('Confirme la modificacion:', ['id'=>$id_incidencias,
          'nombre'=>$_POST['incidencia_titulo'],
          'categoria'=>$_POST['incidencia_categoria'],
          'descripcion'=>$_POST['incidencia_descripcion'],
          'ingredientes'=>$_POST['incidencia_ingredientes'],
          'preparacion'=>$_POST['incidencia_preparacion'],
          'imagen'=>$imagen,
          'editable'=>false], 'Confirmar modificacion',$cats);
        break;

        case 'ModificarOk': //Si confirmarmos la edicion, procedemos a actualizar la incidencia de la BD
          $msg = DB_actincidencia($db, ['id'=>$id_incidencias,
          'nombre'=>$_POST['incidencia_titulo'],
          'categoria'=>$_POST['incidencia_categoria'],
          'descripcion'=>$_POST['incidencia_descripcion'],
          'ingredientes'=>$_POST['incidencia_ingredientes'],
          'preparacion'=>$_POST['incidencia_preparacion'],
          'imagen'=>$imagen]);
          if ($msg===true) {
              $info[] = 'La incidencia '.$_POST['incidencia_titulo'].' ha sido actualizada';
              DB_log($db,$_SESSION['nombre_usuario'],'Ha actualizado la incidencia '.$_POST['incidencia_titulo'].' ');
          } else {
              $info[] = 'No se ha podido actualizar '.$_POST['incidencia_titulo'];
              $info[] = $msg;
              DB_log($db,$_SESSION['nombre_usuario'],'Error al actualizar la incidencia '.$_POST['incidencia_titulo'].' ');
          }
          header('refresh: 5; url=pag_listado.php');
          echo "Redireccionado en 5 seg..";
        break;
          footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 10px;
    }
    
    footer div {
      margin: 0 10px;
    }
    
    footer .barra {
      margin: 0;
    }
        */

        case 'Cancelar':
          header('Location: mis_Incidencias.php');
        break;
  }

    if (isset($info) && msgCount($info)>0) {
        msgError($info);
    }
    DB_desconexion($db);
} else {
    msgError($db);
}

echo "</div></div>";