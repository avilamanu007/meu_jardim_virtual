<?php
// controllers/AuthController.php

class AuthController {
    private PDO $db;
    private UserModel $userModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->userModel = new UserModel($db);
    }

    // -------------------------------------------------------------------------
    // Rota: ?route=register
    // -------------------------------------------------------------------------

    /**
     * Exibe e processa o formulário de Cadastro de Usuário.
     */
    public function register() {
        if (isset($_SESSION['user_id'])) {
            // Redireciona se o usuário já estiver logado
            header('Location: ' . BASE_URL . '?route=plant_register');
            exit;
        }

        $errors = [];
        $data = ['name' => '', 'email' => '', 'password' => '', 'password_confirm' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Captura e valida os dados
            $data['name'] = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = trim($_POST['password'] ?? '');
            $data['password_confirm'] = trim($_POST['password_confirm'] ?? '');

            // Validações
            if (empty($data['name'])) {
                $errors['name'] = "O nome é obrigatório.";
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email inválido.";
            } elseif ($this->userModel->findByEmail($data['email'])) {
                $errors['email'] = "Este email já está cadastrado.";
            }

            if (strlen($data['password']) < 6) {
                $errors['password'] = "A senha deve ter no mínimo 6 caracteres.";
            }

            if ($data['password'] !== $data['password_confirm']) {
                $errors['password_confirm'] = "A confirmação de senha não confere.";
            }

            // Se não houver erros, cria o usuário
            if (empty($errors)) {
                if ($this->userModel->createUser($data['name'], $data['email'], $data['password'])) {
                    $user = $this->userModel->findByEmail($data['email']);
                    $_SESSION['user_id'] = $user['id'];

                    // Redirecionamento para a área protegida
                    header('Location: ' . BASE_URL . '?route=plant_register');
                    exit;
                } else {
                    $errors['db'] = "Erro ao cadastrar usuário. Verifique sua conexão com o banco de dados.";
                }
            }
        }

        require 'views/public/register_form.php';
    }

    // -------------------------------------------------------------------------
    // Rota: ?route=login
    // -------------------------------------------------------------------------

    /**
     * Exibe e processa o formulário de Login.
     */
    public function login() {
        if (isset($_SESSION['user_id'])) {
            // Redireciona se o usuário já estiver logado
            header('Location: ' . BASE_URL . '?route=plant_register');
            exit;
        }

        $errors = [];
        $data = ['email' => '', 'password' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = trim($_POST['password'] ?? '');

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email inválido.";
            } else {
                $user = $this->userModel->findByEmail($data['email']);

                if (!$user || !password_verify($data['password'], $user['password_hash'])) {
                    $errors['password'] = "Email ou senha incorretos.";
                } else {
                    // Login bem-sucedido
                    $_SESSION['user_id'] = $user['id'];
                    header('Location: ' . BASE_URL . '?route=plant_register');
                    exit;
                }
            }
        }

        require 'views/public/login_form.php';
    }

    // -------------------------------------------------------------------------
    // Rota: ?route=logout
    // -------------------------------------------------------------------------
    
    /**
     * Destrói a sessão e redireciona para a tela de login.
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '?route=login');
        exit;
    }
}
