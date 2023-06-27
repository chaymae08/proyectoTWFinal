<?php

include_once 'dbparametros.php';


//Conexion a la BD, devuelve recurso asociado a la BD o error
function DB_conexion()
{
   //$db = mysqli_connect('localhost', 'chaymae082223', 'Qc4rTVuC', 'chaymae082223');
   $db = mysqli_connect('localhost', 'root', 'aulas', 'chaymae082223');
   $db->set_charset("utf8mb4");
    if (!$db) {
        return "Error de conexión a la base de datos (".mysqli_connect_errno().") : ".mysqli_connect_error();
    }
    // Establecer la codificación de los datos almacenados
    mysqli_set_charset($db, "utf8");
    return $db;
}




// Desconexión de la BBDD
function DB_desconexion($db)
{
    mysqli_close($db);
}

//Devuelve el listado de estados
function DB_getListadoEstados($db)
{
    $res = mysqli_query($db, "SELECT * FROM listaestados");

    if ($res) { // Si no hay error
  if (mysqli_num_rows($res)>0) {// Si hay alguna tupla de respuesta
    $tabla = mysqli_fetch_all($res, MYSQLI_ASSOC);
  } else {    // No hay resultados para la consulta
      $tabla = [];
  }
        mysqli_free_result($res);// Liberar memoria de la consulta
    } else {// Error en la consulta
 $tabla = false;
    }
    return $tabla;
}
function DB_log($db, $usuario, $descripcion) {
  // Obtener la fecha y hora actual
  $fecha = date('Y-m-d H:i:s');

  // Preparar la consulta SQL
  $sql = "INSERT INTO log (usuario, fecha, descripcion) VALUES (?, ?, ?)";

  // Preparar la declaración
  $stmt = $db->prepare($sql);

  // Vincular los parámetros
  $stmt->bind_param("sss", $usuario, $fecha, $descripcion);

  // Ejecutar la declaración
  $stmt->execute();
}
function DB_comprobarUsuario($db, $usuario, $clave) {
  $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
  
  if ($stmt === false) {
      die('Error en la preparación: ' . $db->error);
  }
  
  $stmt->bind_param("s", $usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();

  if ($resultado->num_rows > 0) {
      $fila = $resultado->fetch_assoc();
      
      if (!isset($fila['clave'])) {
          die("La clave no se encuentra en la base de datos");
      }
      
      $hash = $fila['clave'];
      if (password_verify($clave, $hash)) {
          return true;
      } else {
          die("La verificación de la contraseña ha fallado. Clave proporcionada: '$clave', hash de la base de datos: '$hash'");
      }
  }
  
  return false;
}


//Llamar a la funcion que tiene los errores
function msgError($msg)
{
    echo "<div class='msg_error'>";
    _msgErrorR($msg);
    echo '</div>';
}

//Mostrar error uno por uno
function _msgErrorR($msg)
{
    if (is_array($msg)) {
        foreach ($msg as $v) {
            _msgErrorR($v);
        }
    } else {
        echo "<p>".htmlentities($msg)."</p>";
    }
}

function DB_comprobarAdmin($db, $usuario) {
      // Preparar la consulta SQL
      $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ? AND tipo = 'Administrador'";

      // Preparar la declaración
      $stmt = $db->prepare($sql);

      // Vincular los parámetros
      $stmt->bind_param("s", $usuario);

      // Ejecutar la declaración
      $stmt->execute();

      // Obtener el resultado
      $stmt->bind_result($num);
      $stmt->fetch();

      // Cerrar la declaración
      $stmt->close();

      // Devolver el resultado
      return $num > 0;
}
function DB_idUsuario($db, $correo) {
      // Preparar la consulta SQL
      $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";

      // Preparar la declaración
      $stmt = $db->prepare($sql);

      // Vincular los parámetros
      $stmt->bind_param("s", $correo);

      // Ejecutar la declaración
      $stmt->execute();

      // Obtener el resultado
      $stmt->bind_result($id);
      $stmt->fetch();

      // Cerrar la declaración
      $stmt->close();

      // Devolver el resultado
      return $id;
}
  function DB_nombreUsuario($db, $id) {
        // Preparar la consulta SQL
        $sql = "SELECT nombre, apellidos FROM usuarios WHERE id_usuario = ?";

        // Preparar la declaración
        $stmt = $db->prepare($sql);

        // Vincular los parámetros
        $stmt->bind_param("i", $id);

        // Ejecutar la declaración
        $stmt->execute();

        // Obtener el resultado
        $stmt->bind_result($nombre, $apellidos);
        $stmt->fetch();

        // Concatenar nombre y apellidos
        $nombreCompleto = $nombre . ' ' . $apellidos;

        // Cerrar la declaración
        $stmt->close();

        // Devolver el resultado
        return $nombreCompleto;
  }

  function DB_fotoUsuario($db, $id) {
        // Preparar la consulta SQL
        $sql = "SELECT fotografia FROM usuarios WHERE id_usuario = ?";

        // Preparar la declaración
        $stmt = $db->prepare($sql);

        // Vincular los parámetros
        $stmt->bind_param("i", $id);

        // Ejecutar la declaración
        $stmt->execute();

        // Obtener el resultado
        $stmt->bind_result($foto);
        $stmt->fetch();

        // Cerrar la declaración
        $stmt->close();

        // Devolver el resultado
        return $foto;
}


//Obtenemos todas las incidencias
function DB_getListadoIncidenciaTotal($db)
{

    $res = mysqli_query($db, "SELECT * FROM incidencias ORDER BY texto ASC");

    if ($res) { // Si no hay error
    if (mysqli_num_rows($res)>0) {// Si hay alguna tupla de respuesta
      $tabla = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {    // No hay resultados para la consulta
        $tabla = [];
    }
        mysqli_free_result($res);// Liberar memoria de la consulta
    } else {// Error en la consulta
   $tabla = false;
    }
    return $tabla;
  }
  function DB_getListadoIncidencias($db, $texto_buscar, $lugar, $estado, $orden, $items, $palabra_clave)
{
    $tabla=false;
    $tabla2=[];

    //Dependiendo del orden especificado
    if ($orden == 'Antiguedad') {
        $o = 'fecha DESC';
    } else if ($orden=='Positivos'){
        $o = 'positivos DESC';
    }else if ($orden=='PositivosNetos'){
        $o = '(positivos - negativos) DESC';
    }

    //Primera consulta para obtener las incidencias
    $res = mysqli_query($db, "SELECT * FROM incidencias WHERE (texto LIKE '%$texto_buscar%' OR lugar LIKE '%$lugar%' OR texto LIKE '%$palabra_clave%') ORDER BY $o LIMIT $items ");
    if ($res && mysqli_num_rows($res)>0) { // Si no hay error y hay alguna tupla de respuesta
        $tabla = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $incidencia_ids = array_column($tabla, 'id_incidencias');
        $incidencia_ids_str = implode(', ', $incidencia_ids);
    }
    
    //Segunda consulta para obtener los estados de las incidencias obtenidas
    if(isset($incidencia_ids_str)){
        $res2 = mysqli_query($db, "SELECT * FROM estados WHERE id_incidencia IN ($incidencia_ids_str)");
        $estados = false;
        if ($res2 && mysqli_num_rows($res2)>0) { // Si no hay error y hay alguna tupla de respuesta
            $estados = mysqli_fetch_all($res2, MYSQLI_ASSOC);
        }
    }
    
    //Si se han conseguido resultados
    if($tabla && $estados){
        //Para cada tupla obtenida, comprobamos que su estado está en la lista de estados seleccionados
        foreach($tabla as $incidencia){
            $matched = false; 
            foreach($estados as $estado){
                //'id_incidencias' es el campo que relaciona las incidencias con los estados
                if($incidencia['id_incidencias'] == $estado['id_incidencia']){
                    $matched = true;
                    break; 
                }
            }
            if($matched){
                array_push($tabla2, $incidencia);
            }
        }

        // para poder acceder más fácilmente después
        if(!empty($tabla2)){
            $tabla = $tabla2;
        }
    }

    if(isset($res)) {
        mysqli_free_result($res);// Liberar memoria de la consulta
    }
    if(isset($res2)) {
        mysqli_free_result($res2);// Liberar memoria de la consulta
    }
  
    return $tabla;
}



  //Devuelve los comentarios de una incidencia
function DB_getComentarios($db, $id)
{$res= mysqli_query($db, "SELECT * FROM comentarios WHERE id_incidencias='$id'");
  if ($res) { // Si no hay error
      if (mysqli_num_rows($res)>0) {// Si hay alguna tupla de respuesta
      $comentarios = mysqli_fetch_all($res, MYSQLI_ASSOC);
      } else {    // No hay resultados para la consulta
          $comentarios = [];
      }
      mysqli_free_result($res);// Liberar memoria de la consulta
  } else {// Error en la consulta
    $comentarios = false;
  }
  return $comentarios;
}
//Devuelve las estados de una incidencia
function DB_getEstadosIncidencia($db,$id)
{
  $vector_estados='';
  $columns = []; //
     

  $res = mysqli_query($db, "SELECT * FROM estados WHERE id_incidencia='$id'");

  if ($res) { // Si no hay error
    if (mysqli_num_rows($res)>0) {// Si hay alguna tupla de respuesta
        while ($row = $res->fetch_assoc()) {
            $columns[] = $row['id_estados'];
        }
      }

      foreach($columns as $c){
        $res2 = mysqli_query($db, "SELECT estado FROM listaestados WHERE id='$c'");
        if($res2){
          $row=$res2->fetch_assoc();
          foreach($row as $r)
          $vector_estados.= $r;
          $vector_estados.= ". ";
        }
      }
    }

    //En caso de error en alguna consulta, devuelve ' ';

return $vector_estados;
}
/*
//Devuelve true o false dependiendo si el usuario ya ha valorado esa incidencias
function DB_getValoradaUsuario($db, $id_incidencia, $id_usuario){
  $res = mysqli_query($db, "SELECT valoracion FROM valoraciones WHERE id_incidencias='$id_incidencias' AND id_usuario='$id_usuario'");
  if($res){
    if(mysqli_num_rows($res)>0){
      $val=mysqli_fetch_row($res)[0];
      return $val;
    }
    else
      return false;
  }else
  return false;
}*/

//Obtener la incidencias indicada por el id
function DB_getIncidencias($db, $id) //Consulta preparada
{
  $prep = mysqli_prepare($db, "SELECT * FROM incidencias WHERE id_incidencias=?");
  $val = $id;
  mysqli_stmt_bind_param($prep, 'i', $val); //está vinculando la variable $val al primer marcador de posición en la consulta SQL preparada que está almacenada en $prep.
  if (mysqli_stmt_execute($prep)) {
      mysqli_stmt_bind_result($prep, $iid, $texto, $lugar, $estado, $fecha, $positivos ,$negativos, $imagen, $palabra, $estado, $descripcion,$num_comentarios,$autor);
      if (mysqli_stmt_fetch($prep)) {
          $incidencias['id_incidencias']=$iid;
          $incidencias['autor'] = $autor;
          $incidencias['texto'] = $texto;
          $incidencias['lugar'] = $lugar;
          $incidencias['fecha'] = $fecha;
          $incidencias['estado_id'] = $estado;
          $incidencias['fotografia'] = $imagen;
          $incidencias['positivos']=$positivos;
          $incidencias['negativos']=$negativos;
          $incidencias['palabra_clave']=$palabra;
          $incidencias['descripcion']=$descripcion;

      } else {
          $incidencias = false;
      } // No hay resultados
  } else {
      $incidencias=false;
  }
  mysqli_stmt_close($prep);

    //Obtenemos los estados de esa incidencia
    $res2 = mysqli_query($db, "SELECT * FROM estados WHERE id_incidencia='$id'");
    if ($res2) { // Si no hay error
      if (mysqli_num_rows($res2)>0) {// Si hay alguna tupla de respuesta

          while ($row = $res2->fetch_assoc()) {
              $columns[] = $row['id_incidencia'];
          }

          $incidencias['estado']=$columns;
      }/* else {    // No hay resultados para la consulta
          $incidencias = [];
      }*/
    }


    //Obtenemos las fotos de esa incidencias
    $res3 = mysqli_query($db, "SELECT fotos_incidencia FROM imagenes_incidencias WHERE id_incidencias='$id'");
    if ($res3) { // Si no hay error
      if (mysqli_num_rows($res3)>0) {// Si hay alguna tupla de respuesta

          while ($row = $res3->fetch_assoc()) {
              $columns2[] = $row['fotos_incidencia'];
           
          }
          $incidencias['fotografia']=$columns2;
      } else {    // No hay resultados para la consulta
        $incidencias['fotografia']='';
       

      }
    }
    return $incidencias;
}
function DB_añadir_comentario($db, $datos) {
  $id_incidencias = $datos['id_incidencias'];
  $comentarios = $datos['comentario'];
  $fecha = $datos['fecha_comentario'];
  $id_usuario = isset($datos['autor_comentario']) ? $datos['autor_comentario'] : null;

  if ($id_usuario !== null) {
    $res = mysqli_query($db, "INSERT INTO comentarios ( id_incidencias, comentarios, fecha_creacion, id_usuario)
      VALUES ( '$id_incidencias', '$comentarios', '$fecha','$id_usuario')");
  } else {
    $res = mysqli_query($db, "INSERT INTO comentarios (id_incidencias, comentarios, fecha_creacion)
      VALUES ('$id_incidencias', '$comentarios', '$fecha')");
  }

  if (!$res) {
    $info[] = 'Error en la consulta ' . __FUNCTION__;
    $info[] = mysqli_error($db);
  } else {
    $res2 = mysqli_query($db, "UPDATE incidencias SET num_comentarios = num_comentarios + 1 WHERE id_incidencias = '$id_incidencias'");
  }

  if (isset($info)) {
    return $info;
  } else {
    return true;
  }
}

//Devuelve true o false dependiendo si el usuario ya ha valorado esa incidencia
function DB_getValoradaUsuario($db, $id_incidencias, $id_usuario){
  $res = mysqli_query($db, "SELECT valoracion FROM valoraciones WHERE id_incidencias='$id_incidencias' AND id_usuario='$id_usuario'");
  if($res){
    if(mysqli_num_rows($res)>0){
      return true; // si existe alguna fila, entonces el usuario ya ha valorado
    }
    else
      return false;
  }else
    return false;
}


//Añade una valoracion
function DB_añadirValoracion($db, $datos){

    $res = mysqli_query($db, "INSERT INTO valoraciones (id_incidencias,id_usuario,valoracion)
        VALUES ('{$datos['id_incidencias']}','{$datos['autor_valoracion']}','{$datos['valoracion']}')");


    if (!$res) {
        $info[] = 'Error en la consulta '.__FUNCTION__;
        $info[] = mysqli_error($db);
    }
    
    else{
      // Cálculo del total de valoraciones positivas
      $resPos = mysqli_query($db, "SELECT COUNT(*) FROM valoraciones WHERE id_incidencias='{$datos['id_incidencias']}' AND valoracion='1'");
      $totalPos = mysqli_fetch_row($resPos)[0];

      // Cálculo del total de valoraciones negativas
      $resNeg = mysqli_query($db, "SELECT COUNT(*) FROM valoraciones WHERE id_incidencias='{$datos['id_incidencias']}' AND valoracion='-1'");
      $totalNeg = mysqli_fetch_row($resNeg)[0];

      // Actualización de la tabla de incidencias
      $resUpdate = mysqli_query($db, "UPDATE incidencias SET positivos='{$totalPos}', negativos='{$totalNeg}' WHERE id_incidencias='{$datos['id_incidencias']}'");

      if (!$resUpdate) {
          $info[] = 'Error en la consulta '.__FUNCTION__;
          $info[] = mysqli_error($db);
      }
  }

    if (isset($info)) {
        return $info;
    } else {
        return true;
    } // OK
}
function DB_anadirIncidencia($db, $datos)
  {
    // Comprobar si el usuario está logueado
    $autor = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'NULL';

    $res = mysqli_query($db, "INSERT INTO incidencias (descripcion,texto,lugar,fecha,palabra_clave,autor)
        VALUES ('{$datos['descripcion']}','{$datos['nombre']}','{$datos['lugar']}','{$datos['fecha']}','{$datos['palabraC']}',{$autor})");

      if ($res) {
          
          //Metemos los estados con el id de la ultima incidencia insertada
         $res2 = mysqli_query($db, "SELECT id_incidencias FROM incidencias ORDER BY id_incidencias DESC LIMIT 1");

          if ($res2) {
              $id= 0;
              $row=$res2->fetch_assoc();
              $id= (int) $row['id_incidencias'];
              
             // foreach ($datos['estados'] as $c) {
                  $res3 = mysqli_query($db, "INSERT INTO estados (id_estados,id_incidencia) VALUES ('1','{$id}')");
              //}
          }
          //Insertamos las fotos en el id mencionado
          $res4=true;
          if($datos['fotos_descripcion']!=''){
            foreach($datos['fotos_descripcion'] as $foto){
              $res4 = mysqli_query($db, "INSERT INTO imagenes_incidencias (id_incidencias,fotos_incidencia) VALUES ('{$id}','{$foto}')");
            }
          }
      }

      if (!$res   || !$res4) {
          $info[] = 'Error en la consulta '.__FUNCTION__;
          $info[] = mysqli_error($db);
      }

      if (isset($info)) {
          return $info;
      } else {
          return true;
      } // OK
  }
  //Devuelve las incidencias de un usuario
function DB_getListadoMisIncidencias($db)
{
    $res = mysqli_query($db, "SELECT * FROM incidencias WHERE autor='{$_SESSION['id_usuario']}'");

    if ($res) { // Si no hay error
  if (mysqli_num_rows($res)>0) {// Si hay alguna tupla de respuesta
    $tabla = mysqli_fetch_all($res, MYSQLI_ASSOC);
  } else {    // No hay resultados para la consulta
      $tabla = [];
  }
        mysqli_free_result($res);// Liberar memoria de la consulta
    } else {// Error en la consulta
 $tabla = false;
    }
    return $tabla;
}
//Obtener listado de usuarios reguistrados en el sistema 
function DB_getListadoUsuariosTotales($db)
{
    $res = mysqli_query($db, "SELECT * FROM usuarios ");  //obtener todos los usuarios 


    if ($res) { // Si no hay error
    if (mysqli_num_rows($res)>0) {// Si hay alguna tupla de respuesta
      $tabla = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {    // No hay resultados para la consulta
        $tabla = [];
    }
        mysqli_free_result($res);// Liberar memoria de la consulta
    } else {// Error en la consulta
   $tabla = false;
    }
    return $tabla;
}

//Borrar el usuario indicado por el id 
  function DB_borrarUsuario($db, $id)
  {
      mysqli_query($db, "DELETE FROM comentarios WHERE id_usuario='$id'");  //borramos su comenatrio
      mysqli_query($db, "DELETE FROM incidencia WHERE autor='$id'");     //borramos su incidencia
      mysqli_query($db, "DELETE FROM valoraciones WHERE id_usuario='$id'");           //borramos sus valoraciones 


      mysqli_query($db, "DELETE FROM usuarios WHERE id_usuario='$id'");    //borramos por completo el usuario 
      if (mysqli_affected_rows($db)==1) { 
          return true;
      } else {
          return false;
      }
  }
  //Devuelve todos los datos del usuario registrado
function DB_obtenerDatosUsuario($db, $id)
{   //utilizamos consulta preparada 
    $prep = mysqli_prepare($db, "SELECT * FROM usuarios WHERE id_usuario=?");
    $val = $id;
    mysqli_stmt_bind_param($prep, 'i', $id);
    if (mysqli_stmt_execute($prep)) {
        mysqli_stmt_bind_result($prep, $unombre, $uapellidos, $uemail, $udireccion, $utelefono,  $uclave,$ufoto, $utipo, $uid,$uactivo, );
        if (mysqli_stmt_fetch($prep)) {
            $usuario['id_usuario'] = $uid;
            $usuario['fotografia'] = $ufoto;
            $usuario['nombre'] = $unombre;
            $usuario['apellidos'] = $uapellidos;
            $usuario['email'] = $uemail;
            $usuario['direccion'] = $udireccion;
            $usuario['telefono'] = $utelefono;
            $usuario['tipo'] = $utipo;
            $usuario['estado'] = $uactivo;
        } else {
            $usuario = false;
        } // No hay resultados
    } else {
        $usuario=false;
    }
    mysqli_stmt_close($prep);
    return $usuario;
}
function DB_editarUsuario($db, $id, $datos)
{   //La clave no se la podemos mostrar a ninguna persona 
    //No comprobamos que no se repita el correo, ya que en la BBDD lo tenemos como unique, y en el caso de repetir, mostramos el error de la bdd
    if ($datos['fotografia']=='') { //Si no hemos subido una nueva imagen, no modificar la que habia

      if ($datos['clave']=='') { //Si no hemos cambiado clave ni imagen, dejar las anteriores
          $res = mysqli_query($db, "UPDATE usuarios SET nombre='{$datos['nombre']}', apellidos='{$datos['apellidos']}',email='{$datos['email']}',
         direccion_postal='{$datos['direccion']}',telefono='{$datos['telefono']}',tipo='{$datos['tipo']}',estado='{$datos['estado']}'
         WHERE id_usuario='$id'");
      } else { //Si hemos cambiado clave pero no imagen, cambiar la clave
        $hashed_password = password_hash($datos['clave'], PASSWORD_DEFAULT); // Encriptar la contraseña
          $res = mysqli_query($db, "UPDATE usuarios SET nombre='{$datos['nombre']}', apellidos='{$datos['apellidos']}',email='{$datos['email']}',
         direccion_postal='{$datos['direccion']}',telefono='{$datos['telefono']}',tipo='{$datos['tipo']}',estado='{$datos['estado']}',clave='{$hashed_password}'
         WHERE id_usuario='$id'");
      }
    } else { //Si hemos subido imagen,sustituir la anterior

        if ($datos['clave']=='') { //Si no hemos cambiado clave pero si imagen, cambiar solo imagen
            $res = mysqli_query($db, "UPDATE usuarios SET nombre='{$datos['nombre']}', apellidos='{$datos['apellidos']}',email='{$datos['email']}',
         direccion_postal='{$datos['direccion']}',telefono='{$datos['telefono']}',tipo='{$datos['tipo']}',estado='{$datos['estado']}', fotografia='{$datos['fotografia']}'
         WHERE id_usuario='$id'");
        } else { //Si hemos cambiado clave e imagen, cambiar ambas
          $hashed_password = password_hash($datos['clave'], PASSWORD_DEFAULT); // Encriptar la contraseña

            $res = mysqli_query($db, "UPDATE usuarios SET nombre='{$datos['nombre']}', apellidos='{$datos['apellidos']}',email='{$datos['email']}',
         direccion_postal='{$datos['direccion']}',telefono='{$datos['telefono']}',tipo='{$datos['tipo']}',estado='{$datos['estado']}', fotografia='{$datos['fotografia']}',clave='{$hashed_password}'
         WHERE id_usuario='$id'");
        }
    }


    if (!$res) {
        $info[] = 'Error al actualizar';
        $info[] = mysqli_error($db);
    }


    if (isset($info)) {
        return $info;
    } else {
        return true;
    }
}

//Funcion para añadir a un usuario 
function DB_añadirUsuario($db, $datos, $tipo)
{
    // Comprobar si ya hay un usuario con el mismo nombre
    $res = mysqli_query($db, "SELECT COUNT(*) FROM usuarios WHERE email='{$datos['email']}'");
    $num = mysqli_fetch_row($res)[0];
    mysqli_free_result($res);

    // Obtener la imagen en formato base64
    $image_base64 = '';
    if (!empty($_FILES['foto_usuario_registro']['tmp_name'])) {
        $image = file_get_contents($_FILES['foto_usuario_registro']['tmp_name']);
        //$image_base64 = base64_encode($image);
    } else {
        // Si no se proporciona una foto, establecer una por defecto
        $image = file_get_contents('../vista/fotos/foto_defecto.png');
       // $image_base64 = base64_encode($default_image);
    }

    // Obtener la contraseña del usuario
    $clave = $datos['clave'];

    // Encriptar la contraseña
    $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

    if ($num>0) {
        $info[] = 'Ya hay un usuario con ese email';
    } else {
        $stmt = mysqli_prepare($db, "INSERT INTO usuarios (nombre,apellidos,email,fotografia,clave,tipo,direccion_postal,telefono,estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");

        mysqli_stmt_bind_param($stmt, "sssssssss", $datos['nombre'], $datos['apellidos'], $datos['email'], $image, $clave_encriptada, $datos['tipo'], $datos['direccion'], $datos['telefono'],$datos['estado']);

        $res = mysqli_stmt_execute($stmt);

        if (!$res) {
            $info[] = 'Error en la consulta ' . __FUNCTION__;
            $info[] = mysqli_error($db);
        }
    }
    if (isset($info)) {
        return $info;
    } else {
        return true;
    } // OK
}
//Funcion para restaurar un archivo
function DB_Restaurar_base($db, $archivo)
{
    $sql = '';
    $error = '';
    if (file_exists($archivo)) {
        $query = file($archivo);

        foreach ($query as $q) {
          if (substr($q, 0, 2) == '--' || $q == '') {
              continue;
            }
            $sql .= $q;
            if (substr(trim($q), - 1, 1) == ';') {
                $result = mysqli_query($db, $sql);
                if (! $result) {
                    $error .= mysqli_error($db) . "\n";
                }
                $sql = '';
            }
        }
        if ($error) {
            $salida = "Error";

        } else {
            $salida = "Éxito";
        }
    }

    return $salida;
}

//Borra un comentario especifico de una incidencia
function DB_borrarComentario($db, $id, $id_incidencias)
{
    mysqli_query($db, "DELETE FROM comentarios WHERE id_comentarios='$id'");

    if (mysqli_affected_rows($db)==1) {
        mysqli_query($db, "UPDATE incidencias SET num_comentarios=num_comentarios-1 WHERE id_incidencias='{$id_incidencias}'");
        return true;
    } else {
        return false;
    }
}
//Funcion para actualizar incidencia de la BD
function DB_editarIncidencia($db, $datos)
{
  if ($datos['fotografia']=='') { //Si no hemos subido una nueva imagen, no modificar la que habia
      $res = mysqli_query($db, "UPDATE incidencias SET texto='{$datos['texto']}',autor='{$_SESSION['id_usuario']}',
       descripcion='{$datos['descripcion']}',lugar='{$datos['lugar']}',fecha='{$datos['fecha']}'
       WHERE id_incidencias='{$datos['id_incidencias']}'");
  } else { //Si hemos subido imagen,sustituir la anterior
      $res = mysqli_query($db, "UPDATE incidencias SET texto='{$datos['texto']}',autor='{$_SESSION['id_usuario']}',
     descripcion='{$datos['descripcion']}',lugar='{$datos['lugar']}',fecha='{$datos['fecha']},palabra_clave='{$datos['palabra_clave']}'
     WHERE id_incidencias='{$datos['id_incidencias']}'");
  }

  //Borramos los estados antiguos e insertamos los nuevos
  mysqli_query($db, "DELETE FROM estados WHERE id_incidencia='{$datos['id_incidencias']}'");
  foreach ($datos['estados'] as $c) {
      $res2 = mysqli_query($db, "INSERT INTO estados (id_incidencia,id_estados) VALUES ('{$datos['id_incidencias']}','{$c}')");
  }

  //Borramos las fotos antiguas e insertamos las nuevas
  mysqli_query($db, "DELETE FROM imagenes_incidencias WHERE id_incidencias='{$datos['id_incidencias']}'");
  foreach ($datos['fotos_descripcion_edicion'] as $f) {
      $res2 = mysqli_query($db, "INSERT INTO imagenes_incidencias (id_incidencias,fotos_incidencia) VALUES ('{$datos['id_incidencias']}','{$f}')");
  }

  if (!$res || !$res2) {
      $info[] = 'Error al actualizar';
      $info[] = mysqli_error($db);
  }


  if (isset($info)) {
      return $info;
  } else {
      return true;
  }
}

//Borrar la incidencia indicado por el titulo
function DB_borrarIncidencia($db, $id)
{
    mysqli_query($db, "DELETE FROM imagenes_incidencias WHERE id_incidencias='$id'");
    mysqli_query($db, "DELETE FROM valoraciones WHERE id_incidencias='$id'");
    mysqli_query($db, "DELETE FROM comentarios WHERE id_incidencias='$id'");
    mysqli_query($db, "DELETE FROM estados WHERE id_incidencia='$id'");

    mysqli_query($db, "DELETE FROM incidencias WHERE id_incidencias='$id'");
    if (mysqli_affected_rows($db)==1) {
        return true;
    } else {
        return false;
    }
}
//Numero de incidencias total en la BD
function DB_getNumIncidenciasTotal($db)
{
    $res = mysqli_query($db, "SELECT COUNT(*) FROM incidencias");
    $num = mysqli_fetch_row($res)[0];
    mysqli_free_result($res);
    return $num;
}

//Numero de usuarios total en la BD
function DB_getNumUsuariosTotal($db)
{
    $res = mysqli_query($db, "SELECT COUNT(*) FROM usuarios");
    $num = mysqli_fetch_row($res)[0];
    mysqli_free_result($res);
    return $num;
}

//Devuelve las tres incidencia con mas comentarios
function DB_getMasComentadas($db){
    $res = mysqli_query($db, "SELECT texto FROM incidencias ORDER BY num_comentarios DESC LIMIT 3");
    if($res){
      if(mysqli_num_rows($res)>0){
        while ($row = $res->fetch_assoc())
            $val[] = $row['texto'];
        return $val;
      }
      else
        return false;
    }else
    return false;
  }
  function registrarUsuario($db,$params,$tipo) {
    // Configuración de la base de datos
    $dbHost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = 'aulas';
    $dbName = 'chaymae082223';

    // Crear conexión
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
   

    
   // Obtener la contraseña del usuario
   $clave = $params['clave'];

   // Encriptar la contraseña
   $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

    if($tipo == ('Colaborador')){
    // Preparar la consulta SQL
    $sql = "INSERT INTO usuarios (nombre, apellidos, email, direccion_postal,telefono, clave, fotografia,tipo) VALUES (?, ?, ?, ?, ?, ?, ?,?)";

    // Preparar la declaración
    $stmt = $conn->prepare($sql);

         $image_base64 = '';
     if (isset($_FILES['foto_usuario_registro']) && is_uploaded_file($_FILES['foto_usuario_registro']['tmp_name'])) {
         $image = file_get_contents($_FILES['foto_usuario_registro']['tmp_name']);
     } else {
         // Si no se proporciona una foto, establecer una por defecto
         $image = file_get_contents('../vista/fotos/foto_defecto.jpeg');
     }
    

    // Vincular los parámetros
    $stmt->bind_param("ssssssss", $params['nombre_usuario'], $params['apellidos_usuario'], $params['email_usuario'], $params['direccion_usuario'], $params['telefono_usuario'], $clave_encriptada, $image,$tipo);
}

if($tipo == ('Administrador')){
 // Preparar la consulta SQL
 $sql = "INSERT INTO usuarios (nombre, apellidos, email, direccion_postal,telefono, clave, fotografia,tipo,estado) VALUES (?, ?, ?, ?, ?, ?, ?,?,?)";

 // Preparar la declaración
 $stmt = $conn->prepare($sql);

 // Obtener la imagen en formato base64
 $image_base64 = '';
 if (isset($_FILES['foto_usuario_registro'])) {
     $image = file_get_contents($_FILES['foto_usuario_registro']['tmp_name']);
   //  $image_base64 = base64_encode($image);
 } else {
  // Si no se proporciona una foto, establecer una por defecto
  $image = file_get_contents('../vista/fotos/foto_defecto.jpeg');
  //$image_base64 = base64_encode($default_image);
}

 // Vincular los parámetros
 $stmt->bind_param("sssssssss", $params['nombre_usuario'], $params['apellidos_usuario'], $params['email_usuario'], $params['direccion_usuario'], $params['telefono_usuario'], $clave_encriptada, $image,$params['tipo'],$params['estado']);


}
    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo "<p style='color:black;'>Nuevo registro creado exitosamente</p>";
    } else {
        echo "<p style='color:black;'>Error: " . $sql . "<br>" . $conn->error;
    }
  
    // Cerrar la conexión
    $conn->close();
}















?>