<?php
function FORM_buscarIncidencia($estado)
{
  $id='1';
    echo "<div class='frm_incidencia_buscar'> <form action='$_SERVER[PHP_SELF]' method='POST'> 
    
    <h2>Criterios de búsqueda</h2>
    <h3 class='tit_incidencias'>Incidencias que contengan</h3>

        <div class='frm_incidencia_buscar_input'> <label for='incidencia_texto'>Texto de búsqueda(Título incidencia):</label>
        <input type='text' name='texto_buscar'";
        if(isset($_POST['texto_buscar']))
          echo "value='{$_POST['texto_buscar']}'";

        echo "/> </div>

        <div class='frm_incidencia_buscar_input'> <label for='palabra_clave'>Palabra clave:</label>
        <input type='text' name='palabra_clave'";
        if(isset($_POST['palabra_clave']))
          echo "value='{$_POST['palabra_clave']}'";

        echo "/> </div>

        <div class='frm_incidencia_buscar_input'> <label for='incidencia_lugar'>Lugar:</label>
        <input type='text' name='lugar_buscar'";
        if(isset($_POST['lugar_buscar']))
          echo "value='{$_POST['lugar_buscar']}'";

        echo "/> </div>


        <div class='frm_incidencia_buscar_estados'> <label for='incidencia_estados' ><strong> Estado: </strong></label>
        <div class='frm_inc_estados'>";
       
        //Sticky  , obtengo valor estados de la base de datos 
        foreach ($estado as $e) {
            echo "<div class='frm_incidencia_buscar_estados'><input type='checkbox' name='estados_buscar[]' value='{$e['id']}' ";
            if (isset($_POST['estados_buscar']) && $_POST['estados_buscar']!=''){
                foreach ($_POST['estados_buscar'] as $ids) {
                    if ($ids==$e['id']) {
                        echo "checked ";
                    }
                }
            }

            echo "/> {$e['estado']} </div>";
        }
      echo "</div></div>

      <h3 class='tit_busqueda'>Ordenar por</h3>

      <input type='radio' name='orden_busqueda' value='Antiguedad'";

    if ((isset($_POST['orden_busqueda']) && $_POST['orden_busqueda']=='Antiguedad') || !isset($_POST['orden_busqueda'])) {
        echo "checked";
    }

    echo "/> <label for='antiguedad'>Antigüedad (primero las más recientes)</label>

       <input type='radio' name='orden_busqueda' value='Positivos'";
        if ((isset($_POST['orden_busqueda']) && $_POST['orden_busqueda']=='Positivos')) {
            echo "checked";
        }
        echo "/> <label for='positivos'>Número de positivos (de más a menos)</label>

        <input type='radio' name='orden_busqueda' value='PositivosNetos'";
        if ((isset($_POST['orden_busqueda']) && $_POST['orden_busqueda']=='PositivosNetos')) {
            echo "checked";
        }
        echo "/> <label for='positivosNetos'>Número de positivos netos (de más a menos)</label>

        <div class='frm_incidencia_buscar_input'> <label for='incidencia_items'>Incidencias por página:</label>
        <input type='number' name='items_buscar'";
        if(isset($_POST['items_buscar']))
          echo "value='{$_POST['items_buscar']}'";

        echo "/> </div>

        <div class='incidencia_buscar'>  <input type='submit' name='accion' value='Aplicar criterios de búsqueda' /> </div>
        <div class='incidencia_buscar'>  <input type='submit' name='accion' value='Limpiar búsqueda' /> </div>

    </form>
  </div>";
}
function Vista_listadoIncidencias($datos)
{
    echo<<<HTML
  <div class='listado'>
    <table><tr>
      <th colspan="2">Listado de incidencias </th>
    </tr>
HTML;
    foreach ($datos as $v) {
        echo "<tr><td class='rec_titulo'> ".htmlentities($v['texto'])."</td>";
        echo "<td class='rec_botones'><form action='{$_SERVER['PHP_SELF']}' method='POST'>

                  <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />
                <input type='submit' name='accion' value='Ver' id='boton_ver'/>";
                /*

        //Si estas logueado, puedes editar y borrar
        if ((isset($_SESSION['usuario']) && isset($_SESSION['admin'])) || (isset($_SESSION['id_usuario']) && $v['idautor']==$_SESSION['id_usuario'])) {
            echo "<input type='submit' name='accion' value='Editar' id='boton_editar' />
                  <input type='submit' name='accion' value='Borrar' id='boton_borrar' />";
        }*/

        echo " </form></td>
              </tr>";
    }

    echo"</table></div>";
}
function Vista_verIncidencia($db, $v, $comentarios, $valorada)
{
    echo "

      <div class='VerIncidencia'>

        <div class='TituloIncidencia'>
                <h2 class='tincidencia'>".htmlentities($v['texto'])."</h2>
               
        </div>

        <div class='detalles_incidencia'>
          <p><strong>Lugar:</strong> ".htmlentities($v['lugar'])."</p>
          <p><strong>Fecha:</strong> ".htmlentities($v['fecha'])."</p>
          <p><strong>Estado:</strong> ".htmlentities($v['estado'])."</p>
          <p><strong>Autor:</strong> ".htmlentities($v['autor'])."</p>



          <p><strong>Valoraciones:</strong> Pos: " .htmlentities($v['positivos']). " Neg: " .htmlentities($v['negativos']). "</p>";
          

          
          echo "<div class='presentacion'>";
          echo "<p>".nl2br($v['descripcion'])."</p>";
          
          if(isset($v['fotografia']) && !empty($v['fotografia'])) {
              foreach($v['fotografia'] as $foto) {
                  echo "<img class='foto_inicial' src='data:image/jpg;base64,".base64_encode($foto)."'/>";
              }
          }
          echo "
                    </div>";
      
        

        "</div>

        

      <div class='proc'>";
      
      echo "</div>
      


      <div class='comentarios'>";
      //Para cada comentario, mostrarlo
      
    if ($comentarios) {
        foreach ($comentarios as $c) {
            echo "  <div class='comentario'>
                <p class='comentador'>".$c['fecha_creacion'].". ";
            if (isset($c['id_usuario'])) {
                echo "".DB_nombreUsuario($db, $c['id_usuario'])."</p>";
            } else {
                echo " Anónimo";
            }

            echo "<p>".$c['comentarios']."</p>";

            //Si eres administrador, puedes borra comentario
                if (isset($_SESSION['nombre_usuario']) && isset($_SESSION['admin'])) {
                    echo "<form action='{$_SERVER['PHP_SELF']}' method='POST'>
                          <input type='hidden' name='id_comentario' value='{$c['id_comentarios']}' />
                          <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />
                          <button type='submit' name='accion' value='Borrar comentario' id='boton_borrar'>
                            
                          </button>
                          </form>";
                }
                

                echo " </div>";
            }
        }
        echo "<div class='button-container'>";
        echo "<button type='submit' name='accion' value='Añadir_comentario' id='boton_anadir_comentario'>
                 
              </button>";

        echo "<div class='Enviar_comentario' style='display: none;'>
                <form action='$_SERVER[PHP_SELF]' method='POST'>
                    <textarea name='comentario' placeholder='Inserte aquí su comentario' cols='60' rows='6'></textarea><br />
                    <input type='hidden' name='fecha_comentario' value='".date('Y-m-d H:i:s')."' />
                 
                    <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />
                    <input type='submit' name='accion' value='Enviar_comentario'/>
                
              </div>";
        
        echo "<script>
                document.getElementById('boton_anadir_comentario').addEventListener('click', function() {
                    document.querySelector('.Enviar_comentario').style.display = 'block';
                });
              </script>";

            
        
        if (isset($_SESSION['id_usuario'])) {    //para guardar el usuario que comenta
            echo "<input type='hidden' name='autor_comentario' value='".$_SESSION['id_usuario']."' />";
        }
        echo "
            </form>";

              

  //Si estas registrado puedes mandar valoracion. Si ya la has mandado, se muestra tu valoracion

 // Verificar si el usuario está identificado o es un visitante
if (isset($_SESSION['id_usuario'])) {
    // Usuario identificado, mostrar formulario de valoración
    echo "<div class='botones_valoracion'>";
    
    if ($valorada == true) {
      echo "<p>Ya has valorado esta incidencia </p>";
    } else {
      echo "<div class='botones_valoracion'>
  <form action='$_SERVER[PHP_SELF]' method='POST'>
    <input type='hidden' name='autor_valoracion' value='".$_SESSION['id_usuario']."' />
    <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />
    <button type='submit' name='accion' value='Añadir_valoracion_pos' id='boton_anadir_valoracion_pos'></button>
  </form>

  <form action='$_SERVER[PHP_SELF]' method='POST'>
    <input type='hidden' name='autor_valoracion' value='".$_SESSION['id_usuario']."' />
    <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />
    <button type='submit' name='accion' value='Añadir_valoracion_neg' id='boton_anadir_valoracion_neg'></button>
  </form>
</div>";
        
   }
    echo "</div>";
  } else {
    echo "<div class='botones_valoracion'>";
$cookie_name = 'valoracion_incidencia_'.$v['id_incidencias'];

// Verificar si la cookie está configurada antes de usarla
$autor_valoracion = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';

if (!isset($_COOKIE[$cookie_name])) {
  echo "<form action='$_SERVER[PHP_SELF]' method='POST'>
  <input type='hidden' name='autor_valoracion' value='".$autor_valoracion."' />
  <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />
  <button type='submit' name='accion' value='Añadir_valoracion_pos' id='boton_anadir_valoracion_pos'>
  </button>
</form>";

 
 echo "<form action='$_SERVER[PHP_SELF]' method='POST'>
 <input type='hidden' name='autor_valoracion' value='".$autor_valoracion."' />
 <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />
 <button type='submit' name='accion' value='Añadir_valoracion_neg' id='boton_anadir_valoracion_neg'>
 </button>
</form>";
} else {
  echo "<p>Ya has valorado esta incidencia como ".$_COOKIE[$cookie_name]."</p>";
}

echo "</div>";
     
    }
  
  
   
      echo "  <div class='pie_pagina'><form action='$_SERVER[PHP_SELF]' method='POST'>
            <input type='hidden' name='id_incidencias' value='{$v['id_incidencias']}' />";
    //Si estas logueado, mostrar botones editar y borrar
    
    if (isset($_SESSION['admin'])) {
      echo "<button type='submit' name='accion' value='Editar' id='boton_editar'></button>
            <button type='submit' name='accion' value='Borrar' id='boton_borrar'></button>";
  }
  
    echo<<< HTML
       <input type='submit' name='accion' value='Cancelar' id='boton_cancelar' />
     
    </button>

    </div>

    </form></div>

  </div>
  
    


    


HTML;
}
//Obtener los parametros del regsitro
function obtenerParametrosRegistro($parametros,$tipo)
{
    $resultado = [
        'enviado' => false,
        'errnombre' => '',
        'errapellidos' => '',
        'erremail' => '',
        'errclave' => '',
        'errdireccion' => '',
        'errtelefono' => '',
        'errfoto' => '',
        'errestado' => '',
        'errtipo' => '',


        'nombre_usuario' => '',
        'apellidos_usuario' => '',
        'email_usuario' => '',
        'clave' => '',
        'direccion_usuario' => '',
        'telefono_usuario' => '',
        'foto_usuario' => '',
        'estado' => '',
        'tipo' => ''


    ];
    if($tipo == ('Colaborador'))
    $campos = ['nombre_usuario', 'apellidos_usuario', 'email_usuario', 'direccion_usuario', 'telefono_usuario', 'clave','foto_usuario'];
    if($tipo == ('Administrador'))
    $campos = ['nombre_usuario', 'apellidos_usuario', 'email_usuario', 'direccion_usuario', 'telefono_usuario', 'clave','foto_usuario','estado','tipo'];

    $camposEnviados = array_intersect_key($parametros, array_flip($campos));

    if (!empty($camposEnviados)) {
        $resultado['enviado'] = true;

        foreach ($campos as $campo) {
            if (empty($parametros[$campo])) {
                $resultado['err' . $campo] = 'No ha indicado ningún ' . str_replace('_', ' ', $campo);
            } else {
                $resultado[$campo] = $parametros[$campo]; //Si el campo no está vacío, entonces copia el valor del campo de $parametros a $resultado
            }
        }
      
        if (!empty($parametros['telefono_usuario']) && !preg_match('/(\+34|0034|34)?[ -]*(6|7)[ -]*([0-9][ -]*){8}/', $parametros['telefono_usuario'])) {
            $resultado['errtelefono'] = 'Telefono no valido';
        }
    }

    return $resultado;

  
}

