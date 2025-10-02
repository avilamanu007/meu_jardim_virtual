<?php

/**
 * Lógica de dados para a tabela 'plants'.
 */
class PlantModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Cria uma nova planta no banco de dados.
     */
    public function createPlant(int $userId, string $name, string $species, string $acquisition_date, string $location): bool {
        try {
            $stmt = $this->db->prepare("INSERT INTO plants (user_id, name, species, acquisition_date, location) VALUES (:user_id, :name, :species, :acquisition_date, :location)");
            
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':species' => $species,
                ':acquisition_date' => $acquisition_date,
                ':location' => $location
            ]);
            
            error_log("PlantModel - Planta criada: " . ($result ? 'SUCESSO' : 'FALHA'));
            return $result;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao criar planta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca todas as plantas de um usuário específico.
     * Esta função é usada para a tela de listagem/Dashboard.
     */
    public function getAllPlantsByUserId(int $userId): array {
        try {
            $stmt = $this->db->prepare("SELECT id, name, species, acquisition_date, location FROM plants WHERE user_id = :user_id ORDER BY name ASC");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $plants = $stmt->fetchAll();
            
            error_log("PlantModel - Plantas encontradas para usuário $userId: " . count($plants));
            return $plants;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar plantas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca uma planta específica pelo ID.
     */
    public function getPlantById(int $id): ?array {
        try {
            $stmt = $this->db->prepare("SELECT id, user_id, name, species, acquisition_date, location FROM plants WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $plant = $stmt->fetch();
            
            error_log("PlantModel - Planta encontrada ID $id: " . ($plant ? 'SIM' : 'NÃO'));
            return $plant ? $plant : null;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar planta ID $id: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca planta por ID verificando propriedade do usuário
     * Usado para validar se a planta pertence ao usuário
     */
    public function getPlantByIdAndUserId(int $plantId, int $userId): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, user_id, name, species, acquisition_date, location 
                FROM plants 
                WHERE id = :plant_id AND user_id = :user_id
            ");
            $stmt->execute([
                ':plant_id' => $plantId,
                ':user_id' => $userId
            ]);
            $plant = $stmt->fetch();
            
            error_log("PlantModel - Validação propriedade - Planta $plantId do usuário $userId: " . ($plant ? 'PERTENCE' : 'NÃO PERTENCE'));
            return $plant ? $plant : null;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao validar propriedade da planta: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Atualiza os dados de uma planta.
     */
    public function updatePlant($plantId, $name, $species, $acquisitionDate, $location): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE plants 
                SET name = ?, species = ?, acquisition_date = ?, location = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([$name, $species, $acquisitionDate, $location, $plantId]);
            
            error_log("PlantModel - Planta atualizada ID $plantId: " . ($result ? 'SUCESSO' : 'FALHA'));
            return $result;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao atualizar planta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui uma planta pelo ID.
     */
    public function deletePlant(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM plants WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            
            error_log("PlantModel - Planta excluída ID $id: " . ($result ? 'SUCESSO' : 'FALHA'));
            return $result;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao excluir planta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Conta quantas plantas o usuário possui
     */
    public function countPlantsByUserId(int $userId): int {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM plants WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao contar plantas: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Busca plantas que precisam de cuidados (com base na última manutenção)
     */
    public function getPlantsNeedingCare(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, 
                       MAX(c.next_maintenance_date) as last_maintenance_date
                FROM plants p
                LEFT JOIN cares c ON p.id = c.plant_id
                WHERE p.user_id = :user_id
                GROUP BY p.id
                HAVING last_maintenance_date IS NULL OR last_maintenance_date <= CURDATE()
                ORDER BY last_maintenance_date ASC
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar plantas precisando de cuidado: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Busca plantas com cuidados pendentes/atrasados para notificações
     */
    public function getPlantsWithPendingCare(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT p.*, 
                       MIN(c.next_maintenance_date) as next_care_date,
                       DATEDIFF(MIN(c.next_maintenance_date), CURDATE()) as days_remaining
                FROM plants p
                INNER JOIN cares c ON p.id = c.plant_id
                WHERE p.user_id = :user_id 
                AND c.next_maintenance_date IS NOT NULL
                AND c.next_maintenance_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                GROUP BY p.id
                HAVING days_remaining <= 7
                ORDER BY next_care_date ASC
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $plants = $stmt->fetchAll();
            
            // Classificar por urgência
            foreach ($plants as &$plant) {
                if ($plant['days_remaining'] < 0) {
                    $plant['priority'] = 'high'; // Atrasado
                } elseif ($plant['days_remaining'] == 0) {
                    $plant['priority'] = 'medium'; // Para hoje
                } else {
                    $plant['priority'] = 'low'; // Próximos dias
                }
            }
            
            error_log("PlantModel - Plantas com cuidados pendentes encontradas: " . count($plants));
            return $plants;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar plantas com cuidados pendentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Busca estatísticas gerais do jardim
     */
    public function getGardenStats(int $userId): array {
        try {
            $stats = [];
            
            // Total de plantas
            $stats['total_plants'] = $this->countPlantsByUserId($userId);
            
            // Plantas com cuidados pendentes
            $pendingPlants = $this->getPlantsWithPendingCare($userId);
            $stats['pending_care'] = count($pendingPlants);
            
            // Plantas por localização
            $stmt = $this->db->prepare("
                SELECT location, COUNT(*) as count 
                FROM plants 
                WHERE user_id = :user_id 
                GROUP BY location 
                ORDER BY count DESC
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $stats['plants_by_location'] = $stmt->fetchAll();
            
            // Espécies mais comuns
            $stmt = $this->db->prepare("
                SELECT species, COUNT(*) as count 
                FROM plants 
                WHERE user_id = :user_id 
                GROUP BY species 
                ORDER BY count DESC 
                LIMIT 5
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $stats['top_species'] = $stmt->fetchAll();
            
            error_log("PlantModel - Estatísticas carregadas: " . json_encode($stats));
            return $stats;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar estatísticas: " . $e->getMessage());
            return [
                'total_plants' => 0,
                'pending_care' => 0,
                'plants_by_location' => [],
                'top_species' => []
            ];
        }
    }

    /**
     * ✅ NOVO: Busca plantas que nunca receberam cuidados
     */
    public function getPlantsWithoutCare(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT p.* 
                FROM plants p
                LEFT JOIN cares c ON p.id = c.plant_id
                WHERE p.user_id = :user_id 
                AND c.id IS NULL
                ORDER BY p.name ASC
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $plants = $stmt->fetchAll();
            error_log("PlantModel - Plantas sem cuidados: " . count($plants));
            return $plants;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar plantas sem cuidados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Busca plantas por localização
     */
    public function getPlantsByLocation(int $userId, string $location): array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, species, acquisition_date, location 
                FROM plants 
                WHERE user_id = :user_id AND location LIKE :location
                ORDER BY name ASC
            ");
            $searchLocation = '%' . $location . '%';
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':location', $searchLocation);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar plantas por localização: " . $e->getMessage());
            return [];
        }
    }

    // =========================================================================
    // ✅ MÉTODOS NOVOS PARA A HOME (RESUMO E NOTIFICAÇÕES)
    // =========================================================================

    /**
     * ✅ NOVO: Conta plantas saudáveis (para o card de resumo)
     */
    public function getHealthyPlantsCount(int $userId): int {
        try {
            // Considera plantas saudáveis aquelas que receberam cuidados recentemente
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT p.id) as count
                FROM plants p
                LEFT JOIN cares c ON p.id = c.plant_id
                WHERE p.user_id = :user_id
                AND (c.care_date >= DATE_SUB(CURDATE(), INTERVAL 15 DAY) OR c.id IS NULL)
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao contar plantas saudáveis: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ NOVO: Conta total de localizações distintas (para o card de resumo)
     */
    public function getTotalLocations(int $userId): int {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT location) as count 
                FROM plants 
                WHERE user_id = :user_id 
                AND location IS NOT NULL 
                AND location != ''
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao contar localizações: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ NOVO: Busca plantas recentemente adicionadas (para a seção de atividades)
     */
    public function getRecentlyAddedPlants(int $userId, int $limit = 5): array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, species, location, acquisition_date,
                       DATE_FORMAT(created_at, '%d/%m/%Y') as added_date
                FROM plants 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $plants = $stmt->fetchAll();
            
            // Adiciona ícone para cada planta
            foreach ($plants as &$plant) {
                $plant['icon'] = $this->getPlantIcon($plant['species']);
            }
            
            return $plants;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar plantas recentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOVO: Busca estatísticas simplificadas para a HOME
     */
    public function getHomeSummaryStats(int $userId): array {
        try {
            return [
                'total_plants' => $this->countPlantsByUserId($userId),
                'pending_care' => $this->getPendingCareCount($userId),
                'healthy_plants' => $this->getHealthyPlantsCount($userId),
                'locations' => $this->getTotalLocations($userId)
            ];
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar estatísticas da home: " . $e->getMessage());
            return [
                'total_plants' => 0,
                'pending_care' => 0,
                'healthy_plants' => 0,
                'locations' => 0
            ];
        }
    }

    /**
     * ✅ NOVO: Conta cuidados pendentes (para integração com CareModel)
     */
    public function getPendingCareCount(int $userId): int {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT p.id) as count
                FROM plants p
                INNER JOIN cares c ON p.id = c.plant_id
                WHERE p.user_id = :user_id 
                AND c.next_maintenance_date IS NOT NULL
                AND c.next_maintenance_date <= CURDATE()
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao contar cuidados pendentes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ NOVO: Helper para determinar ícone baseado na espécie
     */
    private function getPlantIcon(string $species): string {
        $species = strtolower($species);
        
        if (strpos($species, 'suculent') !== false || strpos($species, 'cact') !== false) {
            return '🌵';
        } elseif (strpos($species, 'orquíd') !== false || strpos($species, 'orchild') !== false) {
            return '🌸';
        } elseif (strpos($species, 'samambai') !== false || strpos($species, 'fern') !== false) {
            return '🌿';
        } elseif (strpos($species, 'rosa') !== false || strpos($species, 'rose') !== false) {
            return '🌹';
        } elseif (strpos($species, 'comest') !== false || strpos($species, 'hortali') !== false) {
            return '🍅';
        } else {
            return '🌱';
        }
    }

    /**
     * ✅ NOVO: Busca notificações de plantas (para a seção de notificações)
     */
    public function getPlantNotifications(int $userId): array {
        try {
            $notifications = [];
            
            // Plantas sem cuidados
            $plantsWithoutCare = $this->getPlantsWithoutCare($userId);
            if (!empty($plantsWithoutCare)) {
                $notifications[] = [
                    'type' => 'warning',
                    'title' => 'Plantas sem cuidados',
                    'message' => count($plantsWithoutCare) . ' planta(s) nunca receberam cuidados',
                    'time' => 'Atenção'
                ];
            }
            
            // Plantas com cuidados atrasados
            $overduePlants = $this->getPlantsWithPendingCare($userId);
            $overdueCount = 0;
            foreach ($overduePlants as $plant) {
                if ($plant['priority'] === 'high') {
                    $overdueCount++;
                }
            }
            
            if ($overdueCount > 0) {
                $notifications[] = [
                    'type' => 'urgent',
                    'title' => 'Cuidados atrasados',
                    'message' => $overdueCount . ' planta(s) com cuidados em atraso',
                    'time' => 'Urgente'
                ];
            }
            
            // Plantas adicionadas recentemente
            $recentPlants = $this->getRecentlyAddedPlants($userId, 3);
            if (!empty($recentPlants)) {
                $plantNames = array_slice(array_column($recentPlants, 'name'), 0, 2);
                $notificationMsg = 'Novas plantas: ' . implode(', ', $plantNames);
                if (count($recentPlants) > 2) {
                    $notificationMsg .= ' e mais ' . (count($recentPlants) - 2);
                }
                
                $notifications[] = [
                    'type' => 'info',
                    'title' => 'Plantas recentes',
                    'message' => $notificationMsg,
                    'time' => 'Recente'
                ];
            }
            
            return $notifications;
            
        } catch (PDOException $e) {
            error_log("PlantModel - Erro ao buscar notificações: " . $e->getMessage());
            return [];
        }
    }
}