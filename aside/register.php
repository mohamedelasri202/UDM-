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
<?php if (!empty($message)) : ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <form method="POST" action="register.php">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div>
            <label for="role_id">Role:</label>
            <select id="role_id" name="role_id" required>
                <option value="2">Student</option>
                <option value="1">Teacher</option>
            </select>
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <p>Already have an account? <a href="login.php">Login here</a></p>

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

