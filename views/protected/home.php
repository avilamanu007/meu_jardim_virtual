<?php

$summaryStats = $summaryStats ?? [
    'total_plants' => 0,
    'pending_care' => 0,
    'healthy_plants' => 0,
    'locations' => 0
];

$notifications = $notifications ?? [];
$recentActivities = $recentActivities ?? [];
$pendingCares = $pendingCares ?? []; // ✅ Lista de pendências

//  Mostrar notificações se tiver pendências OU outras notificações
$showNotifications = !empty($notifications) || !empty($pendingCares);
?>

<?php require 'views/includes/header.php'; ?>

<main class="container mx-auto p-4 md:p-8">
    <div class="bg-white p-6 md:p-10 rounded-xl shadow-2xl border border-gray-100">
        
        <!-- Cabeçalho e Saudação -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b pb-6">
            <div class="flex items-center space-x-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <span class="text-2xl">🏠</span>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        Meu Jardim - Início
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">
                        <?= date('d/m/Y') ?> • 
                        <?php
                        $hora = date('H');
                        if ($hora < 12) echo 'Bom dia!';
                        elseif ($hora < 18) echo 'Boa tarde!';
                        else echo 'Boa noite!';
                        ?>
                    </p>
                </div>
            </div>
            
            <!-- Botão para pendências no cabeçalho -->
            <?php if ($summaryStats['pending_care'] > 0): ?>
                <a href="<?= BASE_URL ?>?route=care_pending" 
                   class="mt-4 md:mt-0 px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 transition duration-300 whitespace-nowrap flex items-center space-x-2">
                    <span>⏰</span>
                    <span>Cuidar das Pendências</span>
                    <span class="bg-white text-orange-600 px-2 py-1 rounded-full text-xs font-bold ml-2">
                        <?= $summaryStats['pending_care'] ?>
                    </span>
                </a>
            <?php endif; ?>
        </div>

        <!-- SEÇÃO DE RESUMO (Cards) -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Resumo do Jardim</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Card Total de Plantas -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-4 rounded-xl border border-green-200 hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $summaryStats['total_plants'] ?></p>
                            <p class="text-sm text-gray-600">Plantas</p>
                        </div>
                        <div class="w-10 h-10 bg-green-200 rounded-lg flex items-center justify-center">
                            <span class="text-lg">🌿</span>
                        </div>
                    </div>
                    <?php if ($summaryStats['total_plants'] > 0): ?>
                        <div class="mt-3 pt-3 border-t border-green-200">
                            <a href="<?= BASE_URL ?>?route=dashboard" class="text-xs text-green-600 hover:text-green-800 font-medium flex items-center">
                                Ver todas
                                <span class="ml-1">→</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Card Cuidados Pendentes -->
                <div class="bg-gradient-to-br from-orange-50 to-amber-100 p-4 rounded-xl border border-orange-200 hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $summaryStats['pending_care'] ?></p>
                            <p class="text-sm text-gray-600">Pendentes</p>
                        </div>
                        <div class="w-10 h-10 bg-orange-200 rounded-lg flex items-center justify-center">
                            <span class="text-lg">⏰</span>
                        </div>
                    </div>
                    <?php if ($summaryStats['pending_care'] > 0): ?>
                        <div class="mt-3 pt-3 border-t border-orange-200">
                            <a href="<?= BASE_URL ?>?route=care_pending" class="text-xs text-orange-600 hover:text-orange-800 font-medium flex items-center">
                                Resolver agora
                                <span class="ml-1">→</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="mt-3 pt-3 border-t border-orange-200">
                            <p class="text-xs text-orange-600 font-medium">✅ Tudo em dia!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Card Plantas Saudáveis -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-100 p-4 rounded-xl border border-blue-200 hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $summaryStats['healthy_plants'] ?></p>
                            <p class="text-sm text-gray-600">Saudáveis</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                            <span class="text-lg">💚</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <p class="text-xs text-blue-600">
                            <?= $summaryStats['total_plants'] > 0 ? 
                                round(($summaryStats['healthy_plants'] / $summaryStats['total_plants']) * 100) . '% saudáveis' : 
                                'Sem plantas' 
                            ?>
                        </p>
                    </div>
                </div>

                <!-- Card Localizações -->
                <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-4 rounded-xl border border-purple-200 hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $summaryStats['locations'] ?></p>
                            <p class="text-sm text-gray-600">Localizações</p>
                        </div>
                        <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
                            <span class="text-lg">📍</span>
                        </div>
                    </div>
                    <?php if ($summaryStats['locations'] > 0): ?>
                        <div class="mt-3 pt-3 border-t border-purple-200">
                            <p class="text-xs text-purple-600">🌎 Seu jardim</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!--  SEÇÃO DE NOTIFICAÇÕES - Mostrar se tiver pendências OU notificações -->
            <?php if ($showNotifications): ?>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <span class="mr-2">🔔</span>
                            Notificações
                        </h2>
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            <?= count($notifications) + count($pendingCares) ?>
                        </span>
                    </div>

                    <div class="space-y-3">
                        <!--  NOTIFICAÇÕES DE PENDÊNCIAS -->
                        <?php if (!empty($pendingCares)): ?>
                            <!-- Notificação de cuidados pendentes -->
                            <div class="bg-white p-4 rounded-lg border-l-4 border-orange-500 bg-orange-50 hover:shadow-sm transition duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">Cuidados Pendentes</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Você tem <strong><?= count($pendingCares) ?> cuidado(s) pendente(s)</strong> para realizar.
                                        </p>
                                        <div class="mt-2 space-y-2">
                                            <?php 
                                            // Mostrar as 2 pendências mais urgentes
                                            $urgentPendencies = array_slice($pendingCares, 0, 2);
                                            foreach ($urgentPendencies as $care): 
                                            ?>
                                                <div class="flex items-center justify-between text-xs bg-white p-2 rounded border">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="<?= $care['badge_color'] ?> px-2 py-1 rounded-full text-xs">
                                                            <?= $care['icon'] ?>
                                                        </span>
                                                        <span class="font-medium"><?= htmlspecialchars($care['plant_name']) ?></span>
                                                    </div>
                                                    <span class="text-orange-600 font-semibold">
                                                        <?= $care['days_text'] ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                            
                                            <?php if (count($pendingCares) > 2): ?>
                                                <p class="text-xs text-gray-500 text-center">
                                                    +<?= count($pendingCares) - 2 ?> mais pendente(s)
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Atualizado agora</p>
                                    </div>
                                    <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full ml-2">
                                        Pendente
                                    </span>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <a href="<?= BASE_URL ?>?route=care_pending" class="text-xs text-orange-600 hover:text-orange-800 font-medium flex items-center">
                                        Cuidar de todas as pendências
                                        <span class="ml-1">→</span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!--  OUTRAS NOTIFICAÇÕES DO SISTEMA -->
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="bg-white p-4 rounded-lg border-l-4 
                                    <?= $notification['type'] === 'urgent' ? 'border-red-500 bg-red-50' : 
                                       ($notification['type'] === 'warning' ? 'border-orange-500 bg-orange-50' : 
                                       'border-blue-500 bg-blue-50') ?> hover:shadow-sm transition duration-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-800"><?= htmlspecialchars($notification['title']) ?></p>
                                            <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($notification['message']) ?></p>
                                            <p class="text-xs text-gray-500 mt-2"><?= $notification['time'] ?></p>
                                        </div>
                                        <?php if ($notification['type'] === 'urgent'): ?>
                                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full ml-2">
                                                Urgente
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($notification['type'] === 'urgent' || $notification['type'] === 'warning'): ?>
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <a href="<?= BASE_URL ?>?route=care_pending" class="text-xs text-orange-600 hover:text-orange-800 font-medium flex items-center">
                                                Resolver pendências
                                                <span class="ml-1">→</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!--  MENSAGEM QUANDO NÃO HÁ NOTIFICAÇÕES -->
                        <?php if (empty($notifications) && empty($pendingCares)): ?>
                            <div class="text-center py-8 text-gray-500">
                                <div class="text-4xl mb-2">🎉</div>
                                <p>Tudo em dia! Não há notificações.</p>
                                <p class="text-sm mt-2">Seu jardim está bem cuidado!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!--  SE NÃO HOUVER NOTIFICAÇÕES, EXPANDIR ATIVIDADES RECENTES -->
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <span class="mr-2">📝</span>
                            Atividades Recentes
                        </h2>
                        <a href="<?= BASE_URL ?>?route=care_history" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Ver histórico completo
                        </a>
                    </div>

                    <?php if (empty($recentActivities)): ?>
                        <div class="text-center py-12 text-gray-500">
                            <div class="text-6xl mb-4">🌱</div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">
                                Bem-vindo ao Meu Jardim!
                            </h3>
                            <p class="text-gray-500 mb-6">
                                Comece adicionando suas primeiras plantas e registrando cuidados.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="<?= BASE_URL ?>?route=plant_register" 
                                   class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-300 inline-flex items-center space-x-2">
                                    <span>➕</span>
                                    <span>Nova Planta</span>
                                </a>
                                <a href="<?= BASE_URL ?>?route=care_register" 
                                   class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-300 inline-flex items-center space-x-2">
                                    <span>📝</span>
                                    <span>Registrar Cuidado</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 hover:shadow-sm transition duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-lg">
                                                <?= $activity['icon'] ?? '🌱' ?>
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($activity['description']) ?></p>
                                            <p class="text-xs text-gray-500 mt-1"><?= $activity['time'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($recentActivities) >= 6): ?>
                            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                                <a href="<?= BASE_URL ?>?route=care_history" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center">
                                    Ver todas as atividades
                                    <span class="ml-1">→</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!--  MOSTRAR ATIVIDADES RECENTES APENAS SE HOUVER NOTIFICAÇÕES -->
            <?php if ($showNotifications): ?>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                        <span class="mr-2">📝</span>
                        Atividades Recentes
                    </h2>

                    <?php if (empty($recentActivities)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📊</div>
                            <p>Nenhuma atividade recente.</p>
                            <p class="text-sm mt-2">Registre seu primeiro cuidado!</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="bg-white p-3 rounded-lg border border-gray-200 hover:shadow-sm transition duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm">
                                                <?= $activity['icon'] ?? '🌱' ?>
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800"><?= htmlspecialchars($activity['description']) ?></p>
                                            <p class="text-xs text-gray-500"><?= $activity['time'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($recentActivities) >= 5): ?>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="<?= BASE_URL ?>?route=care_history" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center">
                                    Ver todas as atividades
                                    <span class="ml-1">→</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- AÇÕES RÁPIDAS -->
        <div class="mt-8 bg-gradient-to-r from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Ações Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="<?= BASE_URL ?>?route=plant_register" 
                   class="bg-white p-4 rounded-lg border border-green-300 hover:border-green-500 hover:shadow-md transition duration-200 text-center group">
                    <div class="text-2xl mb-2 group-hover:scale-110 transition duration-200">➕</div>
                    <p class="font-medium text-gray-800">Nova Planta</p>
                    <p class="text-xs text-gray-500 mt-1">Adicionar ao jardim</p>
                </a>
                
                <a href="<?= BASE_URL ?>?route=care_register" 
                   class="bg-white p-4 rounded-lg border border-blue-300 hover:border-blue-500 hover:shadow-md transition duration-200 text-center group">
                    <div class="text-2xl mb-2 group-hover:scale-110 transition duration-200">📝</div>
                    <p class="font-medium text-gray-800">Registrar Cuidado</p>
                    <p class="text-xs text-gray-500 mt-1">Novo registro</p>
                </a>
                
                <a href="<?= BASE_URL ?>?route=dashboard" 
                   class="bg-white p-4 rounded-lg border border-purple-300 hover:border-purple-500 hover:shadow-md transition duration-200 text-center group">
                    <div class="text-2xl mb-2 group-hover:scale-110 transition duration-200">📊</div>
                    <p class="font-medium text-gray-800">Ver Todas</p>
                    <p class="text-xs text-gray-500 mt-1">Plantas e cuidados</p>
                </a>
            </div>
        </div>

        <!-- DICA DO DIA -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-100 p-6 rounded-xl border border-blue-200">
            <div class="flex items-start space-x-4">
                <div class="bg-blue-200 p-3 rounded-lg">
                    <span class="text-xl">💡</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Dica do Dia</h3>
                    <p class="text-gray-600">
                        <?php
                        $dicas = [
                            "Regue suas plantas pela manhã para evitar fungos à noite.",
                            "Verifique as folhas regularmente para identificar pragas cedo.",
                            "Use adubo orgânico a cada 30 dias para plantas mais saudáveis.",
                            "Rotacione as plantas de local para garantir luz uniforme.",
                            "Limpe as folhas com pano úmido para melhor fotossíntese."
                        ];
                        echo $dicas[array_rand($dicas)];
                        ?>
                    </p>
                    <?php if ($summaryStats['pending_care'] > 0): ?>
                        <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                            <p class="text-sm text-orange-800 font-medium flex items-center">
                                <span class="mr-2">⚡</span>
                                Você tem <strong><?= $summaryStats['pending_care'] ?> cuidado(s) pendente(s)</strong>. 
                                <a href="<?= BASE_URL ?>?route=care_pending" class="underline ml-1">Cuidar das pendências</a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<?php require 'views/includes/footer.php'; ?>