function formularioRegistro($params, $accion,$tipo)
{
    $disabled = isset($params['editable']) && $params['editable'] == false ? 'readonly="readonly"' : '';
    $campos = ['nombre_usuario', 'apellidos_usuario', 'email_usuario', 'direccion_usuario', 'telefono_usuario', 'clave','foto_usuario'];

    foreach ($campos as $campo) {
        if (!isset($params[$campo])) {
            $params[$campo] = '';
            $params['err' . $campo] = '';
        }
    }

    echo "<form action='{$_SERVER['PHP_SELF']}' method='post' class='frm_registro' enctype='multipart/form-data'>";

    if ($accion=='Registrar usuario') {
      echo "<div class='frm_registro_input'><div class='label'><label for='registro_foto'>Foto:</label></div>
           <input type='file' name='foto_usuario_registro' id='foto_usuario_registro' accept='image/*' /></div>
           <div id='imagePreview'></div>";

  } else {
      mostrarImagenSeleccionada();
  }

    mostrarError($params, 'foto');
    echo "<div class='frm_registro_input'><div class='label'><label for='incidencia_titulo'>Nombre:</label></div>
    <input type='text' size=60 name='nombre_usuario' value='".$params['nombre_usuario']."' $disabled required /></div>";
      if ($params['errnombre']!='') {
          echo "<p class='error'>{$params['errnombre']}</p>";
      }   



    echo "<div class='frm_registro_input'><div class='label'><label for='registro_apellidos'>Apellidos:</label></div>
        <input type='text' size=60 name='apellidos_usuario' value='".$params['apellidos_usuario']."' $disabled/></div>";
    if ($params['errapellidos']!='') {
        echo "<p class='error'>{$params['errapellidos']}</p>";
    }
    echo "</label>";


    echo "<div class='frm_registro_input'><div class='label'><label for='registro_email'>Email:</label></div>
        <input type='email' size=60 id='email_usuario' name='email_usuario' value='".$params['email_usuario']."' $disabled/></div>
        <div class='error' id='emailinfo'></div>";
    if ($params['erremail']!='') {
        echo "<p class='error'>{$params['erremail']}</p>";
    }
    echo "</label>";


    if ($accion == 'Registrar usuario') {
        echo "<div class='frm_registro_input'><div class='label'><label for='registro_clave'>Clave:</label></div>
          <input type='password' size=30 name='clave'/>
        </div>";
      
    }
    echo "<div class='frm_registro_input'><div class='label'><label for='registro_direccion'>Dirección_postal(opcional):</label></div>
        <input type='text' size=60 name='direccion_usuario' value='".$params['direccion_usuario']."' $disabled/></div>";
    if ($params['errdireccion']!='') {
        echo "<p class='error'>{$params['errdireccion']}</p>";
    }
    echo "</label>";

    echo "<div class='frm_registro_input'><div class='label'><label for='telefono_apellidos'>Telefono (opcional):</label></div>
        <input type='text' id='tel_usuario' size=60 name='telefono_usuario' value='".$params['telefono_usuario']."' $disabled /></div>
        <div class='error' id='telinfo'></div>";
    if ($params['errtelefono']!='') {
        echo "<p class='error'>{$params['errtelefono']}</p>";
    }
    echo "</label>";

    if($tipo == 'Administrador'){
              echo"   <div class='frm_usuario_input'><div class='label'><label for='user_estado'>Estado:</label></div>
              <select name='estado_user' >
                <option value='1' ";
        if ($params['estado']=='1') {
        echo "selected";
        }

        echo "  >Activo</option>
                <option value='0'";
        if ($params['estado']=='0') {
        echo "selected";
        }

        echo" >Inactivo</option></select></div>";

        echo"   <div class='frm_usuario_input'><div class='label'><label for='user_tipo'>Tipo:</label></div>
              <select name='tipo_user' >
                <option value='Colaborador' ";
        if ($params['tipo']=='Colaborador') {
        echo "selected";
        }

        echo "  >Colaborador</option>

                <option value='Administrador'";
        if ($params['tipo']=='Administrador') {
        echo "selected";
        }

        echo" >Administrador</option></select></div>";


    }

    echo "<div class='enviar_form'>
                <input type='submit' name='accion' value='{$accion}'/>
              </div>
    </form>";

    mostrarScripts();
    
}

