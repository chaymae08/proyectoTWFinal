<?php



//funciones para el inicio 

function inicio(){
    //login con base de datos
    //.......
    ob_start();
    if (isset($_GET['logout'])) {
        logout();
    }

    //codigo HTML 
    echo <<<HTML
    <!DOCTYPE html>
  <html lang="es" dir="ltr">
      

  <head>
    <meta charset="UTF-8">
    <title>Los vecinos</title>
    <meta name="author" content="Chaymae Mtafe Boukdir">
    <link rel="shortcut icon" type="image/x-icon" href="/~aulas/proyecto_final/vista/fotos/imagen1.png" />
    <link rel="stylesheet" href="/~aulas/proyecto_final/vista/estilo.css">

  </head>
  <body>

    
        <div class="cabecera">
          <img src="/~aulas/proyecto_final/vista/fotos/imagen2.jpg" alt="Logo header" class="foodHeader" />
          <h1 id="titulo">Quéjate ¡no te calles!</h1>
        </div>



HTML;
} 
//hacer funciones para el menu de incidencias 
function menu(){

    echo <<< HTML
    <nav>
      <ul class="menu">
        <li><a href="/~aulas/proyecto_final/index.php"> <strong>Página de inicio</strong></a></li>
        <li><a href="/~aulas/proyecto_final/controlador/listado.php"> <strong>Ver incidencias</strong></a></li>

HTML;
 //Si el usuario esta registrado, permitimos que añada una incidencia
    if (isset($_SESSION['nombre_usuario'])) {
        echo "<li><a href='/~aulas/proyecto/controlador/anadir_Incidencia.php' ><strong>Añadir Incidencia</strong></a></li>";
        echo "<li><a href='/~aulas/proyecto/controlador/mis_Incidencias.php' ><strong>Mis Incidencias</strong></a></li>";
    }

    echo<<< HTML
        <li><a href="/~aulas/proyecto/controlador/pag_contacto.php"><strong>Página de contacto</strong></a></li>
      </ul>
    </nav>
    HTML;

    if (isset($_SESSION['admin'])) {
      echo"<nav>
      <ul class='menu'>
      <li><a href='/~aulas/proyecto_final/controlador/gestion_Usuarios.php' ><strong>Gestión de usuarios</strong></a></li>
      <li><a href='/~aulas/proyecto_final/controlador/log.php' ><strong>LOG</strong></a></li>
      <li><a href='/~aulas/proyecto_final/controlador/gestion_BBDD.php' ><strong>Gestión de la BBDD</strong></a></li>
      </ul>
    </nav>";
    }



}

//Funcion para la barra lateral, incluyendo las otras utilidades
function barraHTML()
{
  $params = getParmregistro($_POST);

  // Acceso desde formulario de login
  
if (isset($_POST["nombre_usuario"]) && ($params["err"]=='')) {
  $db = DB_conexion();
  $_SESSION["nombre_usuario"] = $params["nombre_usuario"];
  DB_log($db, $_SESSION['nombre_usuario'], "Ha iniciado sesión");
  header("Location: /~aulas/proyecto_final/index.php");
  exit();
}

// Acceso desde formulario de logout

if (isset($_POST["logout"])) {
  $db = DB_conexion();
  DB_log($db, $_SESSION['nombre_usuario'], "Se ha desconectado del sistema");
  DB_desconexion($db);
  logout();
  header("Location: /~aulas/proyecto_final/index.php");
  exit();
}

// Acceso desde formulario de editar su usuario
if (isset($_POST["editarme"])) {
  header("Location: /~aulas/proyecto_final/controlador/editar_usuario.php");
  exit();
}
echo <<< HTML

  <aside class="Barra_lateral">

    <section class="cajasLateral" id="Login">
HTML;


  if (isset($_SESSION["nombre_usuario"]) && ($params["err"]=='')) {
    // Si la sesión está establecida
    bienvenido($_SESSION["nombre_usuario"]);
} else {
    // Si la sesión no está establecida
    login($params);
    
}
echo " </section>";
  if (!isset($_SESSION["nombre_usuario"])) {
    echo "
  <section class='cajasLateral' id='registro'>
  <h4>Registro</h4>
  <p>Puedes registrarte aquí:</p>
  <p><a href='/~aulas/proyecto_final/controlador/gestion_registro.php'>Cuestionario de registro</a></p>
  </section>";
}

}
// Función para obtener y validar los parámetros de inicio de sesión
function getParmregistro($params)
{
    $results = [
        'enviado' => false,
        'errusuario' => '',
        'errclave' => '',
        'nombre_usuario' => '',
        'clave' => '',
        'err' => '',
    ];

    if (isset($params['nombre_usuario'], $params['clave'])) { // El formulario ha sido enviado
        $results['enviado'] = true;
        $results['nombre_usuario'] = $params['nombre_usuario'];
        $results['clave'] = $params['clave'];

        $db = DB_conexion();
        if (is_string($db)) {
            msgError($db);
            return $results;
        }

        
        // Comprobar usuario y contraseña
        if (empty($params['nombre_usuario']) || empty($params['clave']) || !DB_comprobarUsuario($db, $params['nombre_usuario'], ($params['clave']))) {
            $results['err'] = 'Usuario o contraseña incorrectos o cuenta no activa';
        }
        
      

        // Comprobar si el usuario es administrador de la páquina -> tiene más ventajas en la página
        if (DB_comprobarUsuario($db, $params['nombre_usuario'], ($params['clave'])) && DB_comprobarAdmin($db, $params['nombre_usuario'])) {
            $_SESSION['admin'] = 'admin';
        }

        // Guardar datos en las variables de sesión
        $_SESSION['id_usuario'] = DB_idUsuario($db, $results['nombre_usuario']);
        $_SESSION['nombre_usuario'] = DB_nombreUsuario($db, $_SESSION['id_usuario']);
        $_SESSION['foto_usuario'] = DB_fotoUsuario($db, $_SESSION['id_usuario']);
    }

    return $results;
  }
