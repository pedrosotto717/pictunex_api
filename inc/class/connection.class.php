<?php

/**
 * Class Connection
 * * Connection With DB Pictunex
 */
class Connection {
    private $host;
    private $user;
    private $nameDB;
    private $passBD;
    private $DB;

    function Connection ($nameDB){
        $this->host = 'localhost';
        $this->user = 'root';
        $this->passDB = '';
        $this->nameDB = $nameDB;

       try {

        //CREATING THE CONNECTION
        $this->DB = new PDO('mysql:host='. $this->host .';dbname='. $this->nameDB,
                            $this->user,$this->passDB);

        $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->DB->exec("SET CHARACTER SET utf8");

       } catch (Exception $error) {
        die($error->GetMessage());       
       }
    }

    public function getDB(){
        return $this->DB;
    }

    public function queryAll($table) {
        $_query = "SELECT * FROM $table WHERE 1"; //declaro la Intruccion valida SQL
        $result = $this->DB->prepare($_query);  // almaceno en una variable la consulta preparada
        if($result->execute()) //without parameters
            return $result->fetchAll(PDO::FETCH_ASSOC); // retorna todos los resultados en un array asosiativo
        else return false;
            // return $result->rowCount(); // equivalente a num_rows
    }

    public function queryByID($table,$ID) {
        $_query = "SELECT * FROM $table WHERE ID = :id"; //declaro la Intruccion valida SQL
        $result = $this->DB->prepare($_query);  // almaceno en una variable la consulta preparada
        if($result->execute([":id" => $ID])) //without parameters
            return $result->fetch(PDO::FETCH_ASSOC); // retorna todos los resultados en un array asosiativo
        else return false;
            // return $result->rowCount(); // equivalente a num_rows
    }
}

?>