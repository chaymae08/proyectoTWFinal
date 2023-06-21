<?php
session_start();
require_once './vista/codigo_html.php';
require_once './modelo/db.php';


inicio();
menu();

echo <<< HTML

<main>

  <div class="cajaPrincipal">

  <h1>Bienvenido a tu página de incidencias de vecinos</h1>
  <p>En esta página podrás compartir y comentar diferentes situaciones e incidencias que ocurran en tu barrio.</p>
  <p>Si tienes alguna queja, sugerencia o simplemente quieres compartir una experiencia relacionada con la convivencia vecinal, este es el lugar adecuado.</p>
  <p>Puedes publicar tus propias incidencias y también comentar las de otros vecinos para brindar apoyo y consejos.</p>
  <p>Juntos, podemos trabajar para mejorar la convivencia y encontrar soluciones a los problemas que surjan en nuestra comunidad.</p>
  <p>No olvides utilizar el espacio de contacto para cualquier consulta o sugerencia adicional que desees compartir.</p>
  <p><strong>¡GRACIAS POR VISITAR NUESTRA PÁGINA!</strong></p>


<img src="/~aulas/proyecto_final/vista/fotos/inicio1.png" alt="Foto_inicio" class="fotoInicio"/>


</div>


HTML;
barraHTML();

?>