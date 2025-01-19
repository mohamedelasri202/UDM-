<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location:../aside/login.php');
    exit();
}

require_once '../classes/connection.php';
require_once '../classes/coursClasse.php';
require_once '../classes/categorieClasse.php';

$db = Database::getInstance()->getConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate course type
        if (!isset($_POST['coursetype'])) {
            throw new Exception("Course type is required");
        }

        // Common validation for all courses
        if (!isset($_POST['id_categorie'])) {
            throw new Exception("Category is required");
        }

        // Handle image upload for both course types
        $imagePath = '';
        if (isset($_FILES['coursimage']) && $_FILES['coursimage']['error'] === 0) {
            $uploadDir = 'uploads/images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Validate image type
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['coursimage']['type'], $allowedImageTypes)) {
                throw new Exception("Invalid image file type. Please upload JPEG, PNG, or GIF image.");
            }
            
            $imageName = time() . '_' . basename($_FILES['coursimage']['name']);
            $imagePath = $uploadDir . $imageName;
            
            if (!move_uploaded_file($_FILES['coursimage']['tmp_name'], $imagePath)) {
                throw new Exception("Error uploading image file");
            }
        } else {
            throw new Exception("Course image is required");
        }

        if ($_POST['coursetype'] === 'text') {
            // Handle text course
            $textCourse = new TextCourse();
            $textCourse->setTitle($_POST['title']);
            $textCourse->setDescription($_POST['description']);
            $textCourse->setPrice($_POST['price']);
            $textCourse->setIdUser($_SESSION['user_id']);
            $textCourse->setIdCategorie($_POST['id_categorie']);
            $textCourse->setCoursImage($imagePath);
            
            if (isset($_POST['documentcourse']) && !empty(trim($_POST['documentcourse']))) {
                $textCourse->setDocumentCourse($_POST['documentcourse']);
            } else {
                throw new Exception("Course content is required for text courses");
            }
            
            if ($textCourse->addCourse($db)) {
                $_SESSION['success_message'] = "Text course added successfully!";
                header('Location: courses.php');
                exit();
            }
        } 
        else if ($_POST['coursetype'] === 'video') {
            // Handle video course
            if (!isset($_FILES['videocourse']) || $_FILES['videocourse']['error'] !== 0) {
                throw new Exception("Video file is required for video courses");
            }

            // Validate video type
            $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
            if (!in_array($_FILES['videocourse']['type'], $allowedVideoTypes)) {
                throw new Exception("Invalid video file type. Please upload MP4, WebM, or Ogg video.");
            }

            // Handle video upload
            $uploadDir = 'uploads/videos/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $videoName = time() . '_' . basename($_FILES['videocourse']['name']);
            $videoPath = $uploadDir . $videoName;
            
            if (!move_uploaded_file($_FILES['videocourse']['tmp_name'], $videoPath)) {
                throw new Exception("Error uploading video file");
            }

            // Create video course
            $videoCourse = new VideoCourse(
                null,
                $_POST['title'],
                $_POST['description'],
                $_POST['price'],
                $_SESSION['user_id'],
                $_POST['id_categorie'],
                $imagePath,
                'video',
                $videoPath
            );

            if ($videoCourse->addCourse($db)) {
                $_SESSION['success_message'] = "Video course added successfully!";
                header('Location: courses.php');
                exit();
            }
        }
        else {
            throw new Exception("Invalid course type");
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
}
// Fetch categories for dropdown
try {
    $stmt = $db->prepare("SELECT id, title FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $message = "Error loading categories: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <link rel="stylesheet" href="../style/style.css">
   
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i> EduPanel
            </div>
            <nav>
                <div class="nav-item active">
                    <i class="fas fa-home"></i> Dashboard
                </div>
                <div class="nav-item">
                    <i class="fas fa-book"></i> Courses
                </div>
                <div class="nav-item">
                    <i class="fas fa-users"></i> Students
                </div>
                <div class="nav-item">
                    <i class="fas fa-chart-bar"></i> Analytics
                </div>
                <div class="nav-item">
                    <i class="fas fa-cog"></i> Settings
                </div>
            </nav>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1 class="page-title">Course Management</h1>
                <button class="add-course-btn" onclick="openModal()">
                    <i class="fas fa-plus"></i> Add New Course
                </button>
            </div>
            
            <div id="courseModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Course</h2>
                        <button class="close-btn" onclick="closeModal()">&times;</button>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
    <div class="form-group">
        <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
        <input type="text" name="title" id="title" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="form-group">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="4" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div class="form-group">
        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" name="price" id="price" step="0.01" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="form-group">
        <label for="id_categorie" class="block text-sm font-medium text-gray-700">Category</label>
        <select name="id_categorie" id="id_categorie" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                    <?php echo htmlspecialchars($category['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Course Type Selection -->
    <div class="form-group">
        <label for="coursetype" class="block text-sm font-medium text-gray-700">Course Type</label>
        <select id="coursetype" name="coursetype" onchange="toggleCourseInputs()" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select course type</option>
            <option value="text">Text Course</option>
            <option value="video">Video Course</option>
        </select>
    </div>

    <!-- Text Course Content -->
    <div id="textCourseContent" class="form-group" style="display: none;">
        <label for="documentcourse" class="block text-sm font-medium text-gray-700">Course Content (Text)</label>
        <textarea id="documentcourse" name="documentcourse" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <!-- Video Course Content -->
    <div id="videoCourseContent" class="form-group" style="display: none;">
        <label for="videocourse" class="block text-sm font-medium text-gray-700">Course Video</label>
        <input type="file" name="videocourse" id="videocourse" accept="video/*"
            class="mt-1 block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4
            file:rounded-md file:border-0
            file:text-sm file:font-semibold
            file:bg-indigo-50 file:text-indigo-700
            hover:file:bg-indigo-100">
        <p class="mt-1 text-sm text-gray-500">Upload your course video file (MP4, WebM, etc.)</p>
    </div>

    <!-- Course Thumbnail -->
    <div class="form-group">
        <label for="coursimage" class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
        <input type="file" name="coursimage" id="coursimage" accept="image/*" required
            class="mt-1 block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4
            file:rounded-md file:border-0
            file:text-sm file:font-semibold
            file:bg-indigo-50 file:text-indigo-700
            hover:file:bg-indigo-100">
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Add Course
        </button>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>

   

    <script>
        function openModal() {
            document.getElementById('courseModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('courseModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('courseModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        function toggleCourseInputs() {
        const courseType = document.getElementById('coursetype').value;
        const textContent = document.getElementById('textCourseContent');
        const videoContent = document.getElementById('videoCourseContent');

        // Hide both by default
        textContent.style.display = 'none';
        videoContent.style.display = 'none';

        // Show the appropriate input based on the selected type
        if (courseType === 'text') {
            textContent.style.display = 'block';
        } else if (courseType === 'video') {
            videoContent.style.display = 'block';
        }
    }
    </script>
</body>
</html>