<?php
$clave = '44';
$hash = '$2y$10$C6sXJiUo0Vu45cb7mA8t7etD9UpvqKAXF8fx9XvVwfG09kkMBzehG';

if (password_verify($clave, $hash)) {
    echo 'La contraseña es válida!';
} else {
    echo 'La contraseña es inválida.';
}


?>
