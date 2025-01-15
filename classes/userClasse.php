
<?php 

require_once 'connection.php';





class User {
    private $id;
    private $name;
    private $last_name;
    private $email;
    private $passwordHash;
    private $roleId;

    public function __construct($id = null, $name = null, $last_name = null, $email = null, $passwordHash = null, $roleId = null) {
        $this->id = $id;
        $this-> name= $name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roleId = $roleId;
    }

    public function __toString() {
        return $this->name . " " . $this->last_name;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->name; }
    public function getPrenom() { return $this->last_name; }
    public function getEmail() { return $this->email; }
    public function getRoleId() { return $this->roleId; }

    // Password hashing method
    private function setPasswordHash($password) {
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
    }

    // Save user to database
    public function save() {
        $db = Database::getInstance()->getConnection();
        try {
            if ($this->id) {
                // Update existing user
                $stmt = $db->prepare("UPDATE users SET nom = :nom, prenom = :prenom, email = :email, role_id = :role_id WHERE id = :id");
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
                $stmt->bindParam(':nom', $this->name, PDO::PARAM_STR);
                $stmt->bindParam(':prenom', $this->name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindParam(':role_id', $this->roleId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Insert new user
                $stmt = $db->prepare("INSERT INTO user (name, last_name, email, password, role_id) VALUES (:name, :last_name, :email, :password, :role_id)");
                $stmt->bindParam(':nom', $this->name, PDO::PARAM_STR);
                $stmt->bindParam(':prenom', $this->last_name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $this->passwordHash, PDO::PARAM_STR);
                $stmt->bindParam(':role_id', $this->roleId, PDO::PARAM_INT);
                $stmt->execute();
                $this->id = $db->lastInsertId();
            }
            return $this->id;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("An error occurred while saving the user.");
        }
    }

    // Search user by name
    public static function searchByName($name) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM user WHERE name LIKE :name OR last_name LIKE :name");
        $stmt->bindValue(':name', '%' . $name . '%', PDO::PARAM_STR);
        $stmt->execute();
        
        $users = [];
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $result['id'],
                $result['name'],
                $result['last_name'],
                $result['email'],
                $result['password'],
                $result['role_id']
            );
        }
        return $users;
    }

    // Get user by ID
    public static function getById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

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

    // Find user by email
    public static function findByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
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

    // Registration method (signup)
    public static function signup($name, $last_name, $email, $password, $roleId) {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Validate password length
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }

        // Sanitize input
        $name = htmlspecialchars($name);
        $last_name= htmlspecialchars($last_name);

        // Check if email exists
        if (self::findByEmail($email)) {
            throw new Exception("Email is already registered");
        }

        // Create and save new user
        $user = new User(null, $name, $last_name, $email, null, $roleId);
        $user->setPasswordHash($password);
        return $user->save();
    }

    // Login method (signin)
    public static function signin($email, $password) {
        $user = self::findByEmail($email);
        
        if (!$user || !password_verify($password, $user->passwordHash)) {
            throw new Exception("Invalid email or password");
        }

        return $user;
    }

    // Change password method
    public function changePassword($newPassword) {
        if (strlen($newPassword) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }

        $this->setPasswordHash($newPassword);
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':password', $this->passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }
}