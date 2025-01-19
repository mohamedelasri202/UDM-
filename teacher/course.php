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
try {
   
    
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





            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (isset($error)): ?>
        <div class="col-span-3 bg-red-50 text-red-600 p-4 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php foreach ($allCourses as $courseData): 
        $course = $courseData['course'];
        $badgeColor = $course->getCoursType() === 'video' ? 'blue' : 'purple';
    ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
        <div class="relative">
            <?php if ($course->getCoursImage()): ?>
                <img src="<?php echo htmlspecialchars($course->getCoursImage()); ?>" 
                     alt="<?php echo htmlspecialchars($course->getTitle()); ?>" 
                     class="w-full h-48 object-cover">
            <?php else: ?>
                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                    <i data-feather="<?php echo $course->getCoursType() === 'video' ? 'video' : 'file-text'; ?>" 
                       class="w-8 h-8 text-gray-400"></i>
                </div>
            <?php endif; ?>
            
            <!-- Course Type Badge -->
            <span class="absolute top-4 right-4 bg-<?php echo $badgeColor; ?>-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                <?php echo ucfirst($course->getCoursType()); ?> Course
            </span>

            <!-- Category Badge -->
            <?php if (isset($courseData['category_name'])): ?>
            <span class="absolute top-4 left-4 bg-white/90 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">
                <?php echo htmlspecialchars($courseData['category_name']); ?>
            </span>
            <?php endif; ?>
        </div>

        <div class="p-6">
            <h3 class="font-semibold text-lg text-gray-800 mb-2">
                <?php echo htmlspecialchars($course->getTitle()); ?>
            </h3>
            
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                <?php echo htmlspecialchars($course->getDescription()); ?>
            </p>

            <!-- Course Details -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <i data-feather="dollar-sign" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-sm font-medium text-gray-900">
                            <?php echo number_format($course->getPrice(), 2); ?>
                        </span>
                    </div>
                    <?php if ($course instanceof TextCourse && $course->getDocumentCourse()): ?>
                    <div class="flex items-center space-x-2">
                        <i data-feather="file-text" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-sm text-gray-600">Document</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($course instanceof VideoCourse && $course->getVideoCourse()): ?>
                    <div class="flex items-center space-x-2">
                        <i data-feather="video" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-sm text-gray-600">Video</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Author Information -->
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center">
                        <i data-feather="user" class="w-3 h-3 text-gray-500"></i>
                    </div>
                    <span class="text-sm text-gray-600">
                        <?php echo htmlspecialchars($courseData['author'] ?? 'Unknown Author'); ?>
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <a href="view_course.php?id=<?php echo $course->getId(); ?>" 
                   class="flex-1 bg-gray-50 text-gray-700 py-2 rounded-lg hover:bg-gray-100 transition-colors text-center">
                    View Details
                </a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $course->getIdUser()): ?>
                <div class="flex space-x-2">
                    <a href="edit_course.php?id=<?php echo $course->getId(); ?>" 
                       class="px-4 bg-blue-50 text-blue-600 py-2 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-feather="edit-2" class="w-4 h-4"></i>
                    </a>
                    <button onclick="deleteCourse(<?php echo $course->getId(); ?>)"
                            class="px-4 bg-red-50 text-red-600 py-2 rounded-lg hover:bg-red-100 transition-colors">
                        <i data-feather="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
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