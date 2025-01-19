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
    
    public function __construct($id, $title, $description, $price, $id_user, $id_categorie, $coursimage, $coursetype) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->id_user = $id_user;
        $this->id_categorie = $id_categorie;
        $this->coursimage = $coursimage;
        $this->coursetype = $coursetype;
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

    public static function getCourseById($db, $id) {
        try {
            $query = "SELECT * FROM courses WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($course) {
                if ($course['coursetype'] === 'text') {
                    return new TextCourse(
                        $course['id'],
                        $course['title'],
                        $course['description'],
                        $course['price'],
                        $course['id_user'],
                        $course['id_categorie'],
                        $course['coursimage'],
                        'text',
                        $course['documentcourse']
                    );
                } else {
                    return new VideoCourse(
                        $course['id'],
                        $course['title'],
                        $course['description'],
                        $course['price'],
                        $course['id_user'],
                        $course['id_categorie'],
                        $course['coursimage'],
                        'video',
                        $course['videocourse']
                    );
                }
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error retrieving course: " . $e->getMessage());
            return null;
        }
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

    public function afficheCourse($db) {
        try {
            $query = "SELECT c.*, u.username as author, cat.name as category_name 
                     FROM courses c 
                     LEFT JOIN users u ON c.id_user = u.id 
                     LEFT JOIN categories cat ON c.id_categorie = cat.id 
                     WHERE c.coursetype = 'text'";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $courses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $course = new TextCourse(
                    $row['id'],
                    $row['title'],
                    $row['description'],
                    $row['price'],
                    $row['id_user'],
                    $row['id_categorie'],
                    $row['coursimage'],
                    'text',
                    $row['documentcourse']
                );
                
                $courses[] = [
                    'course' => $course,
                    'author' => $row['author'],
                    'category_name' => $row['category_name']
                ];
            }
            
            return $courses;
        } catch (PDOException $e) {
            error_log("Error retrieving text courses: " . $e->getMessage());
            return [];
        }
    }
}

class VideoCourse extends Course {
    private $videocourse;
    
    public function __construct($id = null, $title = null, $description = null, $price = null, 
                              $id_user = null, $id_categorie = null, $coursimage = null, 
                              $coursetype = 'video', $videocourse = null) {
        parent::__construct($id, $title, $description, $price, $id_user, $id_categorie, $coursimage, $coursetype);
        $this->videocourse = $videocourse;
    }
    
    public function getVideoCourse() {
        return $this->videocourse;
    }
    
    public function setVideoCourse($videocourse) {
        $this->videocourse = $videocourse;
    }
    
    public function addCourse($db) {
        try {
            $query = "INSERT INTO courses (title, description, price, id_user, id_categorie, 
                      coursetype, coursimage, videocourse) 
                      VALUES (:title, :description, :price, :id_user, :id_categorie, 
                      :coursetype, :coursimage, :videocourse)";
            
            $stmt = $db->prepare($query);
            
            $params = [
                ':title' => $this->title,
                ':description' => $this->description,
                ':price' => $this->price,
                ':id_user' => $this->id_user,
                ':id_categorie' => $this->id_categorie,
                ':coursetype' => 'video',
                ':coursimage' => $this->coursimage,
                ':videocourse' => $this->videocourse
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error adding video course: " . $e->getMessage());
            return false;
        }
    }

    public function afficheCourse($db) {
        try {
            $query = "SELECT c.*, u.username as author, cat.name as category_name 
                     FROM courses c 
                     LEFT JOIN users u ON c.id_user = u.id 
                     LEFT JOIN categories cat ON c.id_categorie = cat.id 
                     WHERE c.coursetype = 'video'";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $courses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $course = new VideoCourse(
                    $row['id'],
                    $row['title'],
                    $row['description'],
                    $row['price'],
                    $row['id_user'],
                    $row['id_categorie'],
                    $row['coursimage'],
                    'video',
                    $row['videocourse']
                );
                
                $courses[] = [
                    'course' => $course,
                    'author' => $row['author'],
                    'category_name' => $row['category_name']
                ];
            }
            
            return $courses;
        } catch (PDOException $e) {
            error_log("Error retrieving video courses: " . $e->getMessage());
            return [];
        }
    }
}