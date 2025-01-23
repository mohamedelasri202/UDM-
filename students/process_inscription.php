<?php
session_start();
require_once '../classes/connection.php';
require_once 'inscriptioncourse.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: ../aside/login.php');
    exit();
}

if (!isset($_POST['course_id']) || !filter_var($_POST['course_id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error'] = "Invalid course ID";
    header('Location: course.php');
    exit();
}

try {
    $db = Database::getInstance()->getConnection();

    // Create new inscription
    $inscription = new Inscription($_SESSION['user_id'], $_POST['course_id']);

    if ($inscription->addInscription($db)) {
        $_SESSION['success'] = "Successfully enrolled in the course!";
    } else {
        $_SESSION['error'] = "You are already enrolled in this course or an error occurred";
    }
} catch (Exception $e) {
    error_log("Enrollment error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while processing your enrollment";
}

header('Location:../teacher/coursedetails.php?id=' . $_POST['course_id']);
exit();
