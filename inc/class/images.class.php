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

    } # end getAll()


    public function getByID($id) {
        $id = escSpecialChar($id);
        $image = $this->connect->queryByID('images',$id);

        if(isset($image["src"])){
            self::constructUrl($image["src"]);
            return $image;
        }else return false;
    } # end getByID()


    public function getCategories() {
        return $this->categories;
    } # end getCategories()


    public function getByCategory($c) {
        $c = escSpecialChar($c);

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
    } # end getByCategory()


    public function search($key) {
        $key = escSpecialChar($key);

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
    } # end search()


    public function insertImage($name, $keyW, $category, $imgObject) {

        $name = escSpecialChar($name);
        $keyW = escSpecialChar($keyW);
        $category = escSpecialChar($category);

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
                if($_result->rowCount()==1)
                    return true;
                else return false;
            } # end if DB->execute()
            else return false;

        } # end $srcFinal
        return null;
        
    } # end insertImage()


    public function updateImage($id, $name, $keyW, $category, $imgObject) {
        $name = escSpecialChar($name);
        $keyW = escSpecialChar($keyW);
        $category = escSpecialChar($category);

        $id = escSpecialChar($id);

        $_img = $this->connect->queryByID('images',$id);

        if($_img!=false){ # if exist 

            if(self::destroyImages($_img["src"])){
                $srcFinal = self::saveImage($imgObject,$name);

                $_query = "UPDATE images 
                          SET NAME = :name,
                              KEYWORDS = :keywords,
                              CATEGORIES = :categories,
                              SRC = :src 
                              WHERE ID = $id";

                $_result = $this->connect->getDB()->prepare($_query);

                if($srcFinal!=false){
                    if($_result->execute([":name" => $name,
                                      ":keywords" => $keyW,
                                      ":categories" => $category,
                                      ":src" => $srcFinal])){
                        if($_result->rowCount()==1)
                            return true;
                        else return false;
                    } # end if DB->execute()
                    else return false;

                } # end $srcFinal
                return null;
            }
            else return false;
        }else return false;        
    } # end updateImage()


    public function deleteImage($id){

        $id = escSpecialChar($id);

        $_img = $this->connect->queryByID('images',$id);

        if($_img!=false){

            $_query = "DELETE FROM `images` WHERE ID = $id"; 

            $_result = $this->connect->getDB()->prepare($_query);

            $e = $_result->execute();

            if($e && $_result->rowCount()==1){
                if(self::destroyImages($_img["src"]))
                    return true;
                else return false;
            } # end if DB->execute()
            else return false;

        }
    } # end deleteImage()


    public function constructUrl(&$urlImages)  {
        $urlImages = protocol . "://" . http_host . $urlImages;
    }

    public function saveImage($imgObj = "", $name = "") {
        if(!empty($imgObj) && !empty($name)) {
            
            $extention = preg_replace("/(image)+\//", "", $imgObj["type"]);
            $extention = $extention == "jpeg" ? "jpg" : $extention;

            $name = preg_replace("/[\s]/", "_", $name);
            $name = preg_replace("/(ñ)/", "n", $name);
            $name = $name . "_" . time() . "_pictunex" . "." . $extention;

            $srcFinal = $this->pathIMG . $name;

            //Check File Type == Image
            if($imgObj["type"]=="image/jpg" 
            || $imgObj["type"]=="image/jpeg" 
            || $imgObj["type"]=="image/png" 
            || preg_match("/jpg\Z/", $imgObj["name"])==1
            || preg_match("/png\Z/", $imgObj["name"])==1){

                if(move_uploaded_file($imgObj["tmp_name"], $srcFinal))
                    return $this->urlPublicImg . $name;
                return false;
            }else return false;
        
        }
    } #end saveImage()


    public function destroyImages($URL) {
        $name = preg_replace("/(server)+\/+(public)+\/+(img)+\//", "", $URL);
        
        $srcFinal = $this->pathIMG . $name;

        if(file_exists($srcFinal)) {
            if(unlink($srcFinal)==1){
                return true;
            }else return false;

        }else return false;
    } # end destroyImages()
    

}#end Class Images


?>