<?php
require_once 'connection.php';

abstract class Course {
    protected $id;
    protected $title;
    protected $description;
    protected $price;
    protected $id_user;
    protected $id_categorie;
    protected $coursimage;
    protected $coursetype;
    public function __construct($id, $title,$description,$price,$id_user,$id_categorie,$coursimage,$coursetype)
    {
        $this->id=$id;
        $this->title=$title;
        $this->description=$description;
        $this->price=$price;
        $this->id_user=$id_user;
        $this->id_categorie=$id_categorie;
        $this->coursimage=$coursimage;
        $this->coursetype=$coursetype;
        
    }
    
    // Abstract methods
    abstract protected function addCourse($db);
    abstract protected function afficheCourse($db);
    
    // Getters and setters
    public function setTitle($title) {
        $this->title = htmlspecialchars($title);
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setDescription($description) {
        $this->description = htmlspecialchars($description);
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setPrice($price) {
        $this->price = floatval($price);
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function setIdUser($id_user) {
        $this->id_user = intval($id_user);
    }
    
    public function getIdUser() {
        return $this->id_user;
    }
    
    public function setIdCategorie($id_categorie) {
        $this->id_categorie = intval($id_categorie);
    }
    
    public function getIdCategorie() {
        return $this->id_categorie;
    }
    
    public function setCoursImage($coursimage) {
        $this->coursimage = $coursimage;
    }
    
    public function getCoursImage() {
        return $this->coursimage;
    }
    
    public function setCoursType($coursetype) {
        $this->coursetype = $coursetype;
    }
    
    public function getCoursType() {
        return $this->coursetype;
    }
    
}

class TextCourse extends Course {
    private $documentcourse;
    
    public function __construct($id = null, $title = null, $description = null, $price = null, 
                              $id_user = null, $id_categorie = null, $coursimage = null, 
                              $coursetype = 'text', $documentcourse = null) {
        parent::__construct($id, $title, $description, $price, $id_user, $id_categorie, $coursimage, $coursetype);
        $this->documentcourse = $documentcourse;
    }
    
    public function getDocumentCourse() {
        return $this->documentcourse;
    }


    public function afficheCourse($db){

    }
    
    public function setDocumentCourse($documentcourse) {
        $this->documentcourse = htmlspecialchars($documentcourse);
    }
    
    public function addCourse($db) {
        try {
            $query = "INSERT INTO courses (title, description, price, id_user, id_categorie, 
                      coursetype, coursimage, documentcourse) 
                      VALUES (:title, :description, :price, :id_user, :id_categorie, 
                      :coursetype, :coursimage, :documentcourse)";
            
            $stmt = $db->prepare($query);
            
            $params = [
                ':title' => $this->title,
                ':description' => $this->description,
                ':price' => $this->price,
                ':id_user' => $this->id_user,
                ':id_categorie' => $this->id_categorie,
                ':coursetype' => 'text',
                ':coursimage' => $this->coursimage,
                ':documentcourse' => $this->documentcourse
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error adding text course: " . $e->getMessage());
            return false;
        }
    }
}
