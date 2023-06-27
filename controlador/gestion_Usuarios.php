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
  <h1>Gestión de usuarios</h1>
HTML;


$accion='';
$imagen='';


if (isset($_POST['accion'])) {

    switch ($_POST['accion']) {


        case 'Borrar': // Presentar formulario y pedir confirmación
          $accion = 'Borrar';
          $id = $_POST['id'];
        break;

        case 'Confirmar Borrado': // Borrado confirmado
          $accion = 'BorrarOk';
          $id = $_POST['id'];
        break;

        case 'Editar': // Presentar formulario y pedir confirmación
          $accion = 'Editar';
          $id = $_POST['id'];

        break;

        case 'Modificar Datos': // Modificación realizada, comprobando que no haya campos vacios

            if ($_FILES['usuario_img']['error']==0) { //Si se ha subido imagen,la guardamos
                $imagen=addslashes(file_get_contents($_FILES['usuario_img']['tmp_name']));
            }
            $_SESSION['imagen_modificarUsuario'] = $imagen; //Guardamos la imagen para despues recuperarla
            $_SESSION['clave_modificar']=$_POST['user_clave'];

            if (empty($_POST['user_nombre']) || empty($_POST['user_apellidos']) || empty($_POST['user_email'])  || empty($_POST['user_clave']) 
            || (!preg_match('/(\+34|0034|34)?[ -]*(6|7)[ -]*([0-9][ -]*){8}/', $_POST['user_telefono']) && !empty($_POST['user_telefono'])) ) {
             $info_vacios = 'Algún campo no es válido o está vacío';
            echo "<p class='msg_error'>$info_vacios</p>";
        }
        


            if (!isset($info_vacios)) {
                $accion='Modificar';
            }
            else {
              $accion='Editar';
              $id = $_POST['id'];
            }
          break;

          case 'Confirmar modificacion': //Si se ha confirmado la modificacion, recuperamos la imagen
            $imagen=  $_SESSION['imagen_modificarUsuario'];
            $clave_modificada=$_SESSION['clave_modificar'];
            $id = $_POST['id'];
            $accion = 'ModificarOk';
          break;

          case 'Añadir Usuario':
            $accion = 'Añadir Usuario';
            
            break;

        case 'Confirmar registro':
            $accion = 'Confirmar registro';
        //SI no hay foto, asignaremos una imagen por defecto
        $imagen="../vista/fotos/foto_defecto.jpeg";
        $tamimagen=filesize($imagen);
        $fp=fopen($imagen, 'rb'); //abrimos el archivo binario "imagen" en modo lectura
        $contenido=fread($fp, $tamimagen);//lee el archivo hasta el tamaño de la imagen
        $contenido=addslashes($contenido);//Añadimos caracteres de escape
        fclose($fp); //cerramos el archivo


        $datos['fotografia'] = (isset($_FILES['foto_usuario_registro']['tmp_name']) && $_FILES['foto_usuario_registro']['tmp_name']!='') ? addslashes(file_get_contents($_FILES['foto_usuario_registro']['tmp_name'])) : $contenido;
            break;

          


        case 'Cancelar':
          $accion='Cancelar';
        break;
    }
}