function mostrarImagenSeleccionada()
{
  if (isset($_FILES['foto_usuario_registro']['name']) && $_FILES['foto_usuario_registro']['name'] != '') {
    echo "<strong>Imagen seleccionada:</strong> " . $_FILES['foto_usuario_registro']['name'] . "";
    $imagen = $_FILES['foto_usuario_registro']['tmp_name'];
    $tamimagen = filesize($imagen);
    $fp = fopen($imagen, 'rb'); //abrimos el archivo binario "imagen" en modo lectura
    $contenido = fread($fp, $tamimagen); //lee el archivo hasta el tamaño de la imagen
    fclose($fp); //cerramos el archivo
    echo "<div><img class='fotoModificar' src='data:image/.jpg;base64," . base64_encode($contenido) . "'/></div>";
} else {
    echo "<strong>Ninguna imagen seleccionada:</strong> imagen por defecto asignada";
}
}

function mostrarError($params, $campo)
{
  /*
  if ($params['err' . str_replace('_usuario', '', $campo)] != '') {
    echo "<p style='color:black; class='error'>{$params['err' . str_replace('_usuario', '', $campo)]}</p>";
}*/

}

function mostrarScripts()
{
    // Código para mostrar los scripts y que sea más legible para el cliente
    echo <<<HTML

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript">
        (function(){
            function filePreview(input){
                if(input.files && input.files[0]){
                    var reader = new FileReader();

                    reader.onload = function(e){
                        $('#imagePreview').html("<img src='"+e.target.result+"' />");
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $('#foto_usuario_registro').change(function(){
                filePreview(this);
            });
        })();

        $(document).ready(function() {
            $('#email_usuario').blur(function(){
                document.getElementById('emailinfo').textContent='';
                if(! $("#email_usuario").val().match(/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$/)) {
                    document.getElementById('emailinfo').textContent='Email no válido.';
                }
            });

            $('#tel_usuario').change(function(){
                document.getElementById('telinfo').textContent='';
                if(! $("#tel_usuario").val().match(/^(\(\  +[0-9]{2}\))?\s*[0-9]{3}\s*[0-9]{6}$/)) {
                    document.getElementById('telinfo').textContent='Telefono no válido.';
                }
            });
        });
    </script>
HTML;
}





//Formulario para añadir 

function FORM_anadirIncidencia($nombre, $datos, $accion, $estados)
{   
  $nombre = isset($datos['nombre']) ? $datos['nombre'] : '';
  $descripcion = isset($datos['descripcion']) ? $datos['descripcion'] : '';
  $lugar = isset($datos['lugar']) ? $datos['lugar'] : '';
  $fecha = isset($datos['fecha']) ? $datos['fecha'] : '';
  //$imagen = isset($datos['imagen']) ? $datos['imagen'] : '';
  $fotos_descripcion = isset($datos['fotos_descripcion']) ? $datos['fotos_descripcion'] : '';
  $palabraC= isset($datos['palabraC']) ? $datos['palabraC'] : '';



    if (isset($datos['editable']) && $datos['editable']==false) {
        $disabled='readonly="readonly"';
    } else {
        $disabled='';
    }

    echo<<< HTML
    <div class='frm_incidencia'><form action='$_SERVER[PHP_SELF]' method='POST' enctype="multipart/form-data">
        <h3>$nombre</h3>


         <div class='frm_incidencia_input'><div class='label'><label for='incidencia_titulo'>Título:</label></div>
           <input type='text' name='incidencia_titulo' size=60 value= $nombre $disabled>
         </div>

         <!--<div class='frm_incidencia_input'><div class='label'><label for='incidencia_estado'>Estados:</label></div>-->
HTML;
    /*
  //Para cada estado del listado, ver si esta marcada, y si es confirmacion no permitir clickear
    foreach ($estados as $c) {
        echo "<div class='estados'><input type='checkbox' name='estados[]' value='{$c['id']}' ";
        if ($accion=='Confirmar insercion') {
            foreach ($datos['estados'] as $ids) {
                if ($ids==$c['id']) {
                    echo "checked ";
                }
            }
        }
        else if(isset($datos['estados']) && $datos['estados']!=''){ 
        foreach ($datos['estados'] as $ids) {
            if ($ids==$c['id']) {
                echo "checked ";
            }
        }
      }

        echo "/> {$c['estado']} </div>";
    }*/

    echo <<< HTML
         </div>
            <!-- Campo de entrada para Lugar -->
            <div class='frm_incidencia_input'>
            <div class='label'>
              <label for='incidencia_lugar'>Lugar:</label>
            </div>
            <input type='text' name='incidencia_lugar' size='80' value=$lugar $disabled>

          </div>

          <!-- Campo de entrada para Fecha -->
          <div class='frm_incidencia_input'>
            <div class='label'>
              <label for='incidencia_fecha'>Fecha y hora de la incidencia ocurrida:</label>
            </div>
            <input type='datetime-local' name='incidencia_fecha' value=$fecha $disabled/>
          </div>

          <div class='frm_incidencia_input'>
            <div class='label'>
              <label for='incidencia_fecha'>Palabras Clave :</label>
            </div>
            <input type='text' name='incidencia_palabraC' value="$palabraC" $disabled/>

          </div>

         <div class='frm_incidencia_textarea'><div class='label'><label for='incidencia_descripcion'>Descripción:</label></div>
           <textarea name='incidencia_descripcion' rows=5 cols=80 $disabled/>$descripcion</textarea>
         </div>

      

HTML;
     

    echo <<< HTML
       <div class='fotos_descripcion'>
         <h3>Fotos de la descripcion</h3>
HTML;

        //Si vamos a añadir la incidencias muchas imagenes 
        if($accion=='Añadir incidencia')
          echo " <label>Inserte imagenes multiple para la descripción de la incidencia <br /></label>
            <input type='file' id='fotos_descripcion' name='fotos_descripcion[]' style='display:block;' accept='image/*' multiple/>";

        //Si es confirmacion, mostraremos las imagenes subidas
        else{

          if($datos['fotos_descripcion']!=''){
            foreach($_FILES["fotos_descripcion"]['tmp_name'] as $key => $tmp_name){
              $imagen=$_FILES['fotos_descripcion']['tmp_name'][$key];
              $tamimagen=filesize($imagen);
              $fp=fopen($imagen,'rb'); //abrimos el archivo binario 
              $contenido=fread($fp,$tamimagen);//lee el archivo hasta el tamaño de la imagen
              fclose($fp); //cerramos el archivo
              echo "<img class='fotos_proc' style='display:inline;' width='100px' src='data:image/.jpg;base64,".base64_encode($contenido)."'/>";
            }

            }else {
              echo "<p>No ha subido imagenes de la descripcion</p>";
          }
        }

    echo<<<HTML
       </div>



         <input type='hidden' name='nombre' value=$nombre />
         <div class='incidencia_submit'>
           <input type='submit' name='accion' value='$accion' />
           <input type='submit' name='accion' value='Cancelar'/>
         </div>

    </form> </div>
    

    <!--scripts para ver imagenes recien subidas -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript">
          (function(){
            function filePreview(input){
              if(input.files && input.files[0]){
                var reader = new FileReader();

                reader.onload = function(e){
                  $('#imagePreview').html("<img src='"+e.target.result+"' />");
                }
                reader.readAsDataURL(input.files[0]);
              }
            }
            $('#incidencia_imagen').change(function(){
              filePreview(this);
            });
          })();
        </script>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script type="text/javascript">
            $(document).ready(function() {

              if (window.File && window.FileList && window.FileReader) {
                $("#fotos_descripcion").on("change", function(e) {
                   var files = e.target.files,
                   filesLength = files.length;
                   for (var i = 0; i < filesLength; i++) {
                     var f = files[i]
                     var fileReader = new FileReader();
                     fileReader.onload = (function(e) {
                        var file = e.target;
                        $("<span class=\"pip\">" +
                        "<img class=\"fotos_proc\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                        "</span>").insertAfter("#fotos_descripcion");

                    });
                     fileReader.readAsDataURL(f);
                   }
                });
              } else {
                alert("Your browser doesn't support to File API")
                }
            });

            </script>


HTML;
}

//Edicion del usuario
function editarUsuario($usuario, $datos, $accion, $tipo_usuario)
{
    //Depende si es para editar o para confirmar
    if (isset($datos['editable']) && $datos['editable']==false) {
        $disabled='readonly="readonly"';
    } else {
        $disabled='';
    }

    echo<<< HTML
    <div class='frm_usuario'><form action='$_SERVER[PHP_SELF]' method='POST' enctype='multipart/form-data'>
        <h3>{$datos['nombre']} {$datos['apellidos']} </h3>

        <div class='frm_incidencia_imagen'><div class='label'><label for='incidencia_img'></label></div>
HTML;

    if ($accion=='Confirmar modificacion') {
        if ($_FILES['usuario_img']['error']!=0) {
            echo "<p>Se mantiene la imagen principal anterior </p><br />";
        } else {
            echo "<p><strong>Imagen seleccionada:</strong> ".$_FILES['usuario_img']['name']."</p><br />";
            $imagen=$_FILES['usuario_img']['tmp_name'];
            $tamimagen=filesize($imagen);
            $fp=fopen($imagen,'rb'); //abrimos el archivo binario "imagen" en modo lectura
            $contenido=fread($fp,$tamimagen);//lee el archivo hasta el tamaño de la imagen
            fclose($fp); //cerramos el archivo
            echo "<div><img class='fotoModificar' src='data:image/jpeg;base64,".base64_encode($contenido)."'/></div>";
        }

        //En caso de mofiicar
    } elseif ($accion=='Modificar Datos') {
        echo "<img class='fotoModificar' src='data:image/jpeg;base64,".base64_encode($datos['fotografia'])."'/>";
        echo "<div class='frm_usuario_imagen'><label for='usuario_imagen'><strong>Inserte imagen si desea cambiarla:</strong></label>
        <input type='file' name='usuario_img' id='usuario_img' accept='image/*'/></div>
        <div id='imagePreview'>
        </div>";
    } else {
        echo "<img class='fotoModificar' src='data:image/jpeg;base64,".base64_encode($datos['fotografia'])."'/>";
    }

    echo<<<HTML
         <div class='frm_usuario_input'><div class='label'><label for='user_nombre'>Nombre:</label></div>
           <input type='text' name='user_nombre' size=60 value='{$datos["nombre"]}' $disabled/>
         </div>

         <div class='frm_usuario_input'><div class='label'><label for='user_apellidos'>Apellidos:</label></div>
           <input type='text' name='user_apellidos' size=60 value='{$datos["apellidos"]}' $disabled/>
         </div>

         <div class='frm_usuario_input'><div class='label'><label for='user_email'>Email:</label></div>
           <input type='text' name='user_email' id='user_email' size=60 value='{$datos["email"]}' $disabled/>
           <div class='error' id='emailinfo'></div>
         </div>
HTML;

    if ($accion=='Modificar Datos') { //Si vamos a modificarlo, poder modificiar clave
        echo "<div class='frm_usuario_input'><div class='label'><label for='user_clave'>Clave:</label></div>
        <input type='password' name='user_clave' size=30 />
      </div>";
    }

    echo<<<HTML


      <div class='frm_usuario_input'><div class='label'><label for='user_direccion'>Dirección (opcional):</label></div>
        <input type='text' name='user_direccion' size=60 value='{$datos["direccion"]}' $disabled/>
      </div>

      <div class='frm_usuario_input'><div class='label'><label for='user_telefono'>Telefono (opcional):</label></div>
        <input type='text' name='user_telefono' id='user_telefono' size=60 value='{$datos["telefono"]}' $disabled/>
        <div class='error' id='telinfo'></div>
      </div>
HTML;

    

    if ($disabled!='') {
        echo "<input type='hidden' name='estado_user' value='{$datos['estado']}'";
    } 
    
    echo"   <div class='frm_usuario_input'><div class='label'><label for='user_estado'>Estado:</label></div>
              <select name='estado_user' >
                <option value='1' ";
    if ($datos['estado']=='1') {
        echo "selected";
    }

    echo "  >Activo</option>
                <option value='0'";
    if ($datos['estado']=='0') {
        echo "selected";
    }

    echo" >Inactivo</option></select></div>";

    echo"   <div class='frm_usuario_input'><div class='label'><label for='user_tipo'>Tipo:</label></div>
              <select name='tipo_user' >
                <option value='Colaborador' ";
    if ($datos['tipo']=='Colaborador') {
        echo "selected";
    }

    echo "  >Colaborador</option>

                <option value='Administrador'";
    if ($datos['tipo']=='Administrador') {
        echo "selected";
    }

    echo" >Administrador</option></select></div>";


    echo "<input type='hidden' name='id' value='{$datos['id_usuario']}' />";

    echo <<< HTML
         <div class='frm_usuario_submit'>
           <input type='submit' name='accion' value='$accion' />
           <input type='submit' name='accion' value='Cancelar'/>
         </div>

    </form> </div>

    <!--scripts para ver imagenes recien subidas y validacion de email y telefono en el cliente-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript">
          (function(){
            function filePreview(input){
              if(input.files && input.files[0]){
                var reader = new FileReader();

                reader.onload = function(e){
                  $('#imagePreview').html("<label>Nueva imagen:</label>' />");
                  $('#imagePreview').html("<img src='"+e.target.result+"' />");
                }
                reader.readAsDataURL(input.files[0]);
              }
            }
            $('#usuario_img').change(function(){
              filePreview(this);
            });
          })();
          </script>


          <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
          <script type="text/javascript">
            $(document).ready(function() {
              $('#usuario_email').blur(function(){
                document.getElementById('emailinfo').textContent='';
                  if(! $("#usuario_email").val().match(/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$/)) {
                      document.getElementById('emailinfo').textContent='Email no válido.';
                  }
              });
            });
          </script>

          <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
          <script type="text/javascript">
            $(document).ready(function() {
              $('#usuario_telefono').change(function(){
                  document.getElementById('telinfo').textContent='';
                  if(! $("#usuario_telefono").val().match(/^(\(\  +[0-9]{2}\))?\s*[0-9]{3}\s*[0-9]{6}$/)) {
                      document.getElementById('telinfo').textContent='Telefono no válido.';
                  }
              });
            });
          </script>

HTML;
}
//Ver listado de usuarios
function Ver_listadoUsuarios($datos)
{
  echo "<h2>Listado de usuarios</h2>
    <form action='$_SERVER[PHP_SELF]' method='POST'>
        <input type='submit' name='accion' value='Añadir Usuario' id='Añadir_Usuario'/>
    </form>";


    //informacion de usuarios y que pueden editar, borrar ....
    foreach ($datos as $v) {
        echo "<div class='listado'>
        <table>
        <tr>
        <td class='imagen'>
          <img class='fotoUsuario' src='data:image/jpeg;base64,".base64_encode($v['fotografia'])."' width='100px'/>
        </td>
        <td class='usuario_nombre'>
        <div class='info_line'>
        <p>Nombre: ".htmlentities($v['nombre'])." ".htmlentities($v['apellidos'])."</p>
        <p>Email: ".htmlentities($v['email'])."</p>
        </div>
        <div class='info_line'>
        <p>Direccion: ".htmlentities($v['direccion_postal'])."</p>
        <p>Telefono: ".htmlentities($v['telefono'])."</p>
        </div>
        <div class='info_line'>
        <p>Rol: ".htmlentities($v['tipo'])."</p>";
        if ($v['estado']=='1') {
            echo "<p>Estado: Activo</p></div></td>";
        } else {
            echo "<p>Estado: Inactivo </p></div></td>";
        }

        echo "<td class='rec_botones'><form action='{$_SERVER['PHP_SELF']}' method='POST'>

                <input type='hidden' name='id' value='{$v['id_usuario']}' />
          
            <button type='submit' name='accion' value='Editar' id='boton_editar' >
            </button>";
            //No poder borrar el administrador 
            if($_SESSION['id_usuario']!=$v['id_usuario'])
              echo "<button type='submit' name='accion' value='Borrar' id='boton_borrar' >
              </button>";






        echo "</form></td>
              </tr>
              </table>
              </div>";
    }
}
function AnadirUsuario($accion)
{
    echo<<< HTML
    <div class='frm_usuario'><form action='$_SERVER[PHP_SELF]' method='POST' enctype='multipart/form-data'>
        <h3>Registro de Nuevo Usuario</h3>

        <div class='frm_registro_input'><div class='label'><label for='registro_foto'>Foto:</label></div>
           <input type='file' name='foto_usuario_registro' id='foto_usuario_registro' accept='image/*' /></div>
           <div id='imagePreview'></div>
        


        <div class='frm_usuario_input'><div class='label'><label for='user_nombre'>Nombre:</label></div>
           <input type='text' name='user_nombre' size=60 />
        </div>

        <div class='frm_usuario_input'><div class='label'><label for='user_apellidos'>Apellidos:</label></div>
           <input type='text' name='user_apellidos' size=60 />
        </div>

        <div class='frm_usuario_input'><div class='label'><label for='user_email'>Email:</label></div>
           <input type='text' name='user_email' id='user_email' size=60 />
           <div class='error' id='emailinfo'></div>
        </div>

        <div class='frm_usuario_input'><div class='label'><label for='user_clave'>Clave:</label></div>
        <input type='password' name='user_clave' size=30 />
      </div>

      <div class='frm_usuario_input'><div class='label'><label for='user_direccion'>Dirección (opcional):</label></div>
        <input type='text' name='user_direccion' size=60 />
      </div>

      <div class='frm_usuario_input'><div class='label'><label for='user_telefono'>Telefono (opcional):</label></div>
        <input type='text' name='user_telefono' id='user_telefono' size=60 />
        <div class='error' id='telinfo'></div>
      </div>
      <div class='frm_usuario_input'>
            <div class='label'><label for='estado_user'>Estado:</label></div>
            <select name='estado_user'>
                <option value='1'>Activo</option>
                <option value='0'>Inactivo</option>
            </select>
        </div>

        <div class='frm_usuario_input'>
            <div class='label'><label for='tipo_user'>Tipo:</label></div>
            <select name='tipo_user'>
                <option value='Colaborador'>Colaborador</option>
                <option value='Administrador'>Administrador</option>
            </select>
        </div>

      <div class='frm_usuario_submit'>
        <input type='submit' name='accion' value='$accion' />
        <input type='submit' name='accion' value='Cancelar'/>
      </div>
    </form> </div>

    <!--scripts para validacion de email y telefono en el cliente-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#usuario_email').blur(function(){
          document.getElementById('emailinfo').textContent='';
            if(! $("#usuario_email").val().match(/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$/)) {
                document.getElementById('emailinfo').textContent='Email no válido.';
            }
        });
      });
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#usuario_telefono').change(function(){
            document.getElementById('telinfo').textContent='';
            if(! $("#usuario_telefono").val().match(/^(\(\  +[0-9]{2}\))?\s*[0-9]{3}\s*[0-9]{6}$/)) {
                document.getElementById('telinfo').textContent='Telefono no válido.';
            }
        });
      });
    </script>
