<?php

require_once 'connection.php';
class Category
{
    private $id;
    private $title;
    private $description;
    private $db;

    public function __construct($id = null, $title = '', $description = '')
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    // Setters
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    // CRUD Operations
    public function save()
    {
        try {
            if ($this->id === null) {
                // Create new category
                $stmt = $this->db->prepare(
                    "INSERT INTO categories (title, description) VALUES (:title, :description)"
                );
            } else {
                // Update existing category
                $stmt = $this->db->prepare(
                    "UPDATE categories SET title = :title, description = :description WHERE id = :id"
                );
                $stmt->bindParam(':id', $this->id);
            }

            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error saving category: " . $e->getMessage());
            return false;
        }
    }

    public function delete()
    {
        if ($this->id === null) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $this->id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    public static function findById($id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new Category(
                    $result['id'],
                    $result['title'],
                    $result['description']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error finding category: " . $e->getMessage());
            return null;
        }
    }

    public static function getAll()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM categories ORDER BY id DESC");
            $categories = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $categories[] = new Category(
                    $row['id'],
                    $row['title'],
                    $row['description']
                );
            }

            return $categories;
        } catch (PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }
}
