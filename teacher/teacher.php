<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <title>Teacher Dashboard</title>
    <style>
        .bg-custom-blue {
            background-color: #3b82f6; /* Tailwind blue-500 */
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-custom-blue shadow-lg h-screen p-4">
            <h2 class="text-2xl font-semibold text-white mb-6">Teacher Dashboard</h2>
            <nav class="space-y-4">
                <a href="courses.php" class="flex items-center text-white hover:bg-blue-600 px-4 py-2 rounded-lg">
                    <i data-feather="book" class="mr-2"></i> Courses
                </a>
                <a href="students.php" class="flex items-center text-white hover:bg-blue-600 px-4 py-2 rounded-lg">
                    <i data-feather="users" class="mr-2"></i> Students
                </a>
                <a href="registration.php" class="flex items-center text-white hover:bg-blue-600 px-4 py-2 rounded-lg">
                    <i data-feather="edit-3" class="mr-2"></i> Registration
                </a>
                <a href="tags.php" class="flex items-center text-white hover:bg-blue-600 px-4 py-2 rounded-lg">
                    <i data-feather="tag" class="mr-2"></i> Tags
                </a>
            </nav>
            <a href="/logout" class="mt-6 block text-red-200 hover:bg-red-600 px-4 py-2 rounded-lg">
                <i data-feather="log-out" class="mr-2"></i> Logout
            </a>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <h1 class="text-3xl font-bold text-custom-blue mb-6">Welcome, Teacher!</h1>
            <p class="text-gray-700">Use the sidebar to navigate through your dashboard.</p>
            <!-- Add more content here -->
        </div>
    </div>
    
    <script>
        // Initialize Feather Icons
        feather.replace();
    </script>
</body>
</html>
