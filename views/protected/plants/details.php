<?php 
$plant = $plant ?? [];
$cares = $cares ?? [];
$errors = $errors ?? [];
$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';

// Limpar mensagens da sessão
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

$formatDate = function($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
};

require 'views/includes/header.php'; 
?>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Mensagens -->
    <?php if (!empty($successMessage)): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            ✅ <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            ❌ <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="max-w-6xl mx-auto">
        <!-- Botão Voltar MAIS VISÍVEL -->
        <div class="mb-6">
            <a href="<?= BASE_URL ?>?route=dashboard" 
               class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar ao Dashboard
            </a>
        </div>

        <!-- Cabeçalho da Planta -->
        <div class="bg-white p-6 rounded-2xl shadow-lg mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🌿 <?= htmlspecialchars($plant['name']) ?></h1>
                    <p class="text-gray-600 mt-2">
                        <strong>Espécie:</strong> <?= htmlspecialchars($plant['species']) ?><br>
                        <strong>Data de Aquisição:</strong> <?= $formatDate($plant['acquisition_date']) ?><br>
                        <strong>Localização:</strong> <?= htmlspecialchars($plant['location']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-green-600"><?= count($cares) ?></div>
                <div class="text-sm text-gray-600">Total de Cuidados</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-blue-600">
                    <?= count(array_filter($cares, fn($care) => $care['care_type'] === 'Regar')) ?>
                </div>
                <div class="text-sm text-gray-600">Regas</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-yellow-600">
                    <?= count(array_filter($cares, fn($care) => $care['care_type'] === 'Adubar')) ?>
                </div>
                <div class="text-sm text-gray-600">Adubações</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-purple-600">
                    <?= count(array_filter($cares, fn($care) => $care['next_maintenance_date'] && strtotime($care['next_maintenance_date']) <= time())) ?>
                </div>
                <div class="text-sm text-gray-600">Cuidados Pendentes</div>
            </div>
        </div>

        <!-- Últimos Cuidados -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">📅 Últimos Cuidados Registrados</h2>
            </div>
            
            <?php if (empty($cares)): ?>
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-4">💧</div>
                    <p class="text-lg mb-2">Nenhum cuidado registrado para esta planta ainda.</p>
                    <p class="text-sm text-gray-400 mb-4">Registre o primeiro cuidado para começar a acompanhar!</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observações</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Próxima Manutenção</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php 
                            // Mostrar apenas os últimos 5 cuidados
                            $recentCares = array_slice($cares, 0, 5);
                            foreach ($recentCares as $care): ?>
                            <?php 
                                $isOverdue = $care['next_maintenance_date'] && strtotime($care['next_maintenance_date']) < time();
                                $isToday = $care['next_maintenance_date'] && date('Y-m-d') === $care['next_maintenance_date'];
                            ?>
                            <tr class="hover:bg-gray-50 <?= $isOverdue ? 'bg-red-50' : '' ?> <?= $isToday ? 'bg-yellow-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $formatDate($care['care_date']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $typeConfig = [
                                        'Regar' => ['emoji' => '💧', 'color' => 'bg-blue-100 text-blue-800'],
                                        'Adubar' => ['emoji' => '🧪', 'color' => 'bg-yellow-100 text-yellow-800'],
                                        'Podar' => ['emoji' => '✂️', 'color' => 'bg-green-100 text-green-800'],
                                        'Mudar Vaso' => ['emoji' => '🔄', 'color' => 'bg-purple-100 text-purple-800'],
                                        'Limpar Folhas' => ['emoji' => '🧹', 'color' => 'bg-orange-100 text-orange-800']
                                    ];
                                    $config = $typeConfig[$care['care_type']] ?? ['emoji' => '📝', 'color' => 'bg-gray-100 text-gray-800'];
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $config['color'] ?>">
                                        <?= $config['emoji'] ?> 
                                        <span class="ml-1"><?= htmlspecialchars($care['care_type']) ?></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-md">
                                    <?= !empty($care['observations']) ? 
                                        (strlen($care['observations']) > 50 ? 
                                            substr(htmlspecialchars($care['observations']), 0, 50) . '...' : 
                                            htmlspecialchars($care['observations'])) 
                                        : '<span class="text-gray-400">-</span>' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $formatDate($care['next_maintenance_date']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($isOverdue): ?>
                                        <span class="text-red-600 font-medium">⚠️ Atrasada</span>
                                    <?php elseif ($isToday): ?>
                                        <span class="text-yellow-600 font-medium">📅 Para hoje</span>
                                    <?php elseif ($care['next_maintenance_date']): ?>
                                        <span class="text-green-600 font-medium">✅ Em dia</span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Próximos Cuidados Pendentes -->
        <?php 
        $pendingCares = array_filter($cares, fn($care) => 
            $care['next_maintenance_date'] && strtotime($care['next_maintenance_date']) <= time()
        );
        ?>
        <?php if (!empty($pendingCares)): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-4">⚠️ Cuidados Pendentes</h3>
            <div class="space-y-2">
                <?php foreach ($pendingCares as $care): ?>
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border">
                    <div>
                        <span class="font-medium text-gray-800">
                            <?= htmlspecialchars($care['care_type']) ?>
                        </span>
                        <span class="text-sm text-gray-600 ml-2">
                            - Próxima manutenção: <?= $formatDate($care['next_maintenance_date']) ?>
                        </span>
                    </div>
                    <span class="text-red-600 font-medium text-sm">
                        ⚠️ Necessário
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require 'views/includes/footer.php'; ?>