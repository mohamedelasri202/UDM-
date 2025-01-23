<?php
require_once '../classes/coursClasse.php';
require_once '../students/inscriptioncourse.php';
$db = Database::getInstance()->getConnection();
session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: login.php');
    exit;
}

try {
    // Get all courses the user is enrolled in using the existing connection
    $inscriptions = Inscription::getInscriptionsByUser($db, $userId);

    // Fetch complete course objects for each inscription
    $enrolledCourses = [];
    foreach ($inscriptions as $inscription) {
        $course = Course::getCourseById($db, $inscription['id_course']);
        if ($course) {
            $enrolledCourses[] = $course;
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $enrolledCourses = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Courses</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">LearnHub</span>
                </div>

                <!-- Center Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="../front.php" class="text-gray-700 hover:text-blue-600">Home</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="mes-courses.php" class="text-gray-700 hover:text-blue-600">
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

    <main class="p-6">
        <?php if (empty($enrolledCourses)): ?>
            <div class="text-center py-8">
                <p class="text-gray-600 text-lg">Vous n'êtes inscrit à aucun cours pour le moment.</p>
                <a href="../front.php" class="text-blue-500 hover:text-blue-700 mt-4 inline-block">
                    Découvrir les cours disponibles
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($enrolledCourses as $course): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <?php if ($course->getCoursImage()): ?>
                            <img src="<?php echo htmlspecialchars($course->getCoursImage()); ?>"
                                alt="<?php echo htmlspecialchars($course->getTitle()); ?>"
                                class="w-full h-40 object-cover">
                        <?php else: ?>
                            <div class="bg-blue-100 h-40 flex items-center justify-center">
                                <p class="text-blue-600 font-bold text-lg">
                                    <?php echo htmlspecialchars($course->getTitle()); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="p-4">
                            <h2 class="text-xl font-semibold text-blue-600">
                                <?php echo htmlspecialchars($course->getTitle()); ?>
                            </h2>
                            <p class="text-gray-600 mt-2">
                                <?php echo htmlspecialchars($course->getDescription()); ?>
                            </p>

                            <!-- Course Type Badge -->
                            <div class="mt-3">
                                <span class="inline-block px-2 py-1 text-sm <?php echo $course->getCoursType() === 'video' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700'; ?> rounded-full">
                                    <?php echo ucfirst($course->getCoursType()); ?> Course
                                </span>
                            </div>

                            <!-- Price -->
                            <div class="mt-2 text-gray-700">
                                Prix: <?php echo number_format($course->getPrice(), 2); ?> €
                            </div>

                            <!-- <a href="../teacher/mes-courses.php echo $course->getId(); ?>"
                               
                            class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
/*
                             /
                            </a> -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-blue-600 text-white text-center p-4 mt-6">
        <p>&copy; <?php echo date('Y'); ?> Mes Courses. All rights reserved.</p>
    </footer>
</body>

</html>