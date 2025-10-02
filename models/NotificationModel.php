<?php
class NotificationModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserNotifications($userId) {
        try {
            // Notificações de cuidados pendentes
            $sql = "SELECT 
                    'urgent' as type,
                    CONCAT('Cuidado pendente: ', p.name) as title,
                    CONCAT('Próximo cuidado: ', c.care_type, ' - ', DATE_FORMAT(c.next_care_date, '%d/%m/%Y')) as message,
                    TIMESTAMPDIFF(HOUR, NOW(), c.next_care_date) as hours_remaining
                FROM cares c
                JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? AND c.next_care_date <= DATE_ADD(NOW(), INTERVAL 2 DAY)
                ORDER BY c.next_care_date ASC
                LIMIT 5";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $notifications = $stmt->fetchAll();

            // Formatar as notificações
            foreach ($notifications as &$notification) {
                $hours = $notification['hours_remaining'];
                if ($hours <= 24) {
                    $notification['time'] = 'Urgente!';
                    $notification['type'] = 'urgent';
                } elseif ($hours <= 48) {
                    $notification['time'] = 'Em breve';
                    $notification['type'] = 'warning';
                } else {
                    $notification['time'] = 'Próximos dias';
                    $notification['type'] = 'info';
                }
                unset($notification['hours_remaining']);
            }

            return $notifications;

        } catch (PDOException $e) {
            // Em caso de erro, retorna array vazio
            return [];
        }
    }
}