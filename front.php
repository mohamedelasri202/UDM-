<?php
session_start();
require_once 'classes/userClasse.php';
require_once 'classes/categorieClasse.php';
require_once 'classes/coursClasse.php';
require_once 'classes/tagscours.php';

// Fetch existing courses
$db = Database::getInstance()->getConnection();
try {
    $textCourse = new TextCourse();
    $videoCourse = new VideoCourse();

    $textCourses = $textCourse->afficheCourse($db);
    $videoCourses = $videoCourse->afficheCourse($db);

    // Merge and sort all courses
    $allCourses = array_merge($textCourses, $videoCourses);
    usort($allCourses, function ($a, $b) {
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
    <title>LearnHub - Online Learning Platform</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .course-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .course-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-bottom: 1px solid #ddd;
    }

    .course-content {
        padding: 15px;
    }

    .course-content h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .course-content p {
        font-size: 1rem;
        color: #666;
        margin-bottom: 15px;
    }

    .course-tags {
        margin-bottom: 10px;
    }

    .course-tags .tag {
        display: inline-block;
        background-color: #f3f3f3;
        color: #555;
        font-size: 0.9rem;
        padding: 5px 10px;
        border-radius: 5px;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .course-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .course-meta .price {
        font-weight: bold;
        color: #27ae60;
    }

    .course-meta .type {
        display: flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 5px;
        color: #fff;
        font-size: 0.9rem;
    }

    .course-meta .type.tag-blue {
        background-color: #3498db;
    }

    .course-meta .type.tag-purple {
        background-color: #9b59b6;
    }

    .course-author,
    .course-category {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 10px;
    }

    .view-details-btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
        transition: background-color 0.3s;
    }

    .view-details-btn:hover {
        background-color: #0056b3;
    }

    .view-details-btn i {
        margin-right: 5px;
    }
</style>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">LearnHub</span>
                </div>

                <!-- Center Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-gray-700 hover:text-blue-600">Home</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="teacher/mes-courses.php" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-book-reader mr-2"></i>Mes Courses
                        </a>
                    <?php endif; ?>

                </div>

                <!-- Right Side - Login/User Section -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Search Icon -->
                    <button class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <!-- Not Logged In -->
                        <div class="flex items-center space-x-3">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                <a href="aside/login.php">Login</a>
                            </button>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <a href="aside/register.php">Sign Up</a>
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Logged In -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="text-gray-600 hover:text-blue-600 relative">
                                <i class="fas fa-bell"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
                            </button>
                            <!-- User Name and Logout -->
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-700">
                                    <?php
                                    $displayName = isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '';
                                    $displayLastName = isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : '';
                                    echo trim("$displayName $displayLastName");
                                    ?>
                                </span>
                                <a href="teacher/logout.php" class="text-red-600 hover:text-red-700 px-3 py-2 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-blue-600 h-[600px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 text-white">
                    <h1 class="text-5xl font-bold mb-6">Transform Your Future with Online Learning</h1>
                    <p class="text-xl mb-8">Access world-class courses from expert instructors. Learn at your own pace and build the skills you need for success.</p>
                    <button class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
                        Browse Courses
                    </button>
                </div>
                <div class="md:w-1/2 mt-8 md:mt-0">
                    <img src="cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDI0LTAyL3Jhd3BpeGVsb2ZmaWNlMTVfYV9taW5pbWFsX2FuZF9sZXNzX2RldGFpbF9pbGx1c3RyYXRpb25fb2ZfYV9sb19jZDcwMDZlNi0wOWExLTQ2ZGEtOTljMi0wMmU2YTg4OTg2N2VfMS5qcGc.webp" alt="Learning" class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Course Categories</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $categories = Category::getAll();
                foreach ($categories as $category):
                ?>
                    <div class="bg-gray-50 p-6 rounded-lg shadow hover:shadow-lg transition">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-laptop-code text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($category->getTitle()); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($category->getDescription()); ?></p>
                        <a href="courses.php?category=<?php echo $category->getId(); ?>" class="text-blue-600 hover:text-blue-700">Browse Courses →</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Featured Courses -->
    <div class="grid">
        <?php foreach ($allCourses as $courseData):
            $course = $courseData['course'];
            // Fetch tags for this course
            $courseTags = CourseTag::getTagsByCourse($db, $course->getId());
        ?>
            <div class="course-card">


                <div class="course-image">
                    <?php if ($course->getCoursImage()): ?>
                        <?php
                        $imagePath = $course->getCoursImage();
                        $imagePath = 'teacher/' . $imagePath;
                        ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>"
                            alt="<?php echo htmlspecialchars($course->getTitle()); ?>">
                    <?php endif; ?>
                </div>
                <div class="course-content">
                    <h3><?php echo htmlspecialchars($course->getTitle()); ?></h3>
                    <p><?php echo htmlspecialchars($course->getDescription()); ?></p>

                    <div class="course-tags">
                        <?php if (!empty($courseTags)): ?>
                            <?php foreach ($courseTags as $tag): ?>
                                <span class="tag"><?php echo htmlspecialchars($tag['title']); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="course-meta">
                        <span class="price">
                            <i class="fas fa-dollar-sign"></i>
                            <?php echo number_format($course->getPrice(), 2); ?>
                        </span>
                        <span class="type <?php echo $course->getCoursType() === 'video' ? 'tag-blue' : 'tag-purple'; ?>">
                            <i class="fas <?php echo $course->getCoursType() === 'video' ? 'fa-video' : 'fa-book'; ?>"></i>
                            <?php echo ucfirst($course->getCoursType()); ?>
                        </span>
                    </div>

                    <?php if (isset($courseData['author'])): ?>
                        <div class="course-author">
                            <i class="fas fa-user"></i>
                            By: <?php echo htmlspecialchars($courseData['author']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($courseData['category_name'])): ?>
                        <div class="course-category">
                            <i class="fas fa-tags"></i>
                            Category: <?php echo htmlspecialchars($courseData['category_name']); ?>
                        </div>
                    <?php endif; ?>

                    <a href="#" onclick="return checkLogin(<?php echo isset($_SESSION['user_id']); ?>)" class="view-details-btn" data-course="<?php echo $course->getId(); ?>">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <!-- Add more course cards as needed -->
    </div>
    </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">LearnHub</h3>
                    <p class="text-gray-400">Empowering learners worldwide with quality online education.</p>
                </div>
                <!-- Footer content here (same as before) -->
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 LearnHub. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script>
        function checkLogin(isLoggedIn) {
            if (!isLoggedIn) {
                alert('Please login to view course details');
                window.location.href = 'aside/login.php';
                return false;
            }
            // If logged in, redirect to course details
            let courseId = event.target.closest('.view-details-btn').dataset.course;
            window.location.href = 'teacher/coursedetails.php?id=' + courseId;
            return false;
        }
    </script>
</body>

</html>