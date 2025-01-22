<?php
// User.php
require_once 'connection.php';

class User
{
    private $id;
    private $name;
    private $lastName;
    private $email;
    private $passwordHash;
    private $roleId;
    private $status;

    const ROLE_TEACHER = 2;
    const ROLE_STUDENT = 3;
    const STATUS_WAITING = 'waiting';
    const STATUS_ACTIVATED = 'activated';

    public function __construct($id = null, $name = null, $lastName = null, $email = null, $passwordHash = null, $roleId = null, $status = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roleId = $roleId;
        $this->status = $status;
    }

    public function __toString()
    {
        return $this->name . " " . $this->lastName;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLastName()
    {
        return $this->lastName;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getRoleId()
    {
        return $this->roleId;
    }
    public function getStatus()
    {
        return $this->status;
    }

    private function setPasswordHash($password)
    {
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
    }

    private function determineStatus($roleId)
    {
        return $roleId == self::ROLE_TEACHER ? self::STATUS_WAITING : self::STATUS_ACTIVATED;
    }
    function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // Function to get full name of logged in user
    function getLoggedInUserName()
    {
        if (isset($_SESSION['name']) && isset($_SESSION['last_name'])) {
            return $_SESSION['name'] . ' ' . $_SESSION['last_name'];
        }
        return '';
    }

    public function save()
    {
        $db = Database::getInstance()->getConnection();
        try {
            if ($this->id) {
                $stmt = $db->prepare("UPDATE user SET name = :name, last_name = :last_name, email = :email, role_id = :role_id, status = :status WHERE id = :id");
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            } else {
                $stmt = $db->prepare("INSERT INTO user (name, last_name, email, password, role_id, status) VALUES (:name, :last_name, :email, :password, :role_id, :status)");
                $stmt->bindParam(':password', $this->passwordHash, PDO::PARAM_STR);
            }

            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $this->lastName, PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $this->roleId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
            $stmt->execute();

            if (!$this->id) {
                $this->id = $db->lastInsertId();
            }
            return $this->id;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("An error occurred while saving the user.");
        }
    }
    public static function getAllUsers()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM user");
        $stmt->execute();

        $users = [];
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $result['id'],
                $result['name'],
                $result['last_name'],
                $result['email'],
                $result['password'],
                $result['role_id'],
                $result['status']
            );
        }
        return $users;
    }

    public static function signup($name, $lastName, $email, $password, $roleId)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }

        $name = htmlspecialchars($name);
        $lastName = htmlspecialchars($lastName);

        if (self::findByEmail($email)) {
            throw new Exception("Email is already registered");
        }

        $status = $roleId == self::ROLE_TEACHER ? self::STATUS_WAITING : self::STATUS_ACTIVATED;

        $user = new User(null, $name, $lastName, $email, null, $roleId, $status);
        $user->setPasswordHash($password);
        return $user->save();
    }

    public static function signin($email, $password)
    {
        $user = self::findByEmail($email);

        if (!$user) {
            throw new Exception("Invalid email or password");
        }

        if (!password_verify($password, $user->passwordHash)) {
            throw new Exception("Invalid email or password");
        }

        if ($user->status !== self::STATUS_ACTIVATED) {
            throw new Exception("Your account is pending approval");
        }

        return $user;
    }

    public static function findByEmail($email)
    {
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
                $result['role_id'],
                $result['status']
            );
        }
        return null;
    }
    // Add these methods to your User.php class

    // Delete user
    public function delete()
    {
        $db = Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("DELETE FROM user WHERE id = :id");
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
            throw new Exception("Error deleting user");
        }
    }

    // Toggle user status
    public function toggleStatus()
    {
        $newStatus = $this->status === self::STATUS_ACTIVATED ? 'suspended' : self::STATUS_ACTIVATED;

        $db = Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("UPDATE user SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $newStatus, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->status = $newStatus;
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Status update error: " . $e->getMessage());
            throw new Exception("Error updating user status");
        }
    }
}
