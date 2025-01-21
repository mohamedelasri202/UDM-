<?php
require_once '../classes/connection.php';

class CourseInscription
{
    private $id_user;
    private $id_course;

    public function __construct($id_user, $id_course)
    {
        $this->id_user = $id_user;
        $this->id_course = $id_course;
    }

    public function isAlreadyEnrolled($db)
    {
        try {
            $query = "SELECT COUNT(*) FROM inscriptions 
                     WHERE id_user = :id_user AND id_course = :id_course";

            $stmt = $db->prepare($query);
            $stmt->execute([
                ':id_user' => $this->id_user,
                ':id_course' => $this->id_course
            ]);

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking enrollment: " . $e->getMessage());
            return false;
        }
    }

    public function inscription($db)
    {
        try {
            // First check if user is already enrolled
            if ($this->isAlreadyEnrolled($db)) {
                return ['success' => false, 'message' => 'You are already enrolled in this course'];
            }

            // Insert the inscription
            $query = "INSERT INTO inscriptions (id_user, id_course) 
                     VALUES (:id_user, :id_course)";

            $stmt = $db->prepare($query);

            $result = $stmt->execute([
                ':id_user' => $this->id_user,
                ':id_course' => $this->id_course
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Successfully enrolled in the course'];
            } else {
                return ['success' => false, 'message' => 'Failed to enroll in the course'];
            }
        } catch (PDOException $e) {
            error_log("Error in course inscription: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred during enrollment'];
        }
    }

    public static function getUserCourses($db, $userId)
    {
        try {
            $query = "SELECT c.* 
                     FROM courses c
                     JOIN inscriptions i ON c.id = i.id_course
                     WHERE i.id_user = :user_id";

            $stmt = $db->prepare($query);
            $stmt->execute([':user_id' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user courses: " . $e->getMessage());
            return [];
        }
    }
}
