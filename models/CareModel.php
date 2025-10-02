<?php
class CareModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createCare($plantId, $careType, $careDate, $observations, $nextMaintenanceDate) {
        try {
            // ✅ CORREÇÃO: DEBUG DETALHADO para identificar o problema
            error_log("=== CAREMODEL - CREATE CARE ===");
            error_log("Dados recebidos:");
            error_log("  plantId: $plantId");
            error_log("  careType: $careType");
            error_log("  careDate: $careDate");
            error_log("  observations: $observations");
            error_log("  nextMaintenanceDate: $nextMaintenanceDate");
            
            // ✅ CORREÇÃO: Converter care_type para valores do ENUM do banco
            $convertedCareType = $this->convertCareType($careType);
            error_log("Tipo convertido: $careType -> $convertedCareType");
            
            $stmt = $this->db->prepare("
                INSERT INTO cares 
                (plant_id, care_type, care_date, observations, next_maintenance_date) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([
                $plantId, 
                $convertedCareType, // ✅ Já convertido
                $careDate, 
                $observations, 
                $nextMaintenanceDate
            ]);
            
            // DEBUG: Verificar se executou
            error_log("CareModel - Executou: " . ($success ? 'SIM' : 'NÃO'));
            
            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("CareModel - Erro SQL: " . print_r($errorInfo, true));
                
                // ✅ CORREÇÃO: Se der erro de ENUM, tentar inserir sem conversão
                if (isset($errorInfo[1]) && $errorInfo[1] == 1265) { // Erro de dados truncados
                    error_log("CareModel - Tentando inserir sem conversão...");
                    $stmt = $this->db->prepare("
                        INSERT INTO cares 
                        (plant_id, care_type, care_date, observations, next_maintenance_date) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $success = $stmt->execute([
                        $plantId, 
                        $careType, // ✅ Tentar sem conversão
                        $careDate, 
                        $observations, 
                        $nextMaintenanceDate
                    ]);
                    
                    error_log("CareModel - Segunda tentativa: " . ($success ? 'SUCESSO' : 'FALHA'));
                }
            }
            
            error_log("=== CAREMODEL - FIM CREATE CARE ===");
            return $success;
            
        } catch (PDOException $e) {
            error_log("Erro ao registrar cuidado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ CORREÇÃO: Converte tipos do controller para valores do ENUM do banco
     */
    private function convertCareType($controllerType) {
        $conversionMap = [
            'rega' => 'Regar',
            'adubacao' => 'Adubar', 
            'poda' => 'Podar',
            'transplante' => 'Mudar Vaso',
            'tratamento' => 'Limpar Folhas',
            'limpeza' => 'Limpar Folhas'
        ];
        
        // ✅ CORREÇÃO: Debug detalhado da conversão
        error_log("CareModel - Convertendo tipo: '$controllerType'");
        
        $converted = $conversionMap[strtolower($controllerType)] ?? 'Regar';
        error_log("CareModel - Resultado da conversão: '$converted'");
        
        return $converted;
    }

    public function getCaresByPlantId($plantId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM cares 
                WHERE plant_id = ? 
                ORDER BY care_date DESC
            ");
            $stmt->execute([$plantId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar cuidados: " . $e->getMessage());
            return [];
        }
    }

    // ✅ MÉTODO ADICIONAL: Buscar todos os cuidados do usuário
    public function getCaresByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, p.name as plant_name 
                FROM cares c 
                INNER JOIN plants p ON c.plant_id = p.id 
                WHERE p.user_id = ? 
                ORDER BY c.care_date DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar cuidados do usuário: " . $e->getMessage());
            return [];
        }
    }

    // =========================================================================
    // ✅ MÉTODOS PARA BAIXA DE PENDÊNCIAS
    // =========================================================================

    /**
     * ✅ NOVO: Registra a execução de um cuidado pendente
     */
    public function completeCare($careId, $userId, $observations = '') {
        try {
            // Primeiro verifica se o cuidado pertence ao usuário
            $stmt = $this->db->prepare("
                SELECT c.*, p.name as plant_name 
                FROM cares c 
                INNER JOIN plants p ON c.plant_id = p.id 
                WHERE c.id = ? AND p.user_id = ?
            ");
            $stmt->execute([$careId, $userId]);
            $care = $stmt->fetch();
            
            if (!$care) {
                error_log("CareModel - Cuidado não encontrado ou não pertence ao usuário");
                return false;
            }
            
            // Atualiza o cuidado: marca como executado hoje e calcula próxima data
            $today = date('Y-m-d');
            $nextDate = $this->calculateNextMaintenanceDate($care['care_type'], $today);
            
            $stmt = $this->db->prepare("
                UPDATE cares 
                SET care_date = ?, 
                    observations = CONCAT(IFNULL(observations, ''), ?),
                    next_maintenance_date = ?
                WHERE id = ?
            ");
            
            $observationText = $observations ? "\n\nBaixa realizada em " . date('d/m/Y') . ": " . $observations : "\n\nBaixa realizada em " . date('d/m/Y');
            
            $success = $stmt->execute([
                $today,
                $observationText,
                $nextDate,
                $careId
            ]);
            
            error_log("CareModel - Baixa realizada no cuidado $careId: " . ($success ? 'SUCESSO' : 'FALHA'));
            return $success;
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao dar baixa no cuidado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ NOVO: Calcula a próxima data de manutenção baseada no tipo de cuidado
     */
    private function calculateNextMaintenanceDate($careType, $baseDate) {
        $intervals = [
            'Regar' => '+3 days',        // A cada 3 dias
            'Adubar' => '+30 days',      // A cada 30 dias
            'Podar' => '+90 days',       // A cada 3 meses
            'Mudar Vaso' => '+365 days', // A cada ano
            'Limpar Folhas' => '+7 days' // A cada semana
        ];
        
        $interval = $intervals[$careType] ?? '+7 days';
        return date('Y-m-d', strtotime($baseDate . ' ' . $interval));
    }

    /**
     * ✅ NOVO: Busca cuidados pendentes para um usuário (para a tela de baixa)
     */
    public function getPendingCaresForUser($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id as care_id,
                    c.care_type,
                    c.next_maintenance_date,
                    c.observations,
                    DATEDIFF(c.next_maintenance_date, CURDATE()) as days_overdue,
                    p.id as plant_id,
                    p.name as plant_name,
                    p.species,
                    p.location,
                    CASE 
                        WHEN c.next_maintenance_date < CURDATE() THEN 'Atrasado'
                        WHEN c.next_maintenance_date = CURDATE() THEN 'Para hoje'
                        ELSE 'Próximos dias'
                    END as status
                FROM cares c
                INNER JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? 
                AND c.next_maintenance_date IS NOT NULL
                AND c.next_maintenance_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ORDER BY 
                    CASE 
                        WHEN c.next_maintenance_date < CURDATE() THEN 1
                        WHEN c.next_maintenance_date = CURDATE() THEN 2
                        ELSE 3
                    END,
                    c.next_maintenance_date ASC
            ");
            $stmt->execute([$userId]);
            
            $cares = $stmt->fetchAll();
            
            // Adicionar informações extras
            foreach ($cares as &$care) {
                $care['icon'] = $this->getCareIcon($care['care_type']);
                $care['formatted_date'] = date('d/m/Y', strtotime($care['next_maintenance_date']));
                $care['days_text'] = $this->getDaysText($care['days_overdue']);
                
                if ($care['days_overdue'] < 0) {
                    $care['priority'] = 'high';
                    $care['badge_color'] = 'bg-red-100 text-red-800';
                } elseif ($care['days_overdue'] == 0) {
                    $care['priority'] = 'medium';
                    $care['badge_color'] = 'bg-orange-100 text-orange-800';
                } else {
                    $care['priority'] = 'low';
                    $care['badge_color'] = 'bg-blue-100 text-blue-800';
                }
            }
            
            return $cares;
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao buscar cuidados pendentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Helper para texto dos dias
     */
    private function getDaysText($days) {
        if ($days < 0) {
            return abs($days) . ' dia(s) atrasado(s)';
        } elseif ($days == 0) {
            return 'Para hoje';
        } else {
            return "Em $days dia(s)";
        }
    }

    // =========================================================================
    // ✅ MÉTODOS PARA A HOME (RESUMO E NOTIFICAÇÕES)
    // =========================================================================

    /**
     * ✅ NOVO: Conta cuidados pendentes para o usuário
     */
    public function getPendingCareCount($userId): int {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM cares c
                INNER JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? 
                AND c.next_maintenance_date IS NOT NULL
                AND c.next_maintenance_date <= CURDATE()
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao contar cuidados pendentes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ NOVO: Busca cuidados pendentes com detalhes para notificações
     */
    public function getPendingCaresWithDetails($userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id as care_id,
                    c.care_type,
                    c.next_maintenance_date,
                    DATEDIFF(c.next_maintenance_date, CURDATE()) as days_overdue,
                    p.id as plant_id,
                    p.name as plant_name,
                    p.location
                FROM cares c
                INNER JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? 
                AND c.next_maintenance_date IS NOT NULL
                AND c.next_maintenance_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                ORDER BY c.next_maintenance_date ASC
            ");
            $stmt->execute([$userId]);
            $cares = $stmt->fetchAll();
            
            // Adicionar informações de urgência
            foreach ($cares as &$care) {
                if ($care['days_overdue'] < 0) {
                    $care['priority'] = 'high';
                    $care['status'] = 'Atrasado';
                } elseif ($care['days_overdue'] == 0) {
                    $care['priority'] = 'medium';
                    $care['status'] = 'Para hoje';
                } else {
                    $care['priority'] = 'low';
                    $care['status'] = 'Próximos dias';
                }
                
                // Adicionar ícone baseado no tipo de cuidado
                $care['icon'] = $this->getCareIcon($care['care_type']);
            }
            
            return $cares;
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao buscar cuidados pendentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Busca atividades recentes para a HOME
     */
    public function getRecentActivities($userId, $limit = 10): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.care_type,
                    c.care_date,
                    c.observations,
                    c.next_maintenance_date,
                    p.id as plant_id,
                    p.name as plant_name,
                    p.species,
                    DATE_FORMAT(c.care_date, '%d/%m/%Y às %H:%i') as formatted_date,
                    TIMESTAMPDIFF(HOUR, c.care_date, NOW()) as hours_ago
                FROM cares c
                INNER JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? 
                ORDER BY c.care_date DESC
                LIMIT ?
            ");
            $stmt->bindValue(1, $userId);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $activities = $stmt->fetchAll();
            
            // Formatar as atividades para a HOME
            foreach ($activities as &$activity) {
                $activity['description'] = $this->formatActivityDescription($activity);
                $activity['time'] = $this->formatTimeAgo($activity['hours_ago']);
                $activity['icon'] = $this->getCareIcon($activity['care_type']);
            }
            
            return $activities;
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao buscar atividades recentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Busca estatísticas de cuidados para resumo
     */
    public function getCareStats($userId): array {
        try {
            // Cuidados realizados nos últimos 30 dias
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_cares,
                    COUNT(DISTINCT c.plant_id) as plants_cared,
                    SUM(CASE WHEN c.care_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as cares_last_week,
                    AVG(CASE WHEN c.care_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as avg_cares_per_day
                FROM cares c
                INNER JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? 
                AND c.care_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch();
            
            // Distribuição por tipo de cuidado
            $stmt = $this->db->prepare("
                SELECT 
                    care_type,
                    COUNT(*) as count
                FROM cares c
                INNER JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? 
                AND c.care_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY care_type
                ORDER BY count DESC
            ");
            $stmt->execute([$userId]);
            $typeDistribution = $stmt->fetchAll();
            
            return [
                'total_cares' => $stats['total_cares'] ?? 0,
                'plants_cared' => $stats['plants_cared'] ?? 0,
                'cares_last_week' => $stats['cares_last_week'] ?? 0,
                'avg_cares_per_day' => round($stats['avg_cares_per_day'] ?? 0, 1),
                'type_distribution' => $typeDistribution
            ];
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao buscar estatísticas: " . $e->getMessage());
            return [
                'total_cares' => 0,
                'plants_cared' => 0,
                'cares_last_week' => 0,
                'avg_cares_per_day' => 0,
                'type_distribution' => []
            ];
        }
    }

    /**
     * ✅ NOVO: Busca próximos cuidados agendados
     */
    public function getUpcomingCares($userId, $daysAhead = 7): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.care_type,
                    c.next_maintenance_date,
                    DATEDIFF(c.next_maintenance_date, CURDATE()) as days_until,
                    p.id as plant_id,
                    p.name as plant_name,
                    p.location
                FROM cares c
                INNER JOIN plants p ON c.plant_id = p.id
                WHERE p.user_id = ? 
                AND c.next_maintenance_date IS NOT NULL
                AND c.next_maintenance_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY c.next_maintenance_date ASC
            ");
            $stmt->bindValue(1, $userId);
            $stmt->bindValue(2, $daysAhead, PDO::PARAM_INT);
            $stmt->execute();
            
            $upcomingCares = $stmt->fetchAll();
            
            // Adicionar informações de formatação
            foreach ($upcomingCares as &$care) {
                $care['formatted_date'] = date('d/m/Y', strtotime($care['next_maintenance_date']));
                $care['icon'] = $this->getCareIcon($care['care_type']);
                
                if ($care['days_until'] == 0) {
                    $care['timeline'] = 'Hoje';
                } elseif ($care['days_until'] == 1) {
                    $care['timeline'] = 'Amanhã';
                } else {
                    $care['timeline'] = "Em {$care['days_until']} dias";
                }
            }
            
            return $upcomingCares;
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao buscar próximos cuidados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Helper para obter ícone baseado no tipo de cuidado
     */
    private function getCareIcon($careType): string {
        $iconMap = [
            'Regar' => '💧',
            'Adubar' => '🌱',
            'Podar' => '✂️',
            'Mudar Vaso' => '🪴',
            'Limpar Folhas' => '🍃'
        ];
        
        return $iconMap[$careType] ?? '🌿';
    }

    /**
     * ✅ NOVO: Formata descrição da atividade para a HOME
     */
    private function formatActivityDescription($activity): string {
        $plantName = htmlspecialchars($activity['plant_name']);
        $careType = $activity['care_type'];
        
        $descriptions = [
            'Regar' => "Regou {$plantName}",
            'Adubar' => "Adubou {$plantName}",
            'Podar' => "Podou {$plantName}",
            'Mudar Vaso' => "Mudou {$plantName} de vaso",
            'Limpar Folhas' => "Limpeza em {$plantName}"
        ];
        
        return $descriptions[$careType] ?? "Cuidado em {$plantName}";
    }

    /**
     * ✅ NOVO: Formata tempo relativo para a HOME
     */
    private function formatTimeAgo($hoursAgo): string {
        if ($hoursAgo < 1) {
            return 'Agora mesmo';
        } elseif ($hoursAgo < 24) {
            return "Há {$hoursAgo} hora" . ($hoursAgo > 1 ? 's' : '');
        } else {
            $days = floor($hoursAgo / 24);
            return "Há {$days} dia" . ($days > 1 ? 's' : '');
        }
    }

    /**
     * ✅ NOVO: Gera notificações baseadas em cuidados para a HOME
     */
    public function getCareNotifications($userId): array {
        try {
            $notifications = [];
            
            // Cuidados atrasados
            $overdueCares = $this->getPendingCaresWithDetails($userId);
            $highPriorityCares = array_filter($overdueCares, function($care) {
                return $care['priority'] === 'high';
            });
            
            if (!empty($highPriorityCares)) {
                $plantNames = array_slice(array_column($highPriorityCares, 'plant_name'), 0, 2);
                $message = count($highPriorityCares) . ' cuidado(s) atrasado(s)';
                if (!empty($plantNames)) {
                    $message .= ' em: ' . implode(', ', $plantNames);
                }
                
                $notifications[] = [
                    'type' => 'urgent',
                    'title' => 'Cuidados Atrasados',
                    'message' => $message,
                    'time' => 'Urgente'
                ];
            }
            
            // Próximos cuidados (para hoje)
            $todayCares = array_filter($overdueCares, function($care) {
                return $care['priority'] === 'medium';
            });
            
            if (!empty($todayCares)) {
                $plantNames = array_slice(array_column($todayCares, 'plant_name'), 0, 2);
                $message = count($todayCares) . ' cuidado(s) para hoje';
                if (!empty($plantNames)) {
                    $message .= ' em: ' . implode(', ', $plantNames);
                }
                
                $notifications[] = [
                    'type' => 'warning',
                    'title' => 'Cuidados para Hoje',
                    'message' => $message,
                    'time' => 'Hoje'
                ];
            }
            
            // Atividade recente
            $recentActivities = $this->getRecentActivities($userId, 1);
            if (!empty($recentActivities)) {
                $latestActivity = $recentActivities[0];
                $notifications[] = [
                    'type' => 'info',
                    'title' => 'Última Atividade',
                    'message' => $latestActivity['description'],
                    'time' => $latestActivity['time']
                ];
            }
            
            return $notifications;
            
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao gerar notificações: " . $e->getMessage());
            return [];
        }
    }

    // ✅ MÉTODOS ADICIONAIS PARA O CONTROLLER
    public function getCareById($careId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM cares WHERE id = ?");
            $stmt->execute([$careId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao buscar cuidado: " . $e->getMessage());
            return null;
        }
    }

    public function updateCare($careId, $careData) {
        try {
            $stmt = $this->db->prepare("
                UPDATE cares 
                SET care_type = ?, care_date = ?, observations = ?, next_maintenance_date = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $careData['care_type'],
                $careData['care_date'],
                $careData['observations'],
                $careData['next_maintenance_date'],
                $careId
            ]);
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao atualizar cuidado: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCare($careId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cares WHERE id = ?");
            return $stmt->execute([$careId]);
        } catch (PDOException $e) {
            error_log("CareModel - Erro ao excluir cuidado: " . $e->getMessage());
            return false;
        }
    }
}