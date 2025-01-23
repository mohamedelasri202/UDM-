<?php

require_once '../classes/connection.php';
require_once '../classes/coursClasse.php';
require_once '../classes/categorieClasse.php';
require_once '../classes/tags.php';
require_once '../classes/tagscours.php';






session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../aside/login.php');
    exit();
}
// Rest of your existing code...

class CourseDetails
{
    private $id;
    private $title;
    private $description;
    private $price;
    private $author;
    private $category;
    private $image;
    private $type;
    private $content; // video path or text content
    private $tags;

    public function __construct($id, $title, $description, $price, $author, $category, $image, $type, $content, $tags)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->author = $author;
        $this->category = $category;
        $this->image = $image;
        $this->type = $type;
        $this->content = $content;
        $this->tags = $tags;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getAuthor()
    {
        return $this->author;
    }
    public function getCategory()
    {
        return $this->category;
    }
    public function getImage()
    {
        return $this->image;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getContent()
    {
        return $this->content;
    }
    public function getTags()
    {
        return $this->tags;
    }

    public static function getCourseById($courseId)
    {
        $db = Database::getInstance()->getConnection();

        try {
            // Get course basic info and join with related tables
            $stmt = $db->prepare("
                SELECT c.*, u.name as author_name, cat.title as category_name,
                       CASE 
                           WHEN c.coursetype = 'video' THEN c.videocourse
                           ELSE c.documentcourse
                       END as content
                FROM courses c
                LEFT JOIN user u ON c.id_user = u.id
                LEFT JOIN categories cat ON c.id_categorie = cat.id
                WHERE c.id = ?
            ");
            $stmt->execute([$courseId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            // Get tags for the course
            $tagStmt = $db->prepare("
                SELECT t.* 
                FROM tags t 
                JOIN tagscours tc ON t.id = tc.id_tag 
                WHERE tc.id_course = ?
            ");
            $tagStmt->execute([$courseId]);
            $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);

            // Create and return CourseDetails object
            return new CourseDetails(
                $result['id'],
                $result['title'],
                $result['description'],
                $result['price'],
                $result['author_name'],
                $result['category_name'],
                $result['coursimage'],
                $result['coursetype'],
                $result['content'],
                $tags
            );
        } catch (PDOException $e) {
            error_log("Error in getCourseById: " . $e->getMessage());
            return null;
        }
    }

    public static function getRelatedCourses($categoryId, $currentCourseId, $limit = 3)
    {
        $db = Database::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("
                SELECT c.*, u.name as author_name, cat.title as category_name,
                       CASE 
                           WHEN c.coursetype = 'video' THEN c.videocourse
                           ELSE c.documentcourse
                       END as content
                FROM courses c
                LEFT JOIN user u ON c.id_user = u.id
                LEFT JOIN categories cat ON c.id_categorie = cat.id
                WHERE c.id_categorie = ? AND c.id != ?
                LIMIT ?
            ");
            $stmt->execute([$categoryId, $currentCourseId, $limit]);

            $relatedCourses = [];
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Get tags for each course
                $tagStmt = $db->prepare("
                    SELECT t.* 
                    FROM tags t 
                    JOIN tagscours tc ON t.id = tc.id_tag 
                    WHERE tc.id_course = ?
                ");
                $tagStmt->execute([$result['id']]);
                $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);

                $relatedCourses[] = new CourseDetails(
                    $result['id'],
                    $result['title'],
                    $result['description'],
                    $result['price'],
                    $result['author_name'],
                    $result['category_name'],
                    $result['coursimage'],
                    $result['coursetype'],
                    $result['content'],
                    $tags
                );
            }

            return $relatedCourses;
        } catch (PDOException $e) {
            error_log("Error in getRelatedCourses: " . $e->getMessage());
            return [];
        }
    }

    public function formatPrice()
    {
        return number_format($this->price, 2);
    }
}





try {
    $courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$courseId) {
        throw new Exception("Invalid course ID");
    }

    $courseDetails = CourseDetails::getCourseById($courseId);
    if (!$courseDetails) {
        throw new Exception("Course not found");
    }

    $relatedCourses = CourseDetails::getRelatedCourses(
        $courseDetails->getId(),
        $courseId
    );
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: course.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($courseDetails->getTitle()); ?> - Course Details</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div x-data="{
        activeTab: 'overview',
        showMore: false,
        progress: 85,
        videoPlaying: false,
        currentSection: null,
        isSticky: false
    }"
        x-init="window.addEventListener('scroll', () => isSticky = window.pageYOffset > 100)"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
        <!-- Course Header -->
        <div class="relative bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="relative h-80 lg:h-96">
                <img src="<?php echo htmlspecialchars($courseDetails->getImage()); ?>"
                    alt="Course cover"
                    class="w-full h-full object-cover transform transition-transform duration-700 hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent"></div>

                <!-- Video Preview Button -->


                <!-- Course Info Overlay -->
                <div class="absolute bottom-0 left-0 p-8 w-full">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="bg-blue-600 text-xs font-semibold px-4 py-1.5 rounded-full text-white">
                            <?php echo htmlspecialchars($courseDetails->getCategory()); ?>
                        </span>
                        <span class="bg-purple-600 text-xs font-semibold px-4 py-1.5 rounded-full text-white">
                            <?php echo ucfirst($courseDetails->getType()); ?> Course
                        </span>
                    </div>
                    <h1 class="text-4xl font-bold text-white mb-4 leading-tight">
                        <?php echo htmlspecialchars($courseDetails->getTitle()); ?>
                    </h1>
                    <div class="flex items-center space-x-4 text-white">
                        <span class="flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo htmlspecialchars($courseDetails->getAuthor()); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2">
                <!-- Navigation Tabs -->
                <div class="bg-white rounded-xl shadow-lg mb-6 p-1"
                    :class="{ 'fixed top-4 left-4 right-4 lg:max-w-[66%] z-50 transition-all duration-300': isSticky }">
                    <nav class="flex space-x-2">
                        <template x-for="tab in ['overview', 'curriculum', 'reviews']">
                            <button @click="activeTab = tab"
                                :class="{
                                        'bg-blue-600 text-white': activeTab === tab,
                                        'hover:bg-gray-100': activeTab !== tab
                                    }"
                                class="flex-1 py-3 px-4 rounded-lg text-sm font-medium transition-all duration-200 capitalize">
                                <span x-text="tab"></span>
                            </button>
                        </template>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" x-transition>
                        <!-- Course Description -->
                        <div>
                            <h2 class="text-2xl font-bold mb-4">Course Description</h2>
                            <div class="prose max-w-none">
                                <p class="text-gray-600 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($courseDetails->getDescription())); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Course Material -->
                        <div class="mt-8">
                            <?php if ($courseDetails->getType() === 'video'): ?>
                                <div class="video-player rounded-xl overflow-hidden">
                                    <video controls class="w-full">
                                        <source src="<?php echo htmlspecialchars($courseDetails->getContent()); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            <?php else: ?>
                                <div class="text-content prose max-w-none">
                                    <?php echo nl2br(htmlspecialchars($courseDetails->getContent())); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tags Section -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($courseDetails->getTags() as $tag): ?>
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                    <?php echo htmlspecialchars($tag['title']); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Course Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden sticky top-8">
                    <!-- Pricing Section -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-3xl font-bold text-gray-900">
                                $<?php echo $courseDetails->formatPrice(); ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <!-- Action Buttons -->
                        <div class="space-y-4">
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                                    <?php
                                    echo htmlspecialchars($_SESSION['success']);
                                    unset($_SESSION['success']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                                    <?php
                                    echo htmlspecialchars($_SESSION['error']);
                                    unset($_SESSION['error']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <form action="../students/process_inscription.php" method="POST">
                                <input type="hidden" name="course_id" value="<?php echo $courseDetails->getId(); ?>">
                                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-medium hover:bg-blue-700 transform hover:scale-[1.02] transition-all duration-200">
                                    Enroll Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Courses -->
        <?php if (!empty($relatedCourses)): ?>
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">Related Courses</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($relatedCourses as $course): ?>
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <img src="<?php echo htmlspecialchars($course->getImage()); ?>"
                                alt="Related Course"
                                class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold mb-2">
                                    <?php echo htmlspecialchars($course->getTitle()); ?>
                                </h3>
                                <a href="coursedetails.php?id=<?php echo $course->getId(); ?>"
                                    class="text-blue-600 hover:text-blue-700 font-medium">
                                    View Course
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>