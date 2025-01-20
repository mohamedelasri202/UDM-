<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location:../aside/login.php');
    exit();
}

require_once '../classes/connection.php';
require_once '../classes/coursClasse.php';
require_once '../classes/categorieClasse.php';
require_once '../classes/tags.php';
require_once '../classes/tagscours.php';

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->beginTransaction();

    try {
        error_log("Processing course submission: " . print_r($_POST, true));
        // Validate course type
        if (!isset($_POST['coursetype']) || empty($_POST['coursetype'])) {
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

        $courseId = null;

        // Handle Text Course
        if ($_POST['coursetype'] === 'text') {
            $textCourse = new TextCourse();
            $textCourse->setTitle($_POST['title']);
            $textCourse->setDescription($_POST['description']);
            $textCourse->setPrice($_POST['price']);
            $textCourse->setIdUser($_SESSION['user_id']);
            $textCourse->setIdCategorie($_POST['id_categorie']);
            $textCourse->setCoursImage($imagePath);
            $textCourse->setDocumentCourse($_POST['documentcourse']);
            
            if ($textCourse->addCourse($db)) {
                $courseId = $db->lastInsertId();
            } else {
                throw new Exception("Failed to add text course");
            }
        } 
        // Handle Video Course
        else if ($_POST['coursetype'] === 'video') {
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
                $courseId = $db->lastInsertId();
            } else {
                throw new Exception("Failed to add video course");
            }
        }

        // Handle tags if course was created successfully
              // When creating course tag relationships, add error logging
              if ($courseId && isset($_POST['tag_titles']) && !empty($_POST['tag_titles'])) {
                $tagTitles = array_map('trim', explode(',', $_POST['tag_titles']));
                
                foreach ($tagTitles as $tagTitle) {
                    if (empty($tagTitle)) continue;
                    
                    try {
                        // Create or get existing tag
                        $tag = new Tags(null, $tagTitle);
                        $existingTag = Tags::getTagByTitle($db, $tagTitle);
                        
                        $tagId = null;
                        if ($existingTag) {
                            $tagId = $existingTag->getID();
                        } else {
                            $newTag = $tag->addTags($db);
                            if ($newTag) {
                                $tagId = $newTag->getID();
                            }
                        }
                        
                        if ($tagId) {
                            $courseTag = new CourseTag($courseId, $tagId);
                            if (!$courseTag->addCourseTag($db)) {
                                throw new Exception("Failed to associate tag: $tagTitle with course");
                            }
                        } else {
                            throw new Exception("Failed to create or retrieve tag: $tagTitle");
                        }
                    } catch (Exception $e) {
                        error_log("Error processing tag '$tagTitle': " . $e->getMessage());
                        throw $e;
                    }
                }
            }
    
            $db->commit();
            $_SESSION['success_message'] = "Course and tags added successfully!";
            header('Location: courses.php');
            exit();
    
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error in course creation: " . $e->getMessage());
            $message = "Error: " . $e->getMessage();
            $messageType = "error";
        }
    }
    
    

        // If we got here, everything succeeded
    
  
