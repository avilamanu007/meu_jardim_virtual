<?php 
$plant = $plant ?? [];
$cares = $cares ?? [];
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Cuidados - <?= htmlspecialchars($plant['name'] ?? 'Planta') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen p-4">
    <div class="max-w-6xl mx-auto">
        <!-- Cabeçalho -->
        <div class="bg-white p-6 rounded-2xl shadow-lg mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">📋 Histórico de Cuidados</h1>
                    <p class="text-gray-600">
                        <?= htmlspecialchars($plant['name'] ?? 'Planta não encontrada') ?> 
                        <?= isset($plant['species']) ? ' - ' . htmlspecialchars($plant['species']) : '' ?>
                    </p>
                </div>
                <div class="text-right">
                    <a href="<?= BASE_URL ?>?route=care/register" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 inline-block">
                        ➕ Novo Cuidado
                    </a>
                </div>
            </div>
            
            <div class="flex gap-4 mt-4">
                <a href="<?= BASE_URL ?>?route=dashboard" class="text-green-600 hover:text-green-800 text-sm inline-block">
                    &larr; Voltar ao Dashboard
                </a>
                <a href="<?= BASE_URL ?>?route=plant/detail&id=<?= $plant['id'] ?? '' ?>" class="text-green-600 hover:text-green-800 text-sm inline-block">
                    📊 Ver Detalhes da Planta
                </a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <?php foreach ($errors as $error): ?>
                    <div>❌ <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Estatísticas Rápidas -->
        <?php if (!empty($cares)): ?>
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
                    <?= count(array_filter($cares, fn($care) => $care['care_type'] === 'Podar')) ?>
                </div>
                <div class="text-sm text-gray-600">Podas</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Lista de Cuidados -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <?php if (empty($cares)): ?>
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-4">🌱</div>
                    <p class="text-lg mb-2">Nenhum cuidado registrado para esta planta ainda.</p>
                    <p class="text-sm text-gray-400 mb-4">Comece registrando o primeiro cuidado!</p>
                    <a href="<?= BASE_URL ?>?route=care/register" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition duration-200 inline-block">
                        Registrar Primeiro Cuidado
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Próxima Manutenção</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($cares as $care): ?>
                            <?php 
                                $isOverdue = $care['next_maintenance_date'] && strtotime($care['next_maintenance_date']) < time();
                                $isToday = $care['next_maintenance_date'] && date('Y-m-d') === $care['next_maintenance_date'];
                            ?>
                            <tr class="hover:bg-gray-50 <?= $isOverdue ? 'bg-red-50' : '' ?> <?= $isToday ? 'bg-yellow-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d/m/Y', strtotime($care['care_date'])) ?>
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
                                    <?= !empty($care['observations']) ? nl2br(htmlspecialchars($care['observations'])) : '<span class="text-gray-400">Sem observações</span>' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($care['next_maintenance_date']): ?>
                                        <div class="flex items-center">
                                            <?= date('d/m/Y', strtotime($care['next_maintenance_date'])) ?>
                                            <?php if ($isOverdue): ?>
                                                <span class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Atrasada</span>
                                            <?php elseif ($isToday): ?>
                                                <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Hoje</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($isOverdue): ?>
                                        <span class="text-red-600 font-medium">⚠️ Necessária</span>
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
                
                <!-- Resumo -->
                <div class="bg-gray-50 px-6 py-3 text-xs text-gray-500 border-t">
                    Mostrando <?= count($cares) ?> cuidado(s) • 
                    <?= count(array_filter($cares, fn($care) => $care['next_maintenance_date'] && strtotime($care['next_maintenance_date']) < time())) ?> atrasado(s) • 
                    <?= count(array_filter($cares, fn($care) => $care['next_maintenance_date'] && date('Y-m-d') === $care['next_maintenance_date'])) ?> para hoje
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>