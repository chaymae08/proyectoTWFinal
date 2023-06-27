<?php
session_start();

include_once './formu_html.php';
include_once '../vista/codigo_html.php';
include_once '../modelo/db.php';



inicio();
menu();

echo <<<HTML
<main>

<div class="cajaPrincipal">
  <h1>Búsqueda de incidencias</h1>
 
HTML;
//Declaramos variables para que tengan ese valor en caso de no asignarle otro
$accion='';
$valorada = false;
$texto_buscar = '';
$lugar_buscar='';
$estados_buscar = '';
$palabra_clave = '';
$imagen='';


$datos['fotos_descripcion_edicion']=[];
$datos['fotografia'] = [];



if (isset($_POST['accion'])) {
   //Obtenemos las variables POST 
    switch ($_POST['accion']) {

        //Si la accion es buscar, obtenemos los parametros de busqueda
        case 'Aplicar criterios de búsqueda':

            if (isset($_POST['texto_buscar']) && $_POST['texto_buscar']!='') {
                $texto_buscar =$_POST['texto_buscar'];
            }
  
            if (isset($_POST['lugar_buscar']) && $_POST['lugar_buscar']!='') {
                $lugar_buscar =$_POST['lugar_buscar'];
            }
  
            if (isset($_POST['estados_buscar']) && $_POST['estados_buscar']!='') {
                $estados_buscar =$_POST['estados_buscar'];
            }
  
            if (isset($_POST['orden_busqueda']))
                $orden=$_POST['orden_busqueda'];
  
            if (isset($_POST['items_buscar']) && $_POST['items_buscar']!='') {
                $items_buscar =$_POST['items_buscar'];
            }
            if (isset($_POST['palabra_clave']) && $_POST['palabra_clave']!='') {
                $palabra_clave =$_POST['palabra_clave'];
            }
            

            
            
  
            $accion='Buscar';
  
          

        break;
        case 'Limpiar búsqueda':
            // Aquí puedes resetear todas las variables de búsqueda
            if (isset($_POST['texto_buscar'])) {
                unset($_POST['texto_buscar']);
            }
  
            if (isset($_POST['lugar_buscar'])) {
                unset($_POST['lugar_buscar']);
            }
  
            if (isset($_POST['estados_buscar'])) {
                unset($_POST['estados_buscar']);
            }
  
            if (isset($_POST['orden_busqueda']))
                unset($_POST['orden_busqueda']);
  
            if (isset($_POST['items_buscar'])) {
                unset($_POST['items_buscar']);
            }
            if (isset($_POST['palabra_clave'])) {
                unset($_POST['palabra_clave']);
            }
  
          break;
        
        case 'Ver': // Visualizaremos una incidencia
            $accion = 'Ver';
            $id_incidencias = $_POST['id_incidencias'];

          break;
        case 'Editar': // Presentar formulario y pedir confirmación
            $accion = 'Editar';
            $id_incidencias = $_POST['id_incidencias'];
          break;
        case 'Enviar_comentario':        // Visualizaremos el comentario
            $accion = 'Comentario_enviado';
            $id_incidencias = $_POST['id_incidencias'];
            if(!isset($_POST['autor_comentario']))
              $_POST['autor_comentario']=NULL;

          break;

          case 'Cancelar':
            $accion='Cancelar';
          break;

        case 'Añadir_valoracion_neg':
            if(isset($_SESSION['id_usuario'])) {
                // usuario registrado
                $id_incidencias = $_POST['id_incidencias'];
                $valoracion = -1;
                $accion = 'Valoracion_enviada';
            } else {
                // visitante
                $id_incidencias = $_POST['id_incidencias'];
                $valoracion = -1;
                $accion = 'Valoracion_enviada'; 
                 // Aquí se configura la cookie después de que el visitante ha hecho click en el botón de valoración
        $cookie_name = 'valoracion_incidencia_'.$_POST['id_incidencia'];
        $cookie_value = $_POST['accion'] == 'Añadir_valoracion_pos' ? 'positiva' : 'negativa';
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // La cookie expirará en 30 días
            }
            
            
      

          break;
          case 'Añadir_valoracion_pos':
            if(isset($_SESSION['id_usuario'])) {
                // usuario registrado
             $id_incidencias = $_POST['id_incidencias'];
            $valoracion = 1;
            $accion = 'Valoracion_enviada';
            } else {
                // visitante
            $id_incidencias = $_POST['id_incidencias'];
            $valoracion = 1;
            $accion = 'Valoracion_enviada';
                 
         // Aquí se configura la cookie después de que el visitante ha hecho click en el botón de valoración
        $cookie_name = 'valoracion_incidencia_'.$_POST['id_incidencias'];
        $cookie_value = $_POST['accion'] == 'Añadir_valoracion_pos' ? 'positiva' : 'negativa';
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // La cookie expirará en 30 días
            }
            
         

            break;
          case 'Borrar comentario': // Borramos comentario
                $accion = 'Comentario_borrado';
                $id_incidencias = $_POST['id_incidencias'];
              break;

          case 'Modificar Datos': // Modificación realizada, comprobando que no haya campos vacios
                $id_incidencias = $_POST['id_incidencias'];
                if ($_FILES['incidencia_img']['error']==0) { //Si se ha subido imagen,la guardamos
                    $imagen=addslashes(file_get_contents($_FILES['incidencia_img']['tmp_name']));
                }
                $_SESSION['fotografia'] = $imagen; //Guardamos la imagen para despues recuperarla
      
                if($_FILES['fotos_descripcion_edicion']['error'][0]==0){ //Si se ha subido al menos una foto de la descripcion , las guardamos en datos
                  foreach($_FILES["fotos_descripcion_edicion"]['tmp_name'] as $key => $tmp_name)
                    $datos['fotos_descripcion_edicion'][] = addslashes(file_get_contents($_FILES['fotos_descripcion_edicion']['tmp_name'][$key]));
                }
                else {
                    $datos['fotos_descripcion_edicion']=[];
                }
      
                //Si se han seleccionado fotos para mantener antiguas, comprobamos que el indice de esa foto en el array ha sido mantenido en el checkbox
                //En ese caso, la añadimos a datos
                if(isset($_POST['fotos_antiguas_numero']) && isset($_SESSION['fotos_antiguas']) ){
      
                  for($i=0; $i<= sizeof($_SESSION['fotos_antiguas']); $i++){
                    if(gettype(array_search($i, $_POST['fotos_antiguas_numero']))=='integer'){
                      $datos['fotos_descripcion_edicion'][] = addslashes($_SESSION['fotos_antiguas'][$i]); //Guardar la imagen en una variable de sesion para recuperarla despues
                    }
                  }
              }
                //Una vez usadas las variables de session, las borramos para que no se acumulen
                unset($_SESSION['fotos_antiguas']);
                unset($_SESSION['fotos_finales']);
            
                //Guardamos las fotos de datos en session para recuperarlas despues
                 foreach($datos['fotos_descripcion_edicion'] as $f){
                    $_SESSION['fotos_finales'][]=$f;
                  }
      
                  //Si se han dejado campos vacios, indicarlo
                if ($_POST['incidencia_titulo']=='' || $_POST['incidencia_lugar']==''|| $_POST['incidencia_descripcion']=='' || $_POST['incidencia_fecha']=='' || $_POST['incidencia_palabraC']=='') {
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
                $imagen=  $_SESSION['fotografia'];
                $accion = 'ModificarOk';
      
                //Si hay fotos en session de la descripcion de la incidencia, las recuperamos
                if(isset($_SESSION['fotos_finales']) && !empty($_SESSION['fotos_finales'])){
                  foreach($_SESSION['fotos_finales'] as $f)
                    $datos['fotos_descripcion_edicion'][]=$f;
                  unset($_SESSION['fotos_finales']);
                }
              break;
              case 'Borrar': 
                $accion = 'Borrar';
                $id_incidencias = $_POST['id_incidencias'];
              break;
              case 'Confirmar Borrado': // Borrado confirmado
                $accion = 'BorrarOk';
                $id_incidencias = $_POST['id_incidencias'];
              break;
  
    }
} //conexion con bd y accion del boton 
    if (!is_string($db=DB_conexion())) {
        //Dependiendo del orden, obtenemos las incidencias. Si no se ha indicado, la ponemos ascendendente
    
            $incidencias_totales=DB_getListadoIncidenciaTotal($db);
            $estado = DB_getListadoEstados($db);
    
        if ($accion=='') { //Si todavia no se ha especificado accion, mostramos el formulario de busqueda y todas las incidencias
            FORM_buscarIncidencia($estado);
          Vista_listadoIncidencias($incidencias_totales);

        }
    
    
    switch ($accion) {           //PARA AÑADIR ELEMENTOS A LA BASE DE DATOS 

        case 'Buscar': //Si hemos ralizado una busqueda:
  
          FORM_buscarIncidencia($estado);
  
          //Mostramos las incidencias dependiendo de la busqueda realizada
  
              $incidencias=DB_getListadoIncidencias($db, $texto_buscar, $lugar_buscar, $estados_buscar, $orden, $items_buscar , $palabra_clave);
              // Mostrar listado
              if ($incidencias!=false) {
               Vista_listadoIncidencias($incidencias);
              } else {
                  $info[] = 'No se ha encontrado ninguna incidencia con esos parámetros';
                  $info[] = mysqli_error($db);
              }
  
        break;

        case 'Ver': //Al pulsar el boton de ver, procederemos a visualizar la  de forma no editable
           $incidencia = DB_getIncidencias($db, $id_incidencias);
          
            $incidencia['editable']=false;
            $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']); //Obtenemos el autor de la incidencia
            $comentarios=DB_getComentarios($db,$id_incidencias); //Obtenemos los comentarios de la incidencia
            $estados = DB_getEstadosIncidencia($db,$id_incidencias); //Obtenemos el listado de estados 
            $incidencia['estado']=$estados;

            if(isset($_SESSION['id_usuario'])) //Si el usuario esta registrado, comprobamos si ya ha valorado la 
              $valorada=DB_getValoradaUsuario($db, $id_incidencias, $_SESSION['id_usuario']);
              
            $valorada = null;
             Vista_verIncidencia ($db,$incidencia,$comentarios,$valorada);
          break;
          case 'Comentario_enviado':
         
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
            
            //$valorada=NULL;

              Vista_verIncidencia($db,$incidencia,$comentarios,$valorada);
      
          
            break;

            case 'Cancelar':
                header('/~aulas/proyecto_final/controlador/listado.php');
              break;

              //VALORACION
          
              case 'Valoracion_enviada': //metemos la valoracion en la bd
            $valorada=DB_getValoradaUsuario($db, $id_incidencias, $_SESSION['id_usuario']);
            if ($valorada == false){
                if(DB_añadirValoracion($db, ['id_incidencias'=>$id_incidencias,'autor_valoracion'=>$_SESSION['id_usuario'],'valoracion'=>$valoracion])){
                    echo "<p class='error'>Valoración insertada  </p>";
                    DB_log($db,$_SESSION['nombre_usuario'],"Ha valorado una incidencia");
                }else {
                    echo "<p class='error'>No se ha podido insertar la valoracion  </p>";
                    DB_log($db,$_SESSION['nombre_usuario'],"Error al valorar una incidencia");
                }
            }
                //Vista_verIncidencia($db,$incidencia,$comentarios,$valorada);
                $incidencia = DB_getIncidencias($db, $id_incidencias);
                $incidencia['editable']=false;
                $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
                $comentarios=DB_getComentarios($db,$id_incidencias);
                $estados = DB_getEstadosIncidencia($db,$id_incidencias);
                $incidencia['estado']=$estados;
                if(isset($_SESSION['id_usuario']))
                  $valorada=DB_getValoradaUsuario($db, $id_incidencias, $_SESSION['id_usuario']);
              
               //$valorada=NULL;
  
                Vista_verIncidencia($db,$incidencia,$comentarios,$valorada);
        
            

                break;

                case 'Comentario_borrado': //Al borrar  el comentario,lo borramos de la bd y  veremos la incidencia
                    $id_incidencias = $_POST['id_incidencias'];
                    if(DB_borrarComentario($db, $_POST['id_comentario'], $id_incidencias)){
                      echo "<p class='error'>Comentario borrado  </p>";
                      DB_log($db,$_SESSION['nombre_usuario'],"Ha borrado un comentario");
                    }else {
                      echo "<p class='error'>No se ha podido borrar el comentario  </p>";
                      DB_log($db,$_POST['autor_comentario'],"Error al borrar un comentario");
                    }

                    $incidencia = DB_getIncidencias($db, $id_incidencias);
                    $incidencia['editable']=false;
                    $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
                    $comentarios=DB_getComentarios($db,$id_incidencias);
                    $estados = DB_getEstadosIncidencia($db,$id_incidencias);
                    $incidencia['estado']=$estados;
                    if(isset($_SESSION['id_usuario']))
                      $valorada=DB_getValoradaUsuario($db, $id_incidencias, $_SESSION['id_usuario']);
                  
                   //$valorada=NULL;
      
                    Vista_verIncidencia($db,$incidencia,$comentarios,$valorada);
                  break;

                 
                  case 'Editar': //Obtenemos la incidencia y nos saldra el formulario de edcion
                    $incidencia = DB_getIncidencias($db, $id_incidencias);

                    $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
                   
                    $estado = DB_getEstadosIncidencia($db,$id_incidencias);
                    $estados =DB_getListadoEstados($db);

                    $incidencia['estados']= $estado;

                    unset($_SESSION['fotos_antiguas']);
                    $_SESSION['fotos_antiguas']=$incidencia['fotografia'];
                    FORMU_editarIncidencia('Edite los datos:', $incidencia, 'Modificar Datos', $estados);
                  break;
          
                  case 'Modificar': //Si realizamos la edicion, mostraremos el formulario de forma no editable
                    $estado = DB_getEstadosIncidencia($db,$id_incidencias);
                    $estados =DB_getListadoEstados($db);

                    FORMU_editarIncidencia('Confirme la modificacion:', ['id_incidencias'=>$id_incidencias,
                    'texto'=>$_POST['incidencia_titulo'],
                    'estados'=>$_POST['incidencia_estados'],
                    'descripcion'=>$_POST['incidencia_descripcion'],
                    'lugar'=>$_POST['incidencia_lugar'],
                    'fecha'=>$_POST['incidencia_fecha'],
                    'palabra_clave'=>$_POST['incidencia_palabraC'],
                    'fotografia'=>$imagen,
                    'editable'=>false,
                    'fotos_descripcion_edicion'=>$datos['fotos_descripcion_edicion']], 'Confirmar modificacion', $estados);
                  break;
          
                  case 'ModificarOk': //Si confirmarmos la edicion, procedemos a actualizar la incidencia de la BD
                    $id_incidencias = $_POST['id_incidencias'];
                    $imagen=  $_SESSION['fotografia'];
                     //Si hay fotos en session de la descripcion de la incidencia, las recuperamos
                if(isset($_SESSION['fotos_finales']) && !empty($_SESSION['fotos_finales'])){
                  foreach($_SESSION['fotos_finales'] as $f)
                    $datos['fotos_descripcion_edicion'][]=$f;
                  unset($_SESSION['fotos_finales']);
                }
                    $msg = DB_editarIncidencia($db, ['id_incidencias'=>$id_incidencias,
                    'texto'=>$_POST['incidencia_titulo'],
                    'estados'=>$_POST['incidencia_estados'],
                    'descripcion'=>$_POST['incidencia_descripcion'],
                    'lugar'=>$_POST['incidencia_lugar'],
                    'fecha'=>$_POST['incidencia_fecha'],
                    'palabra_clave'=>$_POST['incidencia_palabraC'],
                    'fotografia'=>$imagen,
                    'fotos_descripcion_edicion'=>$datos['fotos_descripcion_edicion'] ]);
                    if ($msg===true) {
                        $info[] = 'La incidencia '.$_POST['incidencia_titulo'].' ha sido actualizada';
                        DB_log($db,$_SESSION['nombre_usuario'],"Ha modificado una incidencia");
                    } else {
                        $info[] = 'No se ha podido actualizar '.$_POST['incidencia_titulo'];
                        $info[] = $msg;
                        DB_log($db,$_SESSION['nombre_usuario'],"Error al modificar una incidencia");
                    }
                    
                    header('refresh: 5; url=listado.php');
                    echo "Redireccionado en 5 seg..";
                  break;
                  case 'Borrar': //Al pulsar el boton de borrar, procederemos a visualizar la incidencias de forma no editable
                    $incidencia = DB_getIncidencias($db, $id_incidencias);

                    $incidencia['autor']=DB_nombreUsuario($db,$incidencia['autor']);
                   
                    $estado = DB_getEstadosIncidencia($db,$id_incidencias);
                    $estados =DB_getListadoEstados($db);
                    $incidencia['estados']= $estados;
                    FORMU_editarIncidencia('Confirme borrado de esta incidencia:', $incidencia, 'Confirmar Borrado', $estados);

                  break;
            
                  case 'BorrarOk': //Si confirmamos el borrado, procedemos a borrar la incidencias de la BD y redireccionar a la pagina de listado
                    $id_incidencias = $_POST['id_incidencias'];

                    if (DB_borrarIncidencia($db, $id_incidencias)) {
                        $info[] = 'La incidencias '.$_POST['incidencia_titulo'].' ha sido borrada';
                        DB_log($db,$_SESSION['nombre_usuario'],"Ha borrado una incidencias");
            
                    } else {
                        $info[] = 'No se ha podido borrar '.$_POST['incidencia_titulo'];
                        DB_log($db,$_SESSION['nombre_usuario'],"Error al borrar una incidencias");
                    }
                    
                    header('refresh: 5; url=listado.php');
                    echo "Redireccionado en 5 seg..";
                  break;
            }
            
            
            
        
            if (isset($info) ) {
                msgError($info);
            }
            DB_desconexion($db);
        } else {
            msgError($db);
        }
        

    //comprobacion base de datos
    //......
    /*
            $estados = array('Pendiente', 'Comprobada', 'Tramitada', 'Irresoluble', 'Resuelta');
            FORM_buscarIncidencia($estados);*/
            echo "</div></div>";

            barraHTML();
            footerHTML();

            

?>
