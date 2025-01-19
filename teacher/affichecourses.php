<?php
require_once '../classes/connection.php';
require_once '../classes/coursClasse.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Initialize course objects
    $textCourse = new TextCourse();
    $videoCourse = new VideoCourse();

    // Get all courses with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 9; // Courses per page
    $offset = ($page - 1) * $limit;

    // Get courses with author information
    $textCourses = $textCourse->afficheCourse($db, $limit, $offset);
    $videoCourses = $videoCourse->afficheCourse($db, $limit, $offset);
    
    // Combine all courses
    $allCourses = array_merge($textCourses, $videoCourses);

    // Get total count for pagination
    $totalCourses = $textCourse->getCount($db) + $videoCourse->getCount($db);
    $totalPages = ceil($totalCourses / $limit);

} catch (PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database error: " . $e->getMessage());
    $error = "An error occurred while fetching courses. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Course Management Dashboard</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .course-card:hover .course-actions {
            opacity: 1;
        }
        .course-actions {
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg p-4 flex flex-col">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                    <i data-feather="user" class="text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h2>
                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'User'); ?></p>
                </div>
            </div>
            
            <nav class="space-y-2 flex-1">
                <div class="text-gray-800 font-medium px-4 py-2 mb-2">Main Menu</div>
                
                <a href="dashboard.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="home" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                <a href="courses.php" class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-lg flex items-center space-x-3 font-medium">
                    <i data-feather="book-open" class="w-4 h-4"></i>
                    <span>Courses</span>
                </a>

                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="users.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="users" class="w-4 h-4"></i>
                    <span>Users</span>
                </a>

                <a href="categories.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="folder" class="w-4 h-4"></i>
                    <span>Categories</span>
                </a>

                <a href="tags.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="tag" class="w-4 h-4"></i>
                    <span>Tags</span>
                </a>
                <?php endif; ?>

                <a href="statistics.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="bar-chart-2" class="w-4 h-4"></i>
                    <span>Statistics</span>
                </a>
            </nav>

            <a href="logout.php" class="mt-4 flex items-center space-x-2 text-gray-600 hover:text-red-600 px-4 py-2.5 rounded-lg transition-colors">
                <i data-feather="log-out" class="w-4 h-4"></i>
                <sp