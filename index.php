<?php
// index.php - O Controlador Frontal (Front Controller)

// 1. Configuração e Inicialização da Sessão
session_start();
require 'config.php'; // Carrega as constantes de configuração (BASE_URL, DB_USER, etc.)

// 2. Conexão com o Banco de Dados (Direta e Funcional)
try {
    // Usa as constantes de config.php para conectar com PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES     => false,
    ];
    
    $db = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // Se a conexão falhar, exibe um erro amigável e para a execução.
    die("<div style='padding: 20px; background-color: #fdd; border: 1px solid #f00; font-family: sans-serif;'>
        <h1>Erro de Conexão com o Banco de Dados</h1>
        <p>Não foi possível conectar ao banco de dados <b>" . DB_NAME . "</b>.</p>
        <p>Verifique o arquivo <code>config.php</code> e se o serviço MySQL do XAMPP está ativo.</p>
        <p>Detalhes do Erro: " . htmlspecialchars($e->getMessage()) . "</p>
    </div>");
}

// 3. Carregamento de Modelos e Controladores
require 'models/UserModel.php';
require 'models/PlantModel.php';
require 'models/CareModel.php'; // Model de cuidados
require 'controllers/UserController.php'; 
require 'controllers/PlantController.php';
require 'controllers/CareController.php'; // Controller de cuidados
require 'controllers/HomeController.php'; // Controller da Home

// --- Lógica de Roteamento ---

// 4. Define a rota: usa o valor de 'route' ou determina a rota padrão
$route = $_GET['route'] ?? '';

// Roteamento Padrão: Se a rota estiver vazia, define 'home' ou 'login'.
if (isset($_SESSION['user_id']) && empty($route)) {
    $route = 'home'; // MUDANÇA: Agora a rota padrão é 'home'
} elseif (!isset($_SESSION['user_id']) && empty($route)) {
    $route = 'login';
}

// 5. Executa o Controlador e a Ação
switch ($route) {
    // --- Rotas de Autenticação (UserController) ---
    case 'login':
        (new UserController($db))->login();
        break;
    case 'register':
        (new UserController($db))->register();
        break;
    case 'logout':
        (new UserController($db))->logout();
        break;

    // --- NOVA ROTA: HOME (HomeController) ---
    case 'home':
        // Rota protegida: Requer que o usuário esteja logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?route=login');
            exit;
        }
        (new HomeController($db))->index();
        break;

    // --- Rotas de Gestão de Plantas (PlantController) ---
    case 'dashboard':
    case 'plant_register':
    case 'plant_details':
    case 'plant_edit':
    case 'plant_delete':
        // Rotas protegidas: Requerem que o usuário esteja logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?route=login');
            exit;
        }
        $controller = new PlantController($db);
        if ($route === 'dashboard') {
            $controller->index(); // Listagem de plantas (Dashboard)
        } elseif ($route === 'plant_register') {
            // CORREÇÃO: Tenta create() primeiro, depois register() como fallback
            if (method_exists($controller, 'create')) {
                $controller->create(); // Formulário de Cadastro de Planta
            } elseif (method_exists($controller, 'register')) {
                $controller->register(); // Fallback para register()
            } else {
                // Se nenhum método existir, mostra erro
                die("Erro: Método para cadastro de planta não encontrado no PlantController");
            }
        } elseif ($route === 'plant_details') {
            $controller->show();
        } elseif ($route === 'plant_edit') {
            $controller->edit();
        } elseif ($route === 'plant_delete') {
            $controller->delete();
        }
        break;

    // --- Rotas: Gestão de Cuidados (CareController) ---
    case 'care_register':
    case 'care_history':
    case 'care_pending':
    case 'care_complete':
    case 'care_stats':
    case 'care_edit':
    case 'care_delete':
        // Rotas protegidas: Requerem que o usuário esteja logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?route=login');
            exit;
        }
        $controller = new CareController($db);
        if ($route === 'care_register') {
            $controller->register(); // Formulário de registro de cuidado
        } elseif ($route === 'care_history') {
            $controller->history(); // Histórico de cuidados de uma planta
        } elseif ($route === 'care_pending') {
            $controller->pending(); // ✅ NOVO: Lista de cuidados pendentes
        } elseif ($route === 'care_complete') {
            $controller->complete(); // ✅ NOVO: Dar baixa em cuidado
        } elseif ($route === 'care_stats') {
            $controller->stats(); // ✅ NOVO: Estatísticas de cuidados
        } elseif ($route === 'care_edit') {
            // ✅ NOVO: Editar cuidado - precisa do ID
            $careId = $_GET['id'] ?? null;
            if (!$careId) {
                $_SESSION['error_message'] = "ID do cuidado não informado.";
                header('Location: ' . BASE_URL . '?route=care_pending');
                exit;
            }
            $controller->edit($careId);
        } elseif ($route === 'care_delete') {
            // ✅ NOVO: Excluir cuidado - precisa do ID
            $careId = $_POST['care_id'] ?? $_GET['id'] ?? null;
            if (!$careId) {
                $_SESSION['error_message'] = "ID do cuidado não informado.";
                header('Location: ' . BASE_URL . '?route=care_pending');
                exit;
            }
            $controller->delete($careId);
        }
        break;
        
    default:
        // Se a rota não for reconhecida, redireciona para HOME (se logado) ou login
        $target_route = isset($_SESSION['user_id']) ? 'home' : 'login';
        header('Location: ' . BASE_URL . '?route=' . $target_route);
        exit;
}