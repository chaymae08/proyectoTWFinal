<?php
function FORM_buscarIncidencia($estado)
{
  $id='1';
    echo "<div class='frm_incidencia_buscar'> <form action='$_SERVER[PHP_SELF]' method='POST'> 
    
    <h2>Criterios de búsqueda</h2>
    <h3 class='tit_incidencias'>Incidencias que contengan</h3>

        <div class='frm_incidencia_buscar_input'> <label for='incidencia_texto'>Texto de búsqueda:</label>
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

            //Si eres administrador, podras borra comentario
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
        echo "<form action='$_SERVER[PHP_SELF]' method='POST'>
        <input type='hidden' name='autor_valoracion' value='".$_SESSION['id_usuario']."' />
        <input type='hidden' name='id_incidencia' value='{$v['id_incidencias']}' />
        <button type='submit' name='accion' value='Añadir_valoracion_pos' id='boton_anadir_valoracion_pos'>
        </button>
      </form>";

echo "<form action='$_SERVER[PHP_SELF]' method='POST'>
        <input type='hidden' name='autor_valoracion' value='".$_SESSION['id_usuario']."' />
        <input type='hidden' name='id_incidencia' value='{$v['id_incidencias']}' />
        <button type='submit' name='accion' value='Añadir_valoracion_neg' id='boton_anadir_valoracion_neg'>
        </button>
      </form>";
   }
    echo "</div>";
  } else {
    // Usuario visitante, verificar si ha realizado una valoración previa mediante cookies
    $cookie_name = 'valoracion_incidencia_'.$v['id_incidencias'];
    
    if (isset($_COOKIE[$cookie_name])) {
      echo "<p>Ya has valorado esta incidencia como ".$_COOKIE[$cookie_name]."</p>";
    } else {
      echo "<div class='botones_valoracion'>";
   
      echo "<form action='$_SERVER[PHP_SELF]' method='POST'>
      <input type='hidden' name='autor_valoracion' value='".$_SESSION['id_usuario']."' />
      <input type='hidden' name='id_incidencia' value='{$v['id_incidencias']}' />
      <button type='submit' name='accion' value='Añadir_valoracion_pos' id='boton_anadir_valoracion_pos'>
      </button>
    </form>";

   
     echo "<form action='$_SERVER[PHP_SELF]' method='POST'>
     <input type='hidden' name='autor_valoracion' value='".$_SESSION['id_usuario']."' />
     <input type='hidden' name='id_incidencia' value='{$v['id_incidencias']}' />
     <button type='submit' name='accion' value='Añadir_valoracion_neg' id='boton_anadir_valoracion_neg'>
     </button>
   </form>";
      echo "</div>";
    }
  
      // Establecer la cookie para evitar múltiples valoraciones del visitante
      $cookie_value = 'visitante';
      setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // La cookie expirará en 30 días (puedes ajustar el tiempo según tus necesidades)
    }
  
  
   
      echo "  <div class='pie_pagina'><form action='$_SERVER[PHP_SELF]' method='POST'>
            <input type='hidden' name='id_incidencia' value='{$v['id_incidencias']}' />";
    //Si estas logueado, mostrar botones editar y borrar
    if (isset($_SESSION['id_usuario'])) {
        echo "<button type='submit' name='accion' value='Editar' id='boton_editar'>
                
              </button>
              <button type='submit' name='accion' value='Borrar' id='boton_borrar'>
                 
              </button>";
    }

    echo<<< HTML
       <input type='submit' name='accion' value='Cancelar' id='boton_cancelar' />
     
    </button>

        </form></div>

    </div>
  
    


    


HTML;
}
//Obtener los parametros del regsitro
function obtenerParametrosRegistro($parametros)
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
        'nombre_usuario' => '',
        'apellidos_usuario' => '',
        'email_usuario' => '',
        'clave' => '',
        'direccion_usuario' => '',
        'telefono_usuario' => '',
        'foto_usuario' => ''
    ];

    $campos = ['nombre_usuario', 'apellidos_usuario', 'email_usuario', 'direccion_usuario', 'telefono_usuario', 'clave','foto_usuario','clave'];
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

function formularioRegistro($params, $accion)
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
  if ($params['err' . str_replace('_usuario', '', $campo)] != '') {
    echo "<p style='color:black; class='error'>{$params['err' . str_replace('_usuario', '', $campo)]}</p>";
}

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
function registrarUsuario($params,$accion,$tipo) {
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
 
     // Preparar la consulta SQL
     $sql = "INSERT INTO usuarios (nombre, apellidos, email, direccion_postal,telefono, clave, fotografia,tipo) VALUES (?, ?, ?, ?, ?, ?, ?,?)";
 
     // Preparar la declaración
     $stmt = $conn->prepare($sql);
 
     // Obtener la imagen en formato base64
     $image_base64 = '';
     if (isset($_FILES['foto_usuario_registro'])) {
         $image = file_get_contents($_FILES['foto_usuario_registro']['tmp_name']);
         $image_base64 = base64_encode($image);
     } else {
      // Si no se proporciona una foto, establecer una por defecto
      $default_image = file_get_contents('../vista/fotos/foto_defecto.png');
      $image_base64 = base64_encode($default_image);
  }
 
     // Vincular los parámetros
     $stmt->bind_param("ssssssss", $params['nombre_usuario'], $params['apellidos_usuario'], $params['email_usuario'], $params['direccion_usuario'], $params['telefono_usuario'], $params['clave'], $image_base64,$tipo);
 
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