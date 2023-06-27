<?php
DEFINE('DB_SERVIDOR','localhost');
DEFINE('DB_USUARIO','root');  
DEFINE('DB_CLAVE','aulas'); 
DEFINE('DB_NOMBRE','chaymae082223');

class Conexion {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    private static $intance = null;

    

    public function __construct() {
        
        
        $this->servername = DB_SERVIDOR;
        $this->username = DB_USUARIO;
        $this->password = DB_CLAVE;
        $this->dbname = DB_NOMBRE;
    }

    public function conectar() {
        $this->conn = new mysqli($this->servername,$this->username,$this->password,$this->dbname);

        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function desconectar() {
        $this->conn->close();
    }
}
?>