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
    <title>Login</title>
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
        input {
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
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
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
        <h2>Login</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</body>
</html>