<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct($dsn, $username, $password) {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed.");
        }
    }

    public static function getInstance($dsn = null, $username = null, $password = null) {
        if (self::$instance === null) {
            $dsn = $dsn ?? 'mysql:host=localhost;dbname=udm';
            $username = $username ?? 'root';
            $password = $password ?? '';
            self::$instance = new Database($dsn, $username, $password);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

class User {
    private $id;
    private $name;
    private $lastName;
    private $email;
    private $password;
    private $roleId;

    public function __construct($id = null, $name = null, $lastName = null, $email = null, $password = null, $roleId = null) {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->roleId = $roleId;
    }

    // Registration method
    public static function signup($name, $lastName, $email, $password, $roleId) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }

        $name = htmlspecialchars($name);
        $lastName = htmlspecialchars($lastName);

        // Check if email already exists
        if (self::findByEmail($email)) {
            throw new Exception("Email is already registered");
        }

        try {
            $db = Database::getInstance()->getConnection();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO user (name, last_name, email, password, role_id) 
                    VALUES (:name, :last_name, :email, :password, :role_id)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'name' => $name,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $hashedPassword,
                'role_id' => $roleId
            ]);
            
            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Error during registration");
        }
    }  
    public function getId() {
        return $this->id;
    }
    
    
    public function getEmail() {
        return $this->email;
    }
    public function getroleid(){
        return $this->roleId;
    }

    // Login method
    public static function signin($email, $password ,$role_id) {
        $user = self::findByEmail($email);
        
        if (!$user || !password_verify($password, $user->password)) {
            throw new Exception("Invalid email or password");
        }

        return $user;
    }

    // Find user by email
    public static function findByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new User(
                $result['id'],
                $result['name'],
                $result['last_name'],
                $result['email'],
                $result['password'],
                $result['role_id']
            );
        }

        return null;
    }
}

// login.php

$message = '';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (
            isset($_POST['email'], $_POST['password']) &&
            !empty($_POST['email']) && !empty($_POST['password'])
        ) {
            $user = User::signin($_POST['email'], $_POST['password']);
            
            // Set session variables
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_email']= $user->getEmail();
            $_SESSION['roleid']=$user->getroleid();
            if ($roleId ==1){
                header("location:../index.php");

            }elseif($roleId == 2){
             header ("location:courses.php");
            }else{
                header ("location:categories.php");            }
            exit();
        }else {
            $message = "All fields are required";
        }
    } catch(Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
>