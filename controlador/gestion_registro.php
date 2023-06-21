<?php
session_start();

include_once './gestion_registro.php';
include_once '../modelo/db.php';
require_once '../vista/codigo_html.php';
require_once '../controlador/formu_html.php';


/*
//Si estas logueado, no permitir acceso desde la url
if (isset($_SESSION['id_usuario'])) {
    header("LOCATION: /~aulas/proyecto_final/index.php");
}*/

//Funciones de HTML
inicio();
menu();

echo <<<HTML
<main>

<div class="cajaPrincipal">
  <h1>Cuestionario de registro</h1>
HTML;

$msg='';

 //Obtenemos datos POST
 
 $params = obtenerParametrosRegistro($_POST);
 formularioRegistro($params, 'Registrar usuario');
 registrarUsuario($params,'Confirmar registro','Colaborador');
 // Obtener datos POST
$params = obtenerParametrosRegistro($_POST);

/*
// Si hemos editado la incidencia, recuperamos la clave y la imagen y añadimos el usuario a la bd
if (isset($_POST['accion']) && $_POST['accion'] == 'Confirmar registro') {
    
    $db = DB_conexion();
    if (!is_string($db)) {
        $params['clave'] = $_SESSION['clave_registro'];
        $params['foto_usuario'] = $_SESSION['foto_registro'];
        $res = registrarUsuario($db, $params,'Colaborador');
        if ($res === true) {
            echo "<p>Su registro se ha completado con éxito. Podrá usar su cuenta cuando un administrador la active.</p>";
        } else {
            $info[] = $res;
        }

        if (isset($info) && msgCount($info) > 0) {
            msgError($info);
            header('refresh: 3; url=/~aulas/proyecto_final/index.php');
        }

        DB_desconexion($db);
    } else {
        msgError($db);
    }
} elseif ((isset($_POST['accion']) && $_POST['accion'] == 'Registrar usuario') &&
    ($params['errnombre'] == '' && $params['errapellidos'] == '' && $params['erremail'] == '' && $params['errdireccion'] == '' && $params['errtelefono'] == '' && $params['errfoto'] == '')) {
    $params['editable'] = false;
    $_SESSION['clave_registro'] = ($_POST['clave']);

    // Si no hay foto, asignaremos una imagen por defecto
    $imagen = "../vista/fotos/foto_defecto.png";
    $contenido = addslashes(file_get_contents($imagen));

    $params['foto_usuario'] = (isset($_FILES['foto_usuario_registro']['tmp_name']) && $_FILES['foto_usuario_registro']['tmp_name'] != '') ? addslashes(file_get_contents($_FILES['foto_usuario_registro']['tmp_name'])) : $contenido;
    $_SESSION['foto_registro'] = $params['foto_usuario'];
    formularioRegistro($params, 'Confirmar registro');
} else {
    formularioRegistro($params, 'Registrar usuario');
}
formularioRegistro($params, 'Registrar usuario');

barraHTML();*/
echo "</div></div>";

 
?>
