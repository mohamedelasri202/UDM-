


<?php require_once '../classes/categorieClasse.php';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $category = Category::findById($_POST['delete_id']);
    if ($category) {
        if ($category->delete()) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// Handle Save (both new and edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // This is an edit submission
        $category = Category::findById($_POST['id']);
        if ($category) {
            $category->setTitle($_POST['title']);
            $category->setDescription($_POST['description']);
        }
    } else {
        // This is a new category
        $category = new Category(
            null,
            $_POST['title'],
            $_POST['description']
        );
    }

    if ($category->save()) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Get all categories for display
$categories = Category::getAll();



?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Categories Management</title>
    <style>
       
            body {
            font-family: 'Inter', sans-serif;
        }
        .slide-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            transition: right 0.3s ease;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .slide-panel.open {
            right: 0;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }

        .overlay.show {
            display: block;
        }
    </style>
    </style>
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex">
   <!-- Sidebar -->
<div class="w-64 bg-white shadow-lg p-4 flex flex-col">
    <div class="flex items-center space-x-3 mb-8">
        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
            <i data-feather="user" class="text-white"></i>
        </div>
        <div>
            <h2 class="font-semibold text-gray-800">Henry Klein</h2>
            <p class="text-sm text-gray-500">Administrator</p>
        </div>
    </div>
     
    <nav class="space-y-2 flex-1">
        <!-- Users Section -->
        <div class="text-gray-800 font-medium px-4 py-2 mb-2">Main Menu</div>
        
        <a href="users.php" class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-lg flex items-center space-x-3 font-medium">
            <i data-feather="users" class="w-4 h-4"></i>
            <span>Users</span>
        </a>

        <!-- Courses Section -->
        <a href="courses.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
            <i data-feather="book-open" class="w-4 h-4"></i>
            <span>Courses</span>
        </a>

        <!-- Categories Section -->
        <a href="categories.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
            <i data-feather="folder" class="w-4 h-4"></i>
            <span>Categories</span>
        </a>

        <!-- Tags Section -->
        <a href="tags.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
            <i data-feather="tag" class="w-4 h-4"></i>
            <span>Tags</span>
        </a>

        <!-- Statistics Section -->
        <a href="" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
            <i data-feather="bar-chart-2" class="w-4 h-4"></i>
            <span>Statistics</span>
        </a>
    </nav>

    <!-- Logout Button -->
    <a href="/logout" class="mt-4 flex items-center space-x-2 text-gray-600 hover:text-red-600 px-4 py-2.5 rounded-lg transition-colors">
        <i data-feather="log-out" class="w-4 h-4"></i>
        <span>Logout</span>
    </a>
</div>



        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">12</h3>
                            <p class="text-green-500 flex items-center">
                                <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                                +2
                            </p>
                            <p class="text-gray-500 font-medium">Total Categories</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                            <i data-feather="folder" class="text-blue-500"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">156</h3>
                            <p class="text-green-500 flex items-center">
                                <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                                +12%
                            </p>
                            <p class="text-gray-500 font-medium">Active Courses</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                            <i data-feather="book-open" class="text-green-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">2,845</h3>
                            <p class="text-blue-500 flex items-center">
                                <i data-feather="users" class="w-4 h-4 mr-1"></i>
                                Total
                            </p>
                            <p class="text-gray-500 font-medium">Enrolled Students</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-50 rounded-full flex items-center justify-center">
                            <i data-feather="users" class="text-purple-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">45</h3>
                            <p class="text-yellow-500 flex items-center">
                                <i data-feather="star" class="w-4 h-4 mr-1"></i>
                                Active
                            </p>
                            <p class="text-gray-500 font-medium">Featured Categories</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center">
                            <i data-feather="award" class="text-yellow-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800">Categories List</h3>
                        <button id="addCategoryBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-blue-600 transition-colors">
                            <i data-feather="plus" class="w-4 h-4"></i>
                            <span>Add Category</span>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm font-medium text-gray-500">
                                <th class="pb-4 pr-6">Title</th>
                                <th class="pb-4 pr-6">Description</th>
                                <th class="pb-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            <?php foreach ($categories as $category): ?>
                                <tr class="border-t border-gray-100">
                                    <td class="py-4 pr-6"><?php echo htmlspecialchars($category->getTitle()); ?></td>
                                    <td class="py-4 pr-6"><p class="truncate w-96"><?php echo htmlspecialchars($category->getDescription()); ?></p></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button onclick="editCategory(<?php echo $category->getId(); ?>, '<?php echo addslashes($category->getTitle()); ?>', '<?php echo addslashes($category->getDescription()); ?>')" 
                                                    class="text-blue-500 hover:text-blue-600">
                                                <i data-feather="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <form action="" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                <input type="hidden" name="delete_id" value="<?php echo $category->getId(); ?>">
                                                <button type="submit" class="text-red-500 hover:text-red-600">
                                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 
   
</div>
<div id="slidePanel" class="slide-panel">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 id="slidePanelTitle" class="text-xl font-semibold text-gray-800">Add New Category</h3>
                <button id="closePanelBtn" class="text-gray-500 hover:text-gray-700">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form id="categoryForm" class="space-y-4" method="post">
                <input type="hidden" id="categoryId" name="id" value="">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Initialize Feather Icons
        feather.replace();

        // Get DOM elements
        const addButton = document.getElementById('addCategoryBtn');
        const closeButton = document.getElementById('closePanelBtn');
        const slidePanel = document.getElementById('slidePanel');
        const overlay = document.getElementById('overlay');
        const categoryForm = document.getElementById('categoryForm');
        const slidePanelTitle = document.getElementById('slidePanelTitle');

        // Open panel function
        function openPanel() {
            slidePanel.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Close panel function
        function closePanel() {
            slidePanel.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            categoryForm.reset();
            document.getElementById('categoryId').value = '';
            slidePanelTitle.textContent = 'Add New Category';
        }

        // Edit category function
        function editCategory(id, title, description) {
            document.getElementById('categoryId').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description;
            slidePanelTitle.textContent = 'Edit Category';
            openPanel();
        }

        // Event listeners
        addButton.addEventListener('click', openPanel);
        closeButton.addEventListener('click', closePanel);
        overlay.addEventListener('click', closePanel);

        // Close panel on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePanel();
            }
        });
    </script>
    <div id="overlay" class="overlay"></div>
</body>
</html>