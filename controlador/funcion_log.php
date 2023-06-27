<?php

function logg(){
    $db = DB_conexion();

    $sql = "SELECT * FROM log ORDER BY fecha DESC";
    $salida = mysqli_query($db, $sql);

    echo <<<HTML
    <table>
    HTML;

    while($v = mysqli_fetch_assoc($salida)){
        echo "<tr>";
        echo "<td>{$v['fecha']}</td>";
        echo "<td>{$v['usuario']}</td>";
        echo "<td>{$v['descripcion']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>