if (!is_string($db=DB_conexion())) {
    switch ($accion) {

      case 'Borrar': //Al pulsar el boton de borrar, procederemos a visualizar la incidencia de forma no editable
        $usuario= DB_obtenerDatosUsuario($db, $id);
        $usuario['editable']=false;
        editarUsuario('Confirme borrado de este usuario', $usuario, 'Confirmar Borrado', 'Administrador');
      break;

      case 'BorrarOk': //Si confirmamos el borrado, procedemos a borrar la incidencia de la BD y redireccionar a la pagina de listado
        if (DB_borrarUsuario($db, $id)) {
            $info[] = 'El usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'].' ha sido borrado';
            DB_log($db,$_SESSION['nombre_usuario'],'El usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'].' ha sido borrado');
        } else {
            $info[] = 'No se ha podido borrar el usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'].'';
            DB_log($db,$_SESSION['nombre_usuario'],'Error al eliminar a '.$_POST['user_nombre'].' '.$_POST['usuario_apellidos'].'');
        }
        header('refresh: 5; url=gestion_Usuarios.php');
        echo "Redireccionado en 5 seg..";
      break;

        case 'Editar': //Al pulsar el boton de borrar, procederemos a visualizar la incidencia de forma editable
        $usuario= DB_obtenerDatosUsuario($db, $id);
        editarUsuario('Edite los datos:', $usuario, 'Modificar Datos', 'Administrador');
        break;

        case 'Modificar': //Si realizamos la edicion, mostraremos el formulario de forma no editable
          editarUsuario('Confirme la modificacion:', ['nombre'=>$_POST['user_nombre'],
          'apellidos'=>$_POST['user_apellidos'],
          'email'=>$_POST['user_email'],
          'clave'=>$_POST['user_clave'],
          'direccion'=>$_POST['user_direccion'],
          'telefono'=>$_POST['user_telefono'],
          'fotografia'=>$imagen,
          'editable'=>false,
          'tipo' => $_POST['tipo_user'],
          'estado' => $_POST['estado_user'],
          'id_usuario'=>$_POST['id']], 'Confirmar modificacion', 'Administrador');
        break;

        case 'ModificarOk': //Si confirmarmos la edicion, procedemos a actualizar la incidencia de la BD
          $msg = DB_editarUsuario($db, $id, ['nombre'=>$_POST['user_nombre'],
          'apellidos'=>$_POST['user_apellidos'],
          'email'=>$_POST['user_email'],
          'clave'=>$clave_modificada,
          'direccion'=>$_POST['user_direccion'],
          'telefono'=>$_POST['user_telefono'],
          'fotografia'=>$imagen,
          'editable'=>false,
          'tipo' => $_POST['tipo_user'],
          'estado' => $_POST['estado_user']]);
          if ($msg===true) {
              $info[] = 'El usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'].' ha sido actualizado';
              DB_log($db,$_SESSION['nombre_usuario'],'El usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'].' ha sido actualizado');
          } else {
              $info[] = 'No se ha podido actualizar '.$_POST['user_nombre'].' '.$_POST['user_apellidos'];
              $info[] = $msg;
              DB_log($db,$_SESSION['nombre_usuario'],'Error ala ctualizar a '.$_POST['user_nombre'].' '.$_POST['user_apellidos']);
          }
          
          header('refresh: 5; url=gestion_Usuarios.php');
          echo "Redireccionado en 5 seg..";
        break;
      
        case 'Añadir Usuario':
            AnadirUsuario('Confirmar registro');
            break;

        case 'Confirmar registro':
            
            if (!empty($_FILES['foto_usuario_registro']['tmp_name'])) {
                $datos['fotografia'] = addslashes(file_get_contents($_FILES['foto_usuario_registro']['tmp_name']));
            } else {
                $imagen = "../vista/fotos/foto_defecto.jpeg";
                $tamimagen = filesize($imagen);
                $fp = fopen($imagen, 'rb'); 
                $contenido = fread($fp, $tamimagen);
                $contenido = addslashes($contenido);
                fclose($fp); 
            
                $datos['fotografia'] = $contenido;
                
            }
            

            if (empty($_POST['user_nombre']) || empty($_POST['user_apellidos']) || empty($_POST['user_email']) || empty($_POST['user_clave'])) {
                echo "<p class='msg_error'>Algún campo obligatorio está vacío.</p>";
            } else {
                $datos = ['nombre'=>$_POST['user_nombre'],
                          'apellidos'=>$_POST['user_apellidos'],
                          'email'=>$_POST['user_email'],
                          'clave'=>$_POST['user_clave'],
                          'direccion'=>$_POST['user_direccion'],
                          'telefono'=>$_POST['user_telefono'],'tipo'=>$_POST['tipo_user'],
                          'estado'=>$_POST['estado_user'],'fotografia'=>$datos['fotografia']];

                $msg = DB_añadirUsuario($db, $datos, "Administrador");
                if ($msg===true) {
                    $info[] = 'El usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'].' ha sido registrado';
                    DB_log($db,$_SESSION['nombre_usuario'],'El usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'].' ha sido registrado');
                } else {
                    $info[] = 'No se ha podido registrar el usuario '.$_POST['user_nombre'].' '.$_POST['user_apellidos'];
                    $info[] = $msg;
                    DB_log($db,$_SESSION['nombre_usuario'],'Error al registrar a '.$_POST['user_nombre'].' '.$_POST['user_apellidos']);
                }
                
                header('refresh: 5; url=gestion_Usuarios.php');
                echo "Redireccionado en 5 seg..";
            }
            break;
        case 'Cancelar':
          header('Location: gestion_Usuarios.php');
        break;

        default:
        $usuarios_totales=DB_getListadoUsuariosTotales($db);
        Ver_listadoUsuarios($usuarios_totales);
        break;
  }

    if (isset($info) ) {
        msgError($info);
    }
    DB_desconexion($db);
} else {
    msgError($db);
}

echo "</div></div>";




barraHTML();
//footerHTML();
?>



