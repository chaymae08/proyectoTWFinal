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
$items_buscar='';
if (isset($_POST['accion'])) {

    switch ($_POST['accion']) {

        
       
        
        case 'Ver': // Visualizaremos una incidencia
            $accion = 'Ver';
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
            $id_incidencias = $_POST['id_incidencias'];
            $valoracion = -1;
            $accion = 'Valoracion_enviada';

          break;
          case 'Añadir_valoracion_pos':
            $id_incidencias = $_POST['id_incidencias'];
            $valoracion = 1;
            $accion = 'Valoracion_enviada';

            break;
  
    }
}
    if (!is_string($db=DB_conexion())) {
        //Dependiendo del orden, obtenemos las incidencias. Si no se ha indicado, la ponemos ascendendente
            $incidencias_totales=DB_getListadoMisincidencias($db);

            $estado = DB_getListadoEstados($db);
    
        if ($accion=='') { //Si todavia no se ha especificado accion, mostramos el formulario de busqueda y todas las incidencias
          Vista_listadoIncidencias($incidencias_totales);
            //Borramos esas variables por si el usuario en mitad de una accion, vuelve al listado

            /*
            unset($_SESSION['fotos_finales']);
            unset($_SESSION['fotos_antiguas']);*/
        }
    
    
    switch ($accion) {           //PARA AÑADIR ELEMENTOS A LA BASE DE DATOS 

        case 'Buscar': //Si hemos ralizado una busqueda:
  
          FORM_buscarIncidencia($estado);
  
          //Mostramos las incidencias dependiendo de la busqueda realizada
  
              $incidencias=DB_getListadoIncidencias($db, $texto_buscar, $lugar_buscar, $estados_buscar, $orden, $items_buscar );
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
            
            // $valorada=NULL;

              Vista_verIncidencia($db,$incidencia,$comentarios,$valorada);
      
          
            break;
              
            case 'Cancelar':
                header('/~aulas/proyecto_final/controlador/mis_Incidencias.php');
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
              
              // $valorada=NULL;
  
                Vista_verIncidencia($db,$incidencia,$comentarios,$valorada);
        
            

                break;
            }
            
            
        
            if (isset($info) && msgCount($info)>0) {
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

?>
