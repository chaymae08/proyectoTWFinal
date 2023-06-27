<?php

require_once('../modelo/dbparametros.php');


/*
$backup_file = 'backup.sql';

// Comando para exportar la base de datos
$command = "mysqldump --user=" . DB_USUARIO . " --password=" . DB_CLAVE . " --host=" . DB_SERVIDOR . " " . DB_NOMBRE . " > {$backup_file}";

// Ejecutar el comando
system($command, $output);

// Comprobar si se ha producido un error
if($output == 0) {
    $_SESSION['message'] = 'Exportación realizada con éxito.';
    DB_log(null, date("Y-m-d H:i:s"), "INFO: Se ha importado la BBDD a un fichero externo");
} else {
    echo "'Ha ocurrido un error durante la exportación.";
}*/
/*
if (!isset($_SESSION['admin'])) {
    header("LOCATION: /~aulas/proyecto/index.php");
}*/
/*
$conexion = new Conexion();
$conexion->conectar();

$backup_file = 'backup.sql';

// Comando para exportar la base de datos
$command = "mysqldump --user=" . DB_USUARIO . " --password=" . DB_CLAVE . " --host=" . DB_SERVIDOR . " " . DB_NOMBRE . " > {$backup_file}";

// Ejecutar el comando
system($command, $output);

// Comprobar si se ha producido un error
if($output == 0) {
    $_SESSION['message'] = 'Exportación realizada con éxito.';
    $conexion->addLog(null, date("Y-m-d H:i:s"), "INFO: Se ha importado la BBDD a un fichero externo");
} else {
    echo " Ha ocurrido un error durante la exportación.";
}

$conexion->desconectar();

header("Location: ../index.php");
*/

    $backupFile = 'seguridad.sql';

    // Comando para generar la copia de seguridad
    $command = "mysqldump --user=" . DB_USUARIO . " --password=" . DB_CLAVE . " --host=" . DB_SERVIDOR . " " . DB_NOMBRE . " > {$backupFile}";

    // Ejecutar el comando
    system($command, $output);

    if($output == 0) {
        echo 'Exportación realizada con éxito.';
    } else {
        echo 'Error';
    }

    

//PRUEBA
/*

$db=DB_conexion();

// Obtener listado de tablas
$tablas = array();
$result = mysqli_query($db, "SHOW TABLES");

while ($row = mysqli_fetch_row($result)) {
    $tablas[] = $row[0];
}

$salida = "";
foreach ($tablas as $table) {
    $result = mysqli_query($db, "SELECT * FROM $table");
    $columnCount = mysqli_num_fields($result);

    // Salvar cada tabla
    while ($row = mysqli_fetch_row($result)) {
        $salida .= "INSERT INTO $table VALUES(";
        for ($j = 0; $j < $columnCount; $j ++) {
            if (isset($row[$j])) {
                $salida .= "'" . mysqli_real_escape_string($db,$row[$j]) . "'";
            } else {
                $salida .= "''";
            }
            if ($j < ($columnCount - 1)) {
                $salida .= ",";
            }
        }
        $salida .= ");\n";
    }
    $salida .= "\n";
}


if(!empty($salida))
{
    // Guardar la copia de seguridad
    $backup_file_name = 'chaymae082223' . '_backup_' . time() . '.sql';
    $fileHandler = fopen($backup_file_name, 'w+');
    $number_of_lines = fwrite($fileHandler, $salida);
    fclose($fileHandler);


    // Descargar la copia de seguridad en el navegador
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file_name));
    
    flush();
    readfile($backup_file_name);
    exec('rm ' . $backup_file_name);
}

  DB_log($db,$_SESSION['nombre_usuario'],"Realiza una copia de seguridad de la base de datos");
*/

?>
