<?php
require_once 'connection.php';
class User {
    private $db;
    private $email;
    private $firstName;
    private $lastName;
    private $password;
    private $roleId;

    public function __construct($database, $email, $firstName = null, $lastName = null, $password = null, $roleId = null) {
        if (!$database instanceof PDO) {
            throw new Exception("Invalid database connection");
        }
        
        $this->db = $database;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password ? password_hash($password, PASSWORD_DEFAULT) : null;
        $this->roleId = $roleId;
    }

    public function login($providedPassword) {
        try {
            if (!$this->db) {
                throw new Exception("Database connection not initialized");
            }

            $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare statement");
            }
            
            $stmt->execute(['email' => $this->email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception("User not found");
            }
            
            if (!password_verify($providedPassword, $user['password'])) {
                throw new Exception("Invalid password");
            }
            
            return $user;
            
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Error during login: " . $e->getMessage());
        }
    }

    public function registration() {
        try {
            $sql = "INSERT INTO user (name, last_name, email, password, role_id)
                    VALUES (:name, :last_name, :email, :password, :role_id)";
            
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                'name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'password' => $this->password,
                'role_id' => $this->roleId
            ]);
            
            if (!$result) {
                throw new Exception("Failed to insert user");
            }
            
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Error inserting user: " . $e->getMessage());
        }
    }
    public function showalluseS(){
        try {
            $sql = $this->db->prepare("SELECT nom , email , FROM ");

           
        }
    }

 
}