<?php
require_once '../classes/connection.php';
require_once '../classes/coursClasse.php'; // Make sure this points to your Course class file



session_start();
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}


try {
    // Validate form submission
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate required fields
    $required_fields = ['title', 'description', 'price', 'id_user', 'categorie_id'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    // Handle file upload
    if (!isset($_FILES['courseimage']) || $_FILES['courseimage']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Course image upload failed');
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['courseimage']['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, and GIF are allowed.');
    }

    // Generate unique filename
    $extension = pathinfo($_FILES['courseimage']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $upload_path = 'uploads/courses/'; // Make sure this directory exists and is writable
    
    // Create upload directory if it doesn't exist
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    // Move uploaded file
    if (!move_uploaded_file($_FILES['courseimage']['tmp_name'], $upload_path . $filename)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Prepare course data
    $courseData = [
        'title' => htmlspecialchars(trim($_POST['title'])),
        'description' => htmlspecialchars(trim($_POST['description'])),
        'price' => floatval($_POST['price']),
        'id_user' => intval($_POST['id_user']),
        'id_categorie' => intval($_POST['categorie_id']),
        'courseimage' => $filename
    ];

    // Create new text course
    $course = new TextCourse();
    if (!$course->addCourse($courseData)) {
        throw new Exception('Failed to add course to database');
    }

    // Redirect on success
    $_SESSION['success_message'] = 'Course added successfully!';
    header('Location: courses.php'); // Adjust the redirect location as needed
    exit;

} catch (Exception $e) {
    // Log error
    error_log("Error in process_course.php: " . $e->getMessage());
    
    // If a file was uploaded and an error occurred after, try to remove it
    if (isset($filename) && file_exists($upload_path . $filename)) {
        unlink($upload_path . $filename);
    }

    // Set error message and redirect
    $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
    header('Location: add_course.php'); // Adjust the redirect location as needed
    exit;
}