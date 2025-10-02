<?php
// models/UserModel.php - Lógica de dados para a tabela 'users'

class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Busca um usuário pelo email.
     * COMPATÍVEL com UserController::register() e login()
     */
    public function getUserByEmail($email) {
        $email = trim($email);
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro no getUserByEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria um novo usuário no banco de dados.
     * COMPATÍVEL com UserController::register()
     */
    public function createUser($name, $email, $passwordHash) {
        $name = trim($name);
        $email = trim($email);

        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password_hash, created_at) 
                VALUES (?, ?, ?, NOW())
            ");

            $success = $stmt->execute([$name, $email, $passwordHash]);
            
            if ($success) {
                return $this->db->lastInsertId(); // Retorna o ID do usuário criado
            }
            return false;
            
        } catch (PDOException $e) {
            error_log("Erro no createUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Método adicional útil: Buscar usuário por ID
     */
    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro no getUserById: " . $e->getMessage());
            return false;
        }
    }
}