HTML;
}
//Formulario para la edicion de la incidencia
function FORMU_editarIncidencia($nombre, $datos, $accion, $estados)
{
    //Dependiendo si es edicion o confirmacion
    if (isset($datos['editable']) && $datos['editable']==false) {
        $disabled='readonly="readonly"';
    } else {
        $disabled='';
    }

   
    echo<<< HTML
    <div class='frm_incidencia'><form action='$_SERVER[PHP_SELF]' method='POST' enctype="multipart/form-data">
        <h3>$nombre</h3>


         <div class='frm_incidencia_input'><div class='label'><label for='incidencia_titulo'>Título:</label></div>
           <input type='text' name='incidencia_titulo' size=60 value='{$datos["texto"]}'$disabled>
         </div>

         <div class='frm_incidencia_input'><div class='label'><label for='incidencia_estado'>Estados:</label></div>
HTML;

  //Para el listado de estados, vemos cuales estan marcadas y si es confirmacion, no permitir clickear
  foreach ($estados as $c) {
    echo "<div class='estados'><input type='checkbox' name='incidencia_estados[]' value='{$c['id']}' ";
    if ($accion=='Confirmar Borrado' || $accion=='Confirmar modificacion') {
        echo "onclick='return false;'";
        if (isset($datos['estados']) && is_array($datos['estados'])) {
            foreach ($datos['estados'] as $ids) {
                if ($ids==$c['id']) {
                    echo "checked ";
                }
            }
        }
    }
    else { //Hacer checkbox sticky
        if (isset($datos['estados']) && is_array($datos['estados'])) {
            foreach ($datos['estados'] as $ids) {
                if ($ids==$c['id']) {
                    echo "checked  ";
                }
            }
        }
    }
    echo "/> {$c['estado']} </div>";
}
    echo <<< HTML
    
    </div>
       <!-- Campo de entrada para Lugar -->
       <div class='frm_incidencia_input'>
       <div class='label'>
         <label for='incidencia_lugar'>Lugar:</label>
       </div>
       <input type='text' name='incidencia_lugar' size='80' value='{$datos["lugar"]}' $disabled>

     </div>

     <!-- Campo de entrada para Fecha -->
     <div class='frm_incidencia_input'>
       <div class='label'>
         <label for='incidencia_fecha'>Fecha y hora de la incidencia ocurrida:</label>
       </div>
       <input type='datetime-local' name='incidencia_fecha' value='{$datos["fecha"]}' $disabled/>
     </div>

     <div class='frm_incidencia_input'>
       <div class='label'>
         <label for='incidencia_palabraC'>Palabras Clave :</label>
       </div>
       <input type='text' name='incidencia_palabraC' value='{$datos["palabra_clave"]}' $disabled/>

     </div>

     <div class='frm_incidencia_textarea'><div class='label'><label for='incidencia_descripcion'>Descripción:</label></div>
           <textarea name='incidencia_descripcion' rows=5 cols=80 $disabled/>{$datos["descripcion"]}</textarea>
         </div>

 

HTML;


    //Al pulsar el boton de editar, veremos la imagen y podremos reemplazarla
    if ($accion=='Modificar Datos') {
      if (isset($datos['fotografia']) && is_array($datos['fotografia']) && !empty($datos['fotografia'])) {
        foreach ($datos['fotografia'] as $foto) {
            echo "<img class='fotoModificar' src='data:image/.jpg;base64,".base64_encode($foto)."'/>";
        }
    } else {
        echo "No hay fotos para mostrar.";
    }
    
     
    
      //  echo "<img class='fotoModificar' src='data:image/.jpg;base64,".base64_encode($datos['fotografia'])."'/>";
        echo "<div class='frm_incidencia_imagen'><label for='incidencia_imagen'><strong>Inserte imagen si desea cambiarla:</strong></label>
         <input type='file' name='incidencia_img' id='incidencia_img'  accept='image/*'/></div>
         <div id='imagePreview'> </div>";

    //Al confirmar la modificacion de la incidencia, veremos la imagen subida
    } elseif ($accion=='Confirmar modificacion') {
        if ($_FILES['incidencia_img']['error']!=0) {
            echo "<p>Se mantiene la imagen principal anterior </p><br />";
        } else {
            echo "<p><strong>Imagen seleccionada:</strong> ".$_FILES['incidencia_img']['name']."</p><br />";
            $imagen=$_FILES['incidencia_img']['tmp_name'];
            $tamimagen=filesize($imagen);
            $fp=fopen($imagen,'rb'); //abrimos el archivo binario "imagen" en modo lectura
            $contenido=fread($fp,$tamimagen);//lee el archivo hasta el tamaño de la imagen
            fclose($fp); //cerramos el archivo
            echo "<div><img class='fotoModificar' src='data:image/.jpg;base64,".base64_encode($contenido)."'/></div>";
        }

      //En otro caso como borrar, mostrar solo la imagen
    } else {
      if (is_array($datos['fotografia'])) {
        foreach ($datos['fotografia'] as $foto) {
            echo "<img class='fotoModificar' src='data:image/.jpg;base64,".base64_encode($foto)."'/>";
        }
    } else {
        echo "<img class='fotoModificar' src='data:image/.jpg;base64,".base64_encode($datos['fotografia'])."'/>";
    }
    
    }

    echo "<div class='fotos_descripcion'>";

    if($accion=='Modificar Datos'){

      //Si hay fotos antiguas
      if($datos['fotografia']!=''){
        echo "<h3>Fotos del de la descripcion antiguas</h3>";
        echo "<label>Desmarque las imagenes que quiera borrar</label><br />";
        $i=0;
        //Para cada foto, la creamos en pequeña, la mostramos y añadimos checkbox para desmarcar la que no queremos mantener, almacenando el indice
        foreach($datos['fotografia'] as $img){
          $image = imagecreatefromstring($img);
          $image = imagescale($image, 250);
          ob_start();
          imagejpeg($image);
          $contents = ob_get_contents();
          ob_end_clean();
          echo "<div class='fotos_editar' style='display:inline-block;'>
          <img src='data:image/jpeg;base64,".base64_encode($contents)."' style='margin:10px;'/><br />
          <input type='checkbox' name='fotos_antiguas_numero[]' checked value='{$i}'  />
          </div>";
          $i++;
          imagedestroy($image);

        }
      }
      echo "</div>

      <div class='fotos_descripcion'>
        <h3>Nuevas fotos de la descripcion</h3>
        <input type='file' id='fotos_descripcion_edicion' name='fotos_descripcion_edicion[]' style='display:block;' accept='image/*' multiple/>
         </div>";

    //Si es confirmacion de edicion, veremos cada una de las fotos nuevas subidas
    } elseif ($accion=='Confirmar modificacion') {
      if(isset($_FILES['fotos_descripcion_edicion']['tmp_name'][0]) && !empty($_FILES['fotos_descripcion_edicion']['tmp_name'][0])){
        echo "<h3>Nuevas fotos seleccionadas</h3>
        <label>Borraremos las fotos que no ha seleccionado.<br />Estas son las nuevas imagenes añadidas<br /></label>";
        foreach($_FILES["fotos_descripcion_edicion"]['tmp_name'] as $key => $tmp_name){
          $imagen=$_FILES['fotos_descripcion_edicion']['tmp_name'][$key];
          $tamimagen=filesize($imagen);
          $fp=fopen($imagen,'rb'); //abrimos el archivo binario "imagen" en modo lectura
          $contenido=fread($fp,$tamimagen);//lee el archivo hasta el tamaño de la imagen
          fclose($fp); //cerramos el archivo
          echo "<img class='fotos_proc' style='display:inline;' width='100px' src='data:image/.jpg;base64,".base64_encode($contenido)."'/>";
        }

        }else {
          echo "<p>No ha subido imagenes de la descripcion</p><p>Puede subirlas editando la incidencia</p>";
      }
    }



    echo <<< HTML
        

         <input type='hidden' name='id_incidencias' value='{$datos["id_incidencias"]}' />
         <div class='frm_incidencia_submit'>
           <input type='submit' name='accion' value='$accion' />
           <input type='submit' name='accion' value='Cancelar'/>
         </div>

    </form> </div>
    </div>

        <!--Scripts para ver imagenes recien subidas -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script type="text/javascript">
              (function(){
                function filePreview(input){
                  if(input.files && input.files[0]){
                    var reader = new FileReader();

                    reader.onload = function(e){
                      $('#imagePreview').html("<img src='"+e.target.result+"' />");
                    }
                    reader.readAsDataURL(input.files[0]);
                  }
                }
                $('#incidencia_img').change(function(){
                  filePreview(this);
                });
              })();


            </script>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script type="text/javascript">
                $(document).ready(function() {

                  if (window.File && window.FileList && window.FileReader) {
                    $("#fotos_procedimiento_edicion").on("change", function(e) {
                       var files = e.target.files,
                       filesLength = files.length;
                       for (var i = 0; i < filesLength; i++) {
                         var f = files[i]
                         var fileReader = new FileReader();
                         fileReader.onload = (function(e) {
                            var file = e.target;
                            $("<span class=\"pip\">" +
                            "<img class=\"fotos_proc\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                            "</span>").insertAfter("#fotos_procedimiento_edicion");

                        });
                         fileReader.readAsDataURL(f);
                       }
                    });
                  } else {
                    alert("Your browser doesn't support to File API")
                    }
                });

                </script>

HTML;
}











?>
<style>
    .frm_registro label {
        font-weight: bold;
        color: black;
    }
    .error{
      color: black;
      opacity: 1;
      display: block;
  }
 

</style>