<?php
require_once 'connection.php';

class CourseTag
{
    private $id_course;
    private $id_tag;

    public function __construct($id_course = null, $id_tag = null)
    {
        $this->id_course = $id_course;
        $this->id_tag = $id_tag;
    }

    public function addCourseTag($db)
    {
        $sql = $db->prepare("INSERT INTO tagscours (id_course, id_tag) VALUES (:id_course, :id_tag)");
        $sql->bindParam(':id_course', $this->id_course);
        $sql->bindParam(':id_tag', $this->id_tag);
        return $sql->execute();
    }

    public static function getTagsByCourse($db, $courseId)
    {
        $sql = $db->prepare("
            SELECT t.* 
            FROM tags t 
            JOIN tagscours tc ON t.id = tc.id_tag 
            WHERE tc.id_course = :courseId
        ");
        $sql->bindParam(':courseId', $courseId);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
