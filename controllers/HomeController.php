<?php
class HomeController {
    private $db;
    private $plantModel;
    private $careModel;

    public function __construct($db) {
        $this->db = $db;
        $this->plantModel = new PlantModel($db);
        $this->careModel = new CareModel($db);
    }

    public function index() {
        // Verificar autenticação
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Buscar dados para o resumo
        $summaryStats = [
            'total_plants' => $this->plantModel->countPlantsByUserId($userId),
            'pending_care' => $this->careModel->getPendingCareCount($userId),
            'healthy_plants' => $this->plantModel->getHealthyPlantsCount($userId),
            'locations' => $this->plantModel->getTotalLocations($userId)
        ];

        //  Buscar lista de pendências para a HOME
        $pendingCares = $this->careModel->getPendingCaresForUser($userId);

        // Buscar notificações (combina notificações de plantas e cuidados)
        $plantNotifications = $this->plantModel->getPlantNotifications($userId);
        $careNotifications = $this->careModel->getCareNotifications($userId);
        $notifications = array_merge($plantNotifications, $careNotifications);

        // Buscar atividades recentes
        $recentActivities = $this->careModel->getRecentActivities($userId, 5);

        // Carregar a view
        require 'views/protected/home.php';
    }
}