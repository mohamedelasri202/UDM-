<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <title>Courses Page</title>
    <style>
        .bg-custom-blue {
            background-color: #3b82f6; /* Tailwind blue-500 */
        }
        .fixed-size {
            width: 100px; /* Fixed width */
            height: 100px; /* Fixed height */
            object-fit: cover; /* Maintain aspect ratio */
        }
        .modal {
            display: none; /* Hidden by default */
        }
        .modal.show {
            display: flex; /* Show when active */
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
            <h1 class="text-3xl font-bold text-custom-blue mb-6">Courses</h1>
            <button id="addCourseBtn" class="bg-custom-blue text-white px-4 py-2 rounded-lg mb-4">Add Course</button>
            
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-custom-blue text-white">
                        <th class="py-2 px-4">Photo</th>
                        <th class="py-2 px-4">Title</th>
                        <th class="py-2 px-4">Description</th>
                        <th class="py-2 px-4">Continue</th>
                        <th class="py-2 px-4">Price</th>
                        <th class="py-2 px-4">Categories</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example Row -->
                    <tr class="text-gray-700">
                        <td class="py-2 px-4"><img src="example.jpg" alt="Course Image" class="fixed-size"></td>
                        <td class="py-2 px-4">Course Title</td>
                        <td class="py-2 px-4">Course Description</td>
                        <td class="py-2 px-4"><a href="#" class="text-blue-600">Continue</a></td>
                        <td class="py-2 px-4">$99.99</td>
                        <td class="py-2 px-4">Category 1</td>
                        <td class="py-2 px-4">
                            <button class="text-blue-600">Edit</button>
                            <button class="text-red-600">Delete</button>
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>

            <!-- Modal Form -->
            <div id="courseForm" class="modal fixed inset-0 bg-gray-800 bg-opacity-50 justify-center items-center">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h2 class="text-xl font-bold mb-4">Add/Edit Course</h2>
                    <form id="form">
                        <label class="block mb-2">Title</label>
                        <input type="text" class="border border-gray-300 rounded-lg w-full mb-4 p-2" required>
                        
                        <label class="block mb-2">Description</label>
                        <textarea class="border border-gray-300 rounded-lg w-full mb-4 p-2" required></textarea>
                        
                        <label class="block mb-2">Photo</label>
                        <input type="file" class="border border-gray-300 rounded-lg w-full mb-4" required>
                        
                        <label class="block mb-2">Price</label>
                        <input type="number" class="border border-gray-300 rounded-lg w-full mb-4 p-2" required>
                        
                        <label class="block mb-2">Categories</label>
                        <select class="border border-gray-300 rounded-lg w-full mb-4 p-2" required>
                            <option value="">Select Category</option>
                            <option value="category1">Category 1</option>
                            <option value="category2">Category 2</option>
                        </select>
                        
                        <label class="block mb-2">Continue</label>
                        <input type="text" class="border border-gray-300 rounded-lg w-full mb-4 p-2" required>
                        
                        <button type="submit" class="bg-custom-blue text-white px-4 py-2 rounded-lg">Save</button>
                        <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-lg" onclick="closeForm()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        feather.replace();

        // Show the form
        document.getElementById('addCourseBtn').addEventListener('click', function() {
            document.getElementById('courseForm').classList.add('show');
            document.getElementById('courseForm').style.display = 'flex'; // Show modal
        });

        // Close the form
        function closeForm() {
            document.getElementById('courseForm').style.display = 'none'; // Hide modal
        }
    </script>
</body>
</html>
