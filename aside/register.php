<?php
// register.php
session_start();
require_once '../classes/userClasse.php';

$message = '';

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (
            isset($_POST['name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['role_id']) &&
            !empty($_POST['name']) && !empty($_POST['last_name']) && !empty($_POST['email']) &&
            !empty($_POST['password']) && !empty($_POST['role_id'])
        ) {
            $userId = User::signup(
                $_POST['name'],
                $_POST['last_name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role_id']
            );

            $message = "Registration successful! ";
            $message .= $_POST['role_id'] == User::ROLE_TEACHER
                ? "Your account is pending approval."
                : "You can now login to your account.";
        } else {
            $message = "All fields are required for registration";
        }
    }
} catch (Exception $e) {
    $message = "Registration Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LearnHub</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-blue-600">LearnHub</h2>
                <h3 class="mt-2 text-xl font-semibold text-gray-900">Create your account</h3>
                <p class="mt-2 text-gray-600">Join our learning community today</p>
            </div>

            <?php if (!empty($message)) : ?>
                <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'success') !== false ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" onsubmit="return validateForm()" class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="name" name="name" required
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your first name">
                    </div>
                </div>

                <!-- Last Name Field -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="last_name" name="last_name" required
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your last name">
                    </div>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your email">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Create a password">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Must be at least 6 characters</p>
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user-tag text-gray-400"></i>
                        </div>
                        <select id="role_id" name="role_id" required
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg appearance-none
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="3">Student</option>
                            <option value="2">Teacher</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg
                           text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
                           focus:ring-offset-2 focus:ring-blue-500 font-medium">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <p class="mt-8 text-center text-sm text-gray-600">
                Already have an account?
                <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">
                    Login here
                </a>
            </p>
        </div>
    </div>

    <script>
        function validateForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Basic email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert('Please enter a valid email address');
                return false;
            }

            // Password validation (minimum 6 characters)
            if (password.length < 6) {
                alert('Password must be at least 6 characters long');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>