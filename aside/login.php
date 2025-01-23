<?php

session_start();
require_once '../classes/userClasse.php';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $user = User::signin($email, $password);

        // Store user data in session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['name'] = $user->getName();
        $_SESSION['role_id'] = $user->getRoleId();

        // Redirect based on role
        switch ($user->getRoleId()) {
            case User::ROLE_STUDENT:
                header('Location:../front.php');
                break;
            case User::ROLE_TEACHER:
                header('Location: ../teacher/course.php');
                break;
            default:
                // Assuming admin role_id is 3
                if ($user->getRoleId() == 1) {
                    header('Location:../aside/users.php');
                } else {
                    // Fallback for any other roles
                    header('Location: index.php');
                }
                break;
        }
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LearnHub</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-blue-600">LearnHub</h2>
                <h3 class="mt-2 text-xl font-semibold text-gray-900">Welcome Back</h3>
                <p class="mt-2 text-gray-600">Please sign in to your account</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" required
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your email">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" required
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your password">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg
                           text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
                           focus:ring-offset-2 focus:ring-blue-500 font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </button>
            </form>

            <!-- Register Link -->
            <p class="mt-8 text-center text-sm text-gray-600">
                Don't have an account?
                <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                    Register here
                </a>
            </p>
        </div>
    </div>
</body>

</html>