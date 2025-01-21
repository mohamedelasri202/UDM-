<?php
// Process form submission


session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location:../aside/login.php');

    exit();
}
require_once '../classes/connection.php';
require_once '../classes/coursClasse.php';
require_once '../classes/categorieClasse.php';
$db = Database::getInstance()->getConnection();
$message = '';
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {



    try {
        // Validate file types
        $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];

        $videoFileType = $_FILES["videocourse"]["type"];
        $imageFileType = $_FILES["coursimage"]["type"];

        if (!in_array($videoFileType, $allowedVideoTypes)) {
            throw new Exception("Invalid video file type. Please upload MP4, WebM, or Ogg video.");
        }

        if (!in_array($imageFileType, $allowedImageTypes)) {
            throw new Exception("Invalid image file type. Please upload JPEG, PNG, or GIF image.");
        }

        // Handle course image upload
        $targetImageDir = "uploads/images/";
        $imageFileName = basename($_FILES["coursimage"]["name"]);
        $targetImagePath = $targetImageDir . time() . '_' . $imageFileName;

        // Handle video upload
        $targetVideoDir = "uploads/videos/";
        $videoFileName = basename($_FILES["videocourse"]["name"]);
        $targetVideoPath = $targetVideoDir . time() . '_' . $videoFileName;

        if (
            move_uploaded_file($_FILES["coursimage"]["tmp_name"], $targetImagePath) &&
            move_uploaded_file($_FILES["videocourse"]["tmp_name"], $targetVideoPath)
        ) {

            // Create new video course
            $videoCourse = new VideoCourse(
                null,
                $_POST['title'],
                $_POST['description'],
                $_POST['price'],
                $_POST['id_user'],
                $_POST['id_categorie'],
                $targetImagePath,
                'video',
                $targetVideoPath
            );

            if ($videoCourse->addCourse($db)) {
                $message = "Course added successfully!";
                $messageType = "success";
            } else {
                $message = "Error adding course.";
                $messageType = "error";
            }
        } else {
            $message = "Error uploading files.";
            $messageType = "error";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Fetch categories for dropdown
$categoryQuery = "SELECT id, title FROM categories";
$categories = $db->query($categoryQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Video Course</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">Add New Video Course</h1>

        <?php if (isset($message)): ?>
            <div class="mb-4 p-4 rounded <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
                <input type="text" name="title" id="title" required minlength="3" maxlength="255"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" required minlength="10"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" name="price" id="price" step="0.01" min="0" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="videocourse" class="block text-sm font-medium text-gray-700">Course Video</label>
                <input type="file" name="videocourse" id="videocourse" required
                    accept=".mp4,.webm,.ogg,video/mp4,video/webm,video/ogg"
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100">
                <p class="mt-1 text-sm text-gray-500">Accepted formats: MP4, WebM, Ogg</p>
            </div>

            <div>
                <label for="coursimage" class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
                <input type="file" name="coursimage" id="coursimage" required
                    accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif"
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100">
                <p class="mt-1 text-sm text-gray-500">Accepted formats: JPEG, PNG, GIF</p>
            </div>

            <div>
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

            <input type="hidden" name="id_user" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '1'; ?>">

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Add Course
                </button>
            </div>
        </form>
    </div>
</body>

</html>

<script>
    // Client-side validation for video file
    document.querySelector('form').addEventListener('submit', function(e) {
        const videoInput = document.getElementById('videocourse');
        const videoFile = videoInput.files[0];

        if (videoFile) {
            const allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
            if (!allowedTypes.includes(videoFile.type)) {
                e.preventDefault();
                alert('Please upload a valid video file (MP4, WebM, or Ogg).');
                return false;
            }
        }
    });
</script>
</body>

</html>