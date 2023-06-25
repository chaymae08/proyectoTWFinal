<?php


function logg(){
    $db = DB_conexion();

    if (!isset($_GET['pagina'])) {
        $pagina = 1;
    } else {
        $pagina = $_GET['pagina'];
    }

    $num_por_pagina = 20; 
    $start_from = ($pagina-1) * $num_por_pagina;

    $sql = "SELECT * FROM log ORDER BY fecha DESC LIMIT $start_from, $num_por_pagina";
    $salida = mysqli_query($db, $sql);

    echo <<<HTML
    <table>
    HTML;

    foreach($salida as $v){
        echo "<tr>";
        echo "<td >{$v['fecha']}</td>";
        echo "<td>{$v['usuario']}</td>";
        echo "<td>{$v['descripcion']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    $sql = "SELECT COUNT(*) FROM log";
    $rs_result = mysqli_query($db,$sql);
    $row = mysqli_fetch_row($rs_result);
    $total_records = $row[0];
    $total_pages = ceil($total_records / $num_por_pagina);

    for ($i=1; $i<=$total_pages; $i++) {
        echo "<a href='log.php?pagina=".$i."'>".$i."</a> ";
    }
  
}
?>