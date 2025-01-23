<?php
require_once '../classes/connection.php';
require_once 'students/inscriptioncourse.php';

// Assuming you have the user's ID from a session or other source
session_start();
$userId = $_SESSION['user_id'] ?? null; // Replace with your actual user ID source

if (!$userId) {
    header('Location: login.php'); // Redirect if no user is logged in
    exit;
}

try {
    // Get database connection
    $db = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get user's inscriptions
    $inscriptions = Inscription::getInscriptionsByUser($db, $userId);

    // Fetch course details for each inscription
    $courses = [];
    foreach ($inscriptions as $inscription) {
        $query = "SELECT * FROM courses WHERE id = :id_course";
        $stmt = $db->prepare($query);
        $stmt->execute([':id_course' => $inscription['id_course']]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($course) {
            $courses[] = $course;
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $courses = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">LearnHub</span>
                </div>

                <!-- Center Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="front.php" class="text-gray-700 hover:text-blue-600">Home</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="teacher/mes-courses.php" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-book-reader mr-2"></i>Mes Courses
                        </a>
                    <?php endif; ?>
                    <a href="#" class="text-gray-700 hover:text-blue-600">Categories</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600">Instructors</a>
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

    <!-- Main Content -->
    <main class="p-6">
        <?php if (empty($courses)): ?>
            <div class="text-center py-8">
                <p class="text-gray-600 text-lg">Vous n'êtes inscrit à aucun cours pour le moment.</p>
                <a href="available-courses.php" class="text-blue-500 hover:text-blue-700 mt-4 inline-block">
                    Découvrir les cours disponibles
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($courses as $course): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <?php if (!empty($course['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($course['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($course['title']); ?>"
                                class="w-full h-40 object-cover">
                        <?php else: ?>
                            <div class="bg-blue-100 h-40 flex items-center justify-center">
                                <p class="text-blue-600 font-bold text-lg">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="p-4">
                            <h2 class="text-xl font-semibold text-blue-600">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </h2>
                            <p class="text-gray-600 mt-2">
                                <?php echo htmlspecialchars($course['description']); ?>
                            </p>
                            <a href="course.php?id=<?php echo $course['id']; ?>"
                                class="text-blue-500 mt-4 inline-block hover:text-blue-700">
                                Voir le cours
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white text-center p-4 mt-6">
        <p>&copy; <?php echo date('Y'); ?> Mes Courses. All rights reserved.</p>
    </footer>
</body>

</html>