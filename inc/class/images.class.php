<?php

require_once "connection.class.php";

/**
 * Class Images
 */
class Images {
    public $num_images_in_storage;
    private $connectDB;

    function Images() {
        $this->connectDB = new Connection('pictunex');
        $_result = $this->connectDB->prepare("SELECT id, COUNT(id) AS NUM_IMAGES FROM images");
        if($_result->execute())
            $this->num_images_in_storage = $_result->fetch(PDO::FETCH_ASSOC)['NUM_IMAGES'];
    }

}


?>