


<?php
session_start();

require_once '../classes/connection.php';
require_once '../classes/coursClasse.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Initialize course objects
    $textCourse = new TextCourse();
    $videoCourse = new VideoCourse();

    // Get all courses
    $textCourses = $textCourse->afficheCourse($db);
    $videoCourses = $videoCourse->afficheCourse($db);

    // Combine and sort all courses
    $allCourses = array_merge($textCourses, $videoCourses);
    usort($allCourses, function($a, $b) {
        return strcmp($b['course']->getTitle(), $a['course']->getTitle());
    });
} catch (PDOException $e) {
    error_log("Error fetching courses: " . $e->getMessage());
    $error = "An error occurred while loading courses.";
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
    <title>Courses</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
                    <h2 class="font-semibold text-gray-800">Henry Klein</h2>
                    <p class="text-sm text-gray-500">Administrator</p>
                </div>
            </div>
            
            <nav class="space-y-2 flex-1">
                <div class="text-gray-800 font-medium px-4 py-2 mb-2">Main Menu</div>
                
                <a href="users.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="users" class="w-4 h-4"></i>
                    <span>Users</span>
                </a>

                <a href="courses.php" class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-lg flex items-center space-x-3 font-medium">
                    <i data-feather="book-open" class="w-4 h-4"></i>
                    <span>Courses</span>
                </a>

                <a href="categories.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="folder" class="w-4 h-4"></i>
                    <span>Categories</span>
                </a>

                <a href="tags.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="tag" class="w-4 h-4"></i>
                    <span>Tags</span>
                </a>

                <a href="" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="bar-chart-2" class="w-4 h-4"></i>
                    <span>Statistics</span>
                </a>
            </nav>

            <a href="/logout" class="mt-4 flex items-center space-x-2 text-gray-600 hover:text-red-600 px-4 py-2.5 rounded-lg transition-colors">
                <i data-feather="log-out" class="w-4 h-4"></i>
                <span>Logout</span>
            </a>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Available Courses</h1>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-blue-700 transition-colors">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    <span>Add New Course</span>
                </button>
            </div>

            <!-- Courses Grid -->
          <!-- Courses Grid -->


    <script>
        feather.replace();
      

    function deleteCourse(courseId) {
        if (confirm('Are you sure you want to delete this course?')) {
            fetch(`delete_course.php?id=${courseId}`, {
                method: 'DELETE',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting course: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting course. Please try again.');
            });
        }
    }
</script>
   
</body>
</html>