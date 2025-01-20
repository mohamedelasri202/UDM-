<?php 
require_once 'connection.php';
class Tags {
    private $id;
    private $title;

    public function __construct($id, $title) {
        $this->id = $id;
        $this->title = $title;
    }

    public function getID() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function addTags($db) {
        $sql = $db->prepare("INSERT INTO tags (title) VALUES (:title)");
        $sql->bindParam(':title', $this->title);
        
        if($sql->execute()) {
            $this->id = $db->lastInsertId();
            return new Tags($this->id, $this->title);
        }
        return null;
    }

    public static function afficheTags() {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("SELECT * FROM tags");
        $stm->execute();
        
        $tags = [];
        $results = $stm->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($results as $row) {
            $tags[] = new Tags($row['id'], $row['title']);
        }
        
        return $tags;
    }

    public function editeTags() {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("UPDATE tags SET title = :title WHERE id = :id");
        $stm->bindParam(':title', $this->title);
        $stm->bindParam(':id', $this->id);
        
        if($stm->execute()) {
            return new Tags($this->id, $this->title);
        }
        return null;
    }

    public function deleteTags() {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("DELETE FROM tags WHERE id = :id");
        $stm->bindParam(':id', $this->id);
        
        return $stm->execute();
    }

    public static function getTagById($id) {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("SELECT * FROM tags WHERE id = :id");
        $stm->bindParam(':id', $id);
        $stm->execute();
        
        if($row = $stm->fetch(PDO::FETCH_ASSOC)) {
            return new Tags($row['id'], $row['title']);
        }
        return null;
    }public static function getTagByTitle($db, $title) {
    $sql = $db->prepare("SELECT * FROM tags WHERE title = :title");
    $sql->bindParam(':title', $title);
    $sql->execute();
    
    if($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        return new Tags($row['id'], $row['title']);
    }
    return null;
}
}
