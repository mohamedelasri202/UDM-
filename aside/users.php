<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location:../aside/login.php');
    exit();
}
require_once '../classes/userClasse.php';
// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($userId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $result['id'],
                $result['name'],
                $result['last_name'],
                $result['email'],
                $result['password'],
                $result['role_id'],
                $result['status']
            );

            switch ($action) {
                case 'toggle_status':
                    $user->toggleStatus();
                    break;
                case 'delete':
                    $user->delete();
                    break;
            }
        }
    }
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
$users = User::getAllUsers()
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>User Management</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
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
            <a href="../teacher/logout.php" class="mt-4 flex items-center space-x-2 text-gray-600 hover:text-red-600 px-4 py-2.5 rounded-lg transition-colors">
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
                            <h3 class="text-2xl font-bold text-gray-800">2,356</h3>
                            <p class="text-green-500 flex items-center">
                                <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                                +3.5%
                            </p>
                            <p class="text-gray-500 font-medium">Total Users</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                            <i data-feather="users" class="text-blue-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">1,245</h3>
                            <p class="text-green-500 flex items-center">
                                <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                                +11%
                            </p>
                            <p class="text-gray-500 font-medium">Students</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                            <i data-feather="book" class="text-green-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">145</h3>
                            <p class="text-red-500 flex items-center">
                                <i data-feather="trending-down" class="w-4 h-4 mr-1"></i>
                                -2.4%
                            </p>
                            <p class="text-gray-500 font-medium">Teachers</p>
                        </div>
                        <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                            <i data-feather="briefcase" class="text-red-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">23</h3>
                            <p class="text-yellow-500 flex items-center">
                                <i data-feather="alert-circle" class="w-4 h-4 mr-1"></i>
                                Active
                            </p>
                            <p class="text-gray-500 font-medium">Suspended Users</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center">
                            <i data-feather="user-x" class="text-yellow-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800">Users List</h3>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm font-medium text-gray-500">
                                <th class="pb-4 pr-6">Name</th>
                                <th class="pb-4 pr-6">Email</th>
                                <th class="pb-4 pr-6">Status</th>
                                <th class="pb-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            <?php foreach ($users as $user): ?>
                                <tr class="border-t border-gray-100">
                                    <td class="py-4 pr-6"><?php echo htmlspecialchars($user->getName() . " " . $user->getLastName()); ?></td>
                                    <td class="py-4 pr-6"><?php echo htmlspecialchars($user->getEmail()); ?></td>
                                    <td class="py-4 pr-6">
                                        <?php if ($user->getStatus() === 'activated'): ?>
                                            <span class="px-3 py-1 text-xs font-medium text-green-600 bg-green-50 rounded-full">Active</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 text-xs font-medium text-yellow-600 bg-yellow-50 rounded-full">Suspended</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <form method="post" class="inline" onsubmit="return confirm('Are you sure you want to toggle this user\'s status?');">
                                                <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <button type="submit" class="<?php echo $user->getStatus() === 'activated' ? 'text-yellow-500 hover:text-yellow-600' : 'text-green-500 hover:text-green-600'; ?>">
                                                    <i data-feather="<?php echo $user->getStatus() === 'activated' ? 'pause' : 'play'; ?>" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                            <form method="post" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                                <input type="hidden" name="action" value="delete">
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
    <script>
        feather.replace();
    </script>
</body>

</html>