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
            $message = "User registered successfully with ID: " . $userId;
            
            // Optional: Redirect to login page after successful registration
            // header("Location: login.php");
            // exit();
        } else {
            $message = "All fields are required for registration";
        }
    }
} catch(Exception $e) {
    $message = "Registration Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .required {
            color: red;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'Error') !== false) ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
            <div class="form-group">
                <label>First Name <span class="required">*</span></label>
                <input type="text" name="name" id="name" required>
            </div>
            
            <div class="form-group">
                <label>Last Name <span class="required">*</span></label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
            
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <div class="form-group">
                <label>Role <span class="required">*</span></label>
                <select name="role_id" id="role_id" required>
                    <option value="">Select Role</option>
                    <option value="1">Admin</option>
                    <option value="2">User</option>
                </select>
            </div>
            
            <button type="submit">Register</button>
        </form>
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

