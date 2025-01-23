<?php
require_once '../classes/connection.php';

class Inscription
{
    private $id_user;
    private $id_course;

    public function __construct($id_user = null, $id_course = null)
    {
        $this->id_user = $id_user;
        $this->id_course = $id_course;
    }

    // Getters
    public function getIdUser()
    {
        return $this->id_user;
    }

    public function getIdCourse()
    {
        return $this->id_course;
    }

    // Setters
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

    public function setIdCourse($id_course)
    {
        $this->id_course = $id_course;
    }

    // Method to add new inscription
    public function addInscription($db)
    {
        try {
            // Check if user is already enrolled
            if ($this->isAlreadyEnrolled($db)) {
                return false;
            }

            $query = "INSERT INTO inscription (id_user, id_course) VALUES (:id_user, :id_course)";
            $stmt = $db->prepare($query);

            $params = [
                ':id_user' => $this->id_user,
                ':id_course' => $this->id_course
            ];

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error adding inscription: " . $e->getMessage());
            return false;
        }
    }

    // Method to check if user is already enrolled in the course
    public function isAlreadyEnrolled($db)
    {
        try {
            $query = "SELECT * FROM inscription WHERE id_user = :id_user AND id_course = :id_course";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':id_user' => $this->id_user,
                ':id_course' => $this->id_course
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error checking enrollment: " . $e->getMessage());
            return true; // Return true to prevent enrollment on error
        }
    }

    // Method to get all inscriptions for a user
    public static function getInscriptionsByUser($db, $id_user)
    {
        try {
            $query = "SELECT * FROM inscription WHERE id_user = :id_user";
            $stmt = $db->prepare($query);
            $stmt->execute([':id_user' => $id_user]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user inscriptions: " . $e->getMessage());
            return [];
        }
    }

    // Method to get all inscriptions for a course
    public static function getInscriptionsByCourse($db, $id_course)
    {
        try {
            $query = "SELECT * FROM inscription WHERE id_course = :id_course";
            $stmt = $db->prepare($query);
            $stmt->execute([':id_course' => $id_course]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting course inscriptions: " . $e->getMessage());
            return [];
        }
    }
}
