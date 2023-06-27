<<?php

session_start();

include_once './formu_html.php';
include_once '../modelo/db.php';
require_once '../vista/codigo_html.php';

//Si no esta logueado, no permitir acceso desde la url
if (!isset($_SESSION['id_usuario'])) {
    header("LOCATION: /~aulas/proyecto_final/index.php");
}

inicio();
menu();

echo <<<HTML
<main>

<div class="cajaPrincipal">
  <h1>Editar usuario</h1>
HTML;


$accion='';
$imagen='';
$clave_modificada='';


//Depende de la accion seleccionada
if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {


        case 'Modificar Datos': // Modificación realizada, comprobando que no haya campos vacios
            $id = $_POST['id'];
        
            if ($_FILES['usuario_img']['error']==0) { //Si se ha subido imagen,la guardamos
                $imagen=addslashes(file_get_contents($_FILES['usuario_img']['tmp_name']));
            }
            $_SESSION['imagen_modificarUsuario'] = $imagen; //Guardamos la imagen para despues recuperarla
            $_SESSION['clave_modificar']=$_POST['user_clave'];

            if (empty($_POST['user_nombre']) || empty($_POST['user_apellidos']) || empty($_POST['user_email'])  || empty($_POST['user_clave']) 
            || (!preg_match('/(\+34|0034|34)?[ -]*(6|7)[ -]*([0-9][ -]*){8}/', $_POST['user_telefono']) && !empty($_POST['user_telefono'])) ) {
             $info_vacios = 'Algún campo no es válido o está vacío';
            echo "<p class='msg_error'>$info_vacios</p>";}
            if (!isset($info_vacios)) {
                $accion='Modificar';
            }
            
        break;

        case 'Confirmar modificacion': //Si se ha confirmado la modificacion, recuperamos la imagen y la clave
            $imagen=  $_SESSION['imagen_modificarUsuario'];
            $clave_modificada=$_SESSION['clave_modificar'];
            $id = $_POST['id'];
            $accion = 'ModificarOk';
        break;


        case 'Cancelar':
          $accion='Cancelar';
        break;
    }
}


if (!is_string($db=DB_conexion())) {
    switch ($accion) {

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

        case 'ModificarOk': //Si confirmarmos la edicion, procedemos a actualizar la receta de la BD
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

        case 'Cancelar':
          header('Location: editar_user.php');
        break;

        //Si modificas tus propios datos
        default:
        $usuario= DB_obtenerDatosUsuario($db,  $_SESSION['id_usuario']);
        editarUsuario('Edite los datos:', $usuario, 'Modificar Datos', 'Administrador');
        break;
  }

    if (isset($info)) {
        msgError($info);
    }
    DB_desconexion($db);
} else {
    msgError($db);
}

echo "</div></div>";


barraHTML();

footerHTML();
