<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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
        header('Location: course.php');
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
    usort($allCourses, function ($a, $b) {
        return strcmp($b['course']->getTitle(), $a['course']->getTitle());
    });
} catch (PDOException $e) {
    error_log("Error fetching courses: " . $e->getMessage());
    $error = "An error occurred while loading courses.";
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="cours.css">

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
                    <a href="logout.php">Logout</a>

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
            <div class="grid">
                <?php foreach ($allCourses as $courseData):
                    $course = $courseData['course'];
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

                            <div class="course-tags">
                                <span class="tag tag-blue">Tag 1</span>
                                <span class="tag tag-green">Tag 2</span>
                                <span class="tag tag-yellow">Tag 3</span>
                            </div>

                            <div class="course-meta">
                                <span class="price">$<?php echo number_format($course->getPrice(), 2); ?></span>
                                <span class="type <?php echo $course->getCoursType() === 'video' ? 'tag-blue' : 'tag-purple'; ?>">
                                    <?php echo ucfirst($course->getCoursType()); ?>
                                </span>
                            </div>
                            <a href="coursedeta" class="view-details-btn">View Details</a>
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

                        <div class="form-group">
                            <label for="tag_titles">Tags (comma-separated)</label>
                            <input type="text" class="form-control" id="tag_titles" name="tag_titles" placeholder="Enter tags separated by commas">
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

                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Add Course</button>
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
            // Remove this line that's causing issues:
            // document.body.style.display = 'none';
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
        // function deleteCourse(courseId) {
        //     if (confirm('Are you sure you want to delete this course?')) {
        //         fetch(`delete_course.php?id=${courseId}`, {
        //                 method: 'DELETE',
        //                 credentials: 'same-origin'
        //             })
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     location.reload();
        //                 } else {
        //                     alert('Error deleting course: ' + data.message);
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error('Error:', error);
        //                 alert('Error deleting course. Please try again.');
        //             });
        //     }
        // }

        // Function to preview image before upload
        // function previewImage(input) {
        //     if (input.files && input.files[0]) {
        //         const reader = new FileReader();

        //         reader.onload = function(e) {
        //             const preview = document.getElementById('imagePreview');
        //             preview.src = e.target.result;
        //             preview.style.display = 'block';
        //         }

        //         reader.readAsDataURL(input.files[0]);
        //     }
        // }

        // Add event listener for image preview
        // document.getElementById('coursimage').addEventListener('change', function() {
        //     previewImage(this);
        // });

        // Initialize any UI components when the page loads
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Reset form when modal is closed
        //     const modal = document.getElementById('courseModal');
        //     const form = modal.querySelector('form');

        //     document.querySelector('.close-btn').addEventListener('click', function() {
        //         form.reset();
        //         const preview = document.getElementById('imagePreview');
        //         if (preview) {
        //             preview.style.display = 'none';
        //         }
        //     });

        //     // Show success message if it exists
        //     const successMessage = document.querySelector('.message.success');
        //     if (successMessage) {
        //         setTimeout(() => {
        //             successMessage.style.display = 'none';
        //         }, 5000);
        //     }
        // });

        // Initialize Feather icons if you're using them
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        document.addEventListener('DOMContentLoaded', function() {
            const tagInput = document.getElementById('tagInput');
            const tagContainer = document.getElementById('tagContainer');
            const hiddenTagInput = document.getElementById('courseTagsHidden');

            // Only run this code if the elements exist
            if (tagInput && tagContainer && hiddenTagInput) {
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
                    hiddenTagInput.value = JSON.stringify(tags);
                    tagContainer.innerHTML = tags.map(tag => `
                <div class="tag">
                    <span>${tag}</span>
                    <button type="button" onclick="removeTag('${tag}')">&times;</button>
                </div>
            `).join('');
                }

                // Make removeTag function global
                window.removeTag = function(tagToRemove) {
                    tags = tags.filter(tag => tag !== tagToRemove);
                    updateTags();
                }
            }
        });
    </script>
</body>

</html>