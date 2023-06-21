<?php

//Conexion a la BD, devuelve recurso asociado a la BD o error
function DB_conexion()
{
   //$db = mysqli_connect('localhost', 'chaymae082223', 'Qc4rTVuC', 'chaymae082223');
   $db = mysqli_connect('localhost', 'root', 'aulas', 'chaymae082223');
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
  // Preparar la consulta SQL
  $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? AND clave = ?");

  // Verificar si la preparación fue exitosa
  if ($stmt === false) {
      // Manejar el error - por ejemplo, imprimir un mensaje de error y terminar la ejecución
      die('Error en la preparación: ' . $db->error);
  }

  // Vincular los parámetros a la consulta SQL
  $stmt->bind_param("ss", $usuario, $clave);

  // Ejecutar la consulta SQL
  $stmt->execute();

  // Obtener el resultado
  $resultado = $stmt->get_result();
  

  // Verificar si existe algún usuario con el correo y la clave proporcionados
  if ($resultado->num_rows > 0) {
      // El correo y la contraseña coinciden
      return true;
  }

  // El correo y la contraseña no coinciden
  return false;
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


//Numero de incidenciass total en la BD
function DB_getNumIncidenciasTotal($db)
{
    $res = mysqli_query($db, "SELECT COUNT(*) FROM incidencias");
    $num = mysqli_fetch_row($res)[0];
    mysqli_free_result($res);
    return $num;
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
//Obtener el listado de incidencias que contiene la busqueda segun el orden
function DB_getListadoIncidencias($db, $texto_buscar, $lugar, $estado, $orden, $items)
{
    $res1='';
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

    //Consulta con los parametros menos los estados
    $res = mysqli_query($db, "SELECT * FROM incidencias WHERE texto LIKE '%$texto_buscar%' AND lugar LIKE '%$lugar%' ORDER BY $o LIMIT $items ");

    if ($res) { // Si no hay error
      if (mysqli_num_rows($res)>0) {// Si hay alguna tupla de respuesta
        $tabla = mysqli_fetch_all($res, MYSQLI_ASSOC);
      } else {    // No hay resultados para la consulta
          $tabla = false;
      }

      //Si se han conseguido resultados
      if($tabla){
        if($estado!=''){
        //Para cada tupla obtenida, comprobamos que su estado está en la lista de estados seleccionados, en caso afirmativo, lo metemos en una nueva tabla: tabla2
        foreach($tabla as $incidencias){
          if(in_array($incidencias['estado_id'], $estado)){
            array_push($tabla2, $incidencias);
          }
        }

        // para poder acceder más fácilmente después
        if($tabla2!=''){
          unset($tabla);
          $tabla = $tabla2;
        }
      }

      mysqli_free_result($res);// Liberar memoria de la consulta
      }
    }else {// Error en la consulta
      $tabla = false;
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
      /*  echo "The value of id is: ";
      var_dump($id);*/

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






?>