// Fetch categories for dropdown
try {
    $stmt = $db->prepare("SELECT id, title FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $message = "Error loading categories: " . $e->getMessage();
}

// Fetch existing courses
try {
    $textCourse = new TextCourse();
    $videoCourse = new VideoCourse();

    $textCourses = $textCourse->afficheCourse($db);
    $videoCourses = $videoCourse->afficheCourse($db);

    $allCourses = array_merge($textCourses, $videoCourses);
    usort($allCourses, function($a, $b) {
        return strcmp($b['course']->getTitle(), $a['course']->getTitle());
    });
} catch (PDOException $e) {
    error_log("Error fetching courses: " . $e->getMessage());
    $error = "An error occurred while loading courses.";
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <link rel="stylesheet" href="../style/style.css">
    <style>
        .form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #D1D5DB;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Tag Input Styling */
.tag-input-wrapper {
    margin-bottom: 1rem;
}

.tag-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #D1D5DB;
    border-radius: 0.5rem;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.5rem 0;
}

.tag {
    display: inline-flex;
    align-items: center;
    background-color: #EEF2FF;
    border: 1px solid #E0E7FF;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    color: #4F46E5;
    transition: all 0.2s ease;
}

.tag:hover {
    background-color: #E0E7FF;
}

.tag-remove {
    margin-left: 0.5rem;
    cursor: pointer;
    color: #6366F1;
    border: none;
    background: none;
    font-size: 1.25rem;
    padding: 0 0.25rem;
    line-height: 1;
}

.tag-remove:hover {
    color: #4F46E5;
}

/* File Input Styling */
.file-input-wrapper {
    position: relative;
}

.file-input-label {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background-color: #F3F4F6;
    border: 1px solid #D1D5DB;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.file-input-label:hover {
    background-color: #E5E7EB;
}

.file-input {
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
    position: absolute;
}

/* Submit Button Styling */
.submit-btn {
    background-color: #4F46E5;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.submit-btn:hover {
    background-color: #4338CA;
}

.help-text {
    font-size: 0.875rem;
    color: #6B7280;
    margin-top: 0.25rem;
}
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 700px;
            border-radius: 8px;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .close-btn {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            line-height: 1;
        }

        .close-btn:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        /* Message styles */
        .grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
            padding: 1.5rem;
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .course-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .course-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .course-content p {
            color: #666;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            margin-top: auto;
        }

        .course-meta .price {
            font-weight: 600;
            color: #2563eb;
        }

        .course-meta .type {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .course-meta .type.video {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .course-meta .type.text {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        /* Keep your existing message styles */
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .message.error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .message.success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
         


.tag-input-container {
    margin-top: 5px;
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.tag {
    background-color: #e9ecef;
    border-radius: 3px;
    padding: 5px 10px;
    display: inline-flex;
    align-items: center;
    margin-right: 5px;
}

.tag span {
    margin-right: 5px;
}

.tag button {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 0 5px;
}

.tag button:hover {
    color: #dc3545;
}











    </style>
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

            <?php if (isset($message)): ?>
                <div class="message <?php echo isset($messageType) ? $messageType : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Course Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($allCourses as $courseData): 
                    $course = $courseData['course'];
                    $badgeColor = $course->getCoursType() === 'video' ? 'blue' : 'purple';
                ?>
                <div class="course-card">
                    <div class="course-image">
                        <?php if ($course->getCoursImage()): ?>
                            <img src="<?php echo htmlspecialchars($course->getCoursImage()); ?>" 
                                 alt="<?php echo htmlspecialchars($course->getTitle()); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="course-content">
                        <h3><?php echo htmlspecialchars($course->getTitle()); ?></h3>
                        <p><?php echo htmlspecialchars($course->getDescription()); ?></p>
                        <div class="course-meta">
                            <span class="price">$<?php echo number_format($course->getPrice(), 2); ?></span>
                            <span class="type"><?php echo ucfirst($course->getCoursType()); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Modal -->
            <div id="courseModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Course</h2>
                        <button class="close-btn" onclick="closeModal()">&times;</button>
                    </div>
                    
 <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Course Title</label>
        <input type="text" name="title" id="title" required>
    </div>

    <!-- New Tag Input Section -->
    <div class="form-group">
    <div class="form-group">
    <label for="tag_titles">Tags (comma-separated)</label>
    <input type="text" class="form-control" id="tag_titles" name="tag_titles" placeholder="Enter tags separated by commas">
</div>
            <!-- Hidden input to store tags for form submission -->
            <input type="hidden" name="course_tags" id="courseTagsHidden">
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4" required></textarea>
    </div>

    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" name="price" id="price" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="id_categorie">Category</label>
        <select name="id_categorie" id="id_categorie" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                    <?php echo htmlspecialchars($category['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="coursetype">Course Type</label>
        <select id="coursetype" name="coursetype" onchange="toggleCourseInputs()" required>
            <option value="">Select course type</option>
            <option value="text">Text Course</option>
            <option value="video">Video Course</option>
        </select>
    </div>

    <div id="textCourseContent" class="form-group" style="display: none;">
        <label for="documentcourse">Course Content (Text)</label>
        <textarea id="documentcourse" name="documentcourse" rows="4"></textarea>
    </div>

    <div id="videoCourseContent" class="form-group" style="display: none;">
        <label for="videocourse">Course Video</label>
        <input type="file" name="videocourse" id="videocourse" accept="video/*">
        <p class="help-text">Upload your course video file (MP4, WebM, or Ogg)</p>
    </div>

    <div class="form-group">
        <label for="coursimage">Course Thumbnail</label>
        <input type="file" name="coursimage" id="coursimage" accept="image/*" required>
        <p class="help-text">Upload a course thumbnail image (JPEG, PNG, or GIF)</p>
    </div>

    <button type="submit" class="submit-btn">Add Course</button>
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
            document.body.style.display= 'none';
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

            // Show the appropriate input based on selection
            if (courseType === 'text') {
                textContent.style.display = 'block';
                document.getElementById('documentcourse').setAttribute('required', 'required');
                document.getElementById('videocourse').removeAttribute('required');
            } else if (courseType === 'video') {
                videoContent.style.display = 'block';
                document.getElementById('videocourse').setAttribute('required', 'required');
                document.getElementById('documentcourse').removeAttribute('required');
            }
        }

        // Function to handle course deletion
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

        // Function to preview image before upload
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Add event listener for image preview
        document.getElementById('coursimage').addEventListener('change', function() {
            previewImage(this);
        });

        // Initialize any UI components when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Reset form when modal is closed
            const modal = document.getElementById('courseModal');
            const form = modal.querySelector('form');
            
            document.querySelector('.close-btn').addEventListener('click', function() {
                form.reset();
                const preview = document.getElementById('imagePreview');
                if (preview) {
                    preview.style.display = 'none';
                }
            });

            // Show success message if it exists
            const successMessage = document.querySelector('.message.success');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            }
        });

        // Initialize Feather icons if you're using them
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        document.addEventListener('DOMContentLoaded', function() {
    const tagInput = document.getElementById('tagInput');
    const tagContainer = document.getElementById('tagContainer');
    const hiddenTagInput = document.getElementById('courseTagsHidden');
    let tags = [];

    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = this.value.trim();
            
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                updateTags();
                this.value = '';
            }
        }
    });

    function updateTags() {
        // Update the hidden input with JSON string of tags
        hiddenTagInput.value = JSON.stringify(tags);
        
        // Update the visual representation
        tagContainer.innerHTML = tags.map(tag => `
            <div class="tag">
                <span>${tag}</span>
                <button type="button" onclick="removeTag('${tag}')">&times;</button>
            </div>
        `).join('');
    }

    // Make removeTag function global so onclick can access it
    window.removeTag = function(tagToRemove) {
        tags = tags.filter(tag => tag !== tagToRemove);
        updateTags();
    }
});

    </script>
</body>
</html>