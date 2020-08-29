<?php

$dir = preg_replace("/[\\\]/", "/", __DIR__);
$dirHelpers = preg_replace("/class/", "helpers.php", $dir);

require_once "connection.class.php";
require_once $dirHelpers;

/**
 * Class Images
 */
class Images {
    public $num_images_in_storage;
    private $connect;
    private $pathIMG;
    private $urlPublicImg;
    private $categories;

    function Images($httpReferer) {
        //Connection with DataBase using Class "Connection"
        $this->connect = new Connection('pictunex');

        $_result = $this->connect->getDB()->prepare("SELECT id, COUNT(id) AS NUM_IMAGES FROM images");

        if($_result->execute())
            $this->num_images_in_storage = $_result->fetch(PDO::FETCH_ASSOC)['NUM_IMAGES'];
        else die();

        //Reset path to storage imgs
        $this->pathIMG = preg_replace("/(api\S)+(inc\S)+(class)/i", "public\\img\\", __DIR__);

        //Url Public to get access __defined__ http_host
        $this->urlPublicImg = preg_replace("/api\/index.*/", "", php_self) . "public/img/";

        //Categories
        $this->categories =  ["categories" => [
            "animales",
            "paisajes",
            "naturaleza",
            "fantasia",
            "tecnologia",
            "ciencia",
            "moda",
            "arquitectura",
            "industria "]];
        
    }


    public function getAll() {
        $arrImages = $this->connect->queryAll('images');

        if ($arrImages!=false) {
            for ($h=0; $h < count($arrImages); $h++){
                self::constructUrl($arrImages[$h]["src"]);
            }

            return $arrImages;
        }else return false;

    }


    public function getByID($id) {
        $image = $this->connect->queryByID('images',$id);
        self::constructUrl($image["src"]);
        return $image;
    }


    public function getCategories() {
        return $this->categories;
    }


    public function getByCategory($c) {

        if(in_array($c,$this->categories["categories"])){
            $_result = $this->connect->getDB()->prepare("SELECT * FROM `images` WHERE categories LIKE '%$c%'");
            if($_result->execute()){

                $arrImages = $_result->fetchAll(PDO::FETCH_ASSOC);

                if ($arrImages!=false) {
                    for ($h=0; $h < count($arrImages); $h++){
                        self::constructUrl($arrImages[$h]["src"]);
                    }

                    return $arrImages;
                }else return false;

            }else return false;
        }else return false;
    }


    public function search($key) {
        escSpecialChar($key);

        if(preg_match('/[a-z]/', $key)){

            $_query = "SELECT * FROM `images` WHERE MATCH(`name`,`keywords`,`categories`) AGAINST('$key' IN BOOLEAN MODE)";

            $_result = $this->connect->getDB()->prepare($_query);

            if($_result->execute()){
                $arrImages = $_result->fetchAll(PDO::FETCH_ASSOC);

                if ($arrImages!=false) {
                    for ($h=0; $h < count($arrImages); $h++){
                        self::constructUrl($arrImages[$h]["src"]);
                    }

                    return $arrImages;
                }else return false;

            }
            else return false;

        }else return false;
    }


    public function insertImage($name, $keyW, $category, $imgObject) {

        escSpecialChar($name);
        escSpecialChar($keyW);
        escSpecialChar($category);

        $srcFinal = self::saveImage($imgObject,$name);

        $_query = "INSERT INTO `images`
                (`id`, `name`, `keywords`, `categories`, `src`, `CREATION_DATE`) 
                VALUES (NULL, :name, :keywords, :categories, :src, NOW())";

        $_result = $this->connect->getDB()->prepare($_query);

        if($srcFinal!=false){

            if($_result->execute([":name" => $name,
                              ":keywords" => $keyW,
                              ":categories" => $category,
                              ":src" => $srcFinal])){
                return true;
            } # end if DB->execute()
            else return false;

        } # end $srcFinal
        return null;
        
    }


    public function UpdateImage($id,$datImage) {

        $_query = "UPDATE images 
                  SET ID = NULL,
                      NAME = :name,
                      KEYWORDS = :keywords,
                      CATEGORIES = :categories,
                      SRC = :src,
                      CREATION_DATE = NOW()";
    }


    public function saveImage($imgObj, $name) {
        $name = preg_replace("/[\s]/", "_", $name);
        $name = $name . "_" . time() . "-pictunex" . "." . preg_replace("/(image)+\//", "", $imgObj["type"]);
        $srcFinal = $this->pathIMG . $name;

        //Check File Type == Image
        if($imgObj["type"]=="image/jpg" || $imgObj["type"]=="image/jpeg" || $imgObj["type"]=="image/png" ){

            if(move_uploaded_file($imgObj["tmp_name"], $srcFinal))
                return $this->urlPublicImg . $name;
            return false;
        }else return false;
    
    }


    public function constructUrl(&$urlImages)  {
        $urlImages = protocol . "://" . http_host . $urlImages;
    }



    // $_SERVER["HTTP_REFERER"]

}#end Class Images


?>