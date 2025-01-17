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
        // Create new text course object
        $textCourse = new TextCourse();
        
        // Validate category
        if (!isset($_POST['id_categorie'])) {
            throw new Exception("Category is required");
        }
        
        // Set basic course properties
        $textCourse->setTitle($_POST['title'] ?? '');
        $textCourse->setDescription($_POST['description'] ?? '');
        $textCourse->setPrice($_POST['price'] ?? 0);
        $textCourse->setIdUser($_SESSION['user_id']);
        $textCourse->setIdCategorie($_POST['id_categorie']);
        
        // Handle image upload
        if (isset($_FILES['coursimage']) && $_FILES['coursimage']['error'] === 0) {
            $uploadDir = 'uploads/images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $imageName = uniqid() . '_' . basename($_FILES['coursimage']['name']);
            $imagePath = $uploadDir . $imageName;
            
            if (move_uploaded_file($_FILES['coursimage']['tmp_name'], $imagePath)) {
                $textCourse->setCoursImage($imagePath);
            }
        }
        
        // Handle course content (text)
        if (isset($_POST['documentcourse']) && !empty(trim($_POST['documentcourse']))) {
            $textCourse->setDocumentCourse($_POST['documentcourse']);
        } else {
            throw new Exception("Course content is required");
        }
        
       
        if ($textCourse->addCourse($db)) {
            $_SESSION['success_message'] = "Course added successfully!";
            header('Location: courses.php'); 
            exit();
        } else {
            throw new Exception("Error adding course to database");
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        error_log("Error in course creation: " . $e->getMessage());
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --bg-light: #f8f9fa;
            --text-dark: #2b2d42;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background-color: white;
            padding: 2rem;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 2rem;
        }

        .nav-item {
            padding: 0.75rem 1rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-item:hover {
            background-color: var(--bg-light);
            color: var(--primary);
        }

        .nav-item.active {
            background-color: var(--primary);
            color: white;
        }

        .main-content {
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .add-course-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .add-course-btn:hover {
            background-color: var(--secondary);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 12px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }

        button[type="submit"]:hover {
            background-color: var(--secondary);
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
            
            <div id="courseModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Course</h2>
                        <button class="close-btn" onclick="closeModal()">&times;</button>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" id="price" name="price" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="id_categorie">Category:</label>
                            <select id="id_categorie" name="id_categorie" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                        <?php echo htmlspecialchars($category['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="coursimage">Course Image:</label>
                            <input type="file" id="coursimage" name="coursimage" accept="image/*" required>
                        </div>

                        <div class="form-group">
                            <label for="documentcourse">Course Content:</label>
                            <textarea id="documentcourse" name="documentcourse" required></textarea>
                        </div>

                        <button type="submit">Add Course</button>
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
    </script>
</body>
</html>