function login($params)
{
    // Inicializar los parámetros si no se han enviado
    if (!$params['enviado']) {
        $params = ['nombre_usuario' => '', 'clave' => '', 'err' => ''];
    }

    // Obtener la URL de la página actual
    $self = $_SERVER['PHP_SELF'];

    // Mostrar el formulario de login
    echo <<<HTML
    <form class='login' action='{$self}' method='post'>
        <h3>Login</h3>
        <div class='form_login'>
            <label>
                Usuario:
                <input type='text' name='nombre_usuario' value='{$params['nombre_usuario']}' />
            </label>
            <label>
                Clave:
                <input type='password' name='clave' value='{$params['clave']}' />
            </label>
HTML;

    // Mostrar el mensaje de error si existe
    if (!empty($params['err'])) {
        echo "<p class='error'>{$params['err']}</p>";
    }

    echo <<<HTML
        </div>
        <input type='submit' name='login' value='Login'/>
    </form>
HTML;
}
function bienvenido($nombre)
{
    // Determinar el tipo de usuario
    $tipo = isset($_SESSION['admin']) ? 'Administrador' : 'Colaborador';

    // Mostrar el mensaje de bienvenida y el tipo de usuario
    echo "<p>Bienvenido: <strong>{$nombre}</strong></p>";
    echo "<p style='color:blueviolet'>{$tipo}</p>";

    // Mostrar la foto del usuario si existe
    /*
    if (!empty($_SESSION['foto_usuario'])) {
        $fotoBase64 = base64_encode($_SESSION['foto_usuario']);
        echo "<img class='fotoUsuario' src='data:image/.png;base64,{$fotoBase64}' />";
    }*/
        // Conexión a la base de datos
      $db = DB_conexion();

      // Preparar la consulta SQL para obtener la foto del usuario
      $stmt = $db->prepare("SELECT fotografia FROM usuarios WHERE email = ?");
      $stmt->bind_param("s", $nombre);
      $stmt->execute();
      $stmt->bind_result($fotografia);
       $stmt->fetch();

      // Obtener el resultado de la consulta
     
          // Convertir la foto en formato binario a base64
        //  $fotoBase64 = base64_encode($fotografia);

          // Mostrar la imagen
          echo "<img class='fotoUsuario' src='data:image/.png;base64,{$fotografia}' />";
      

      // Cerrar la conexión a la base de datos
      $stmt->close();
      DB_desconexion($db);

    // Mostrar el formulario de logout y editar
    $self = $_SERVER['PHP_SELF'];
    echo <<<HTML
    <form class='logout' action='{$self}' method='post'>
        <input type='submit' name='editarme' value='Editar'/>
        <input type='submit' name='logout' value='Logout'/>
    </form>
HTML;
}
function logout() {
   // La sesión debe estar iniciada
   if (session_status()==PHP_SESSION_NONE) {
    session_start();
    }
// Borrar variables de sesión
$_SESSION = array();
session_unset();
// Obtener parámetros de cookie de sesión
$param = session_get_cookie_params();
// Borrar cookie de sesión
setcookie(
    session_name(),
    $_COOKIE[session_name()],
    time()-3597000,
    $param['path'],
    $param['domain'],
    $param['secure'],
    $param['httponly']
);
// Destruir sesión
session_destroy();
}
echo <<<HTML



HTML;







?>