<?php
// views/protected/dashboard.php
// Esta view é carregada pelo PlantController::index()

// Inclui o cabeçalho e menu de navegação
require 'views/includes/header.php'; 

// Formata a data para o padrão brasileiro
$formatDate = function($date) {
    return date('d/m/Y', strtotime($date));
};
?>

<div class="min-h-screen bg-gradient-to-br from-green-50 to-gray-50 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            
            <!-- Cabeçalho -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-8 sm:px-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-sm">
                            <span class="text-3xl">🌿</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">
                                Minhas Plantas
                            </h1>
                            <p class="text-green-100 mt-1">Gerencie seu jardim virtual</p>
                        </div>
                    </div>
                    
                    <a href="<?= BASE_URL ?>?route=plant_register" 
                       class="bg-white text-green-600 hover:bg-gray-50 font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center space-x-2 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Nova Planta</span>
                    </a>
                </div>
            </div>

            <!-- Conteúdo -->
            <div class="p-6 sm:p-8">
                <!-- Mensagens de Sucesso -->
                <?php if (isset($_GET['success'])): ?>
                    <?php 
                        $message = '';
                        $type = $_GET['success'];
                        if ($type == 'plant_created') {
                            $message = "Planta cadastrada com sucesso!";
                        } elseif ($type == 'plant_updated') {
                            $message = "Planta atualizada com sucesso!";
                        } elseif ($type == 'plant_deleted') {
                            $message = "Planta excluída com sucesso!";
                        }
                    ?>
                    <?php if (!empty($message)): ?>
                        <div class="mb-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-lg animate-fade-in">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold"><?= htmlspecialchars($message) ?></span>
                                </div>
                                <button onclick="this.parentElement.parentElement.remove()" class="text-white/80 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Barra de Pesquisa -->
                <div class="mb-8">
                    <form method="GET" action="<?= BASE_URL ?>" class="relative max-w-2xl mx-auto">
                        <input type="hidden" name="route" value="dashboard">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="search" 
                                name="search" 
                                placeholder="Pesquisar por nome, espécie ou localização..." 
                                value="<?= htmlspecialchars($searchQuery) ?>"
                                class="w-full pl-12 pr-20 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 bg-gray-50/50 backdrop-blur-sm"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <?php if (!empty($searchQuery)): ?>
                                    <a href="<?= BASE_URL ?>?route=dashboard" 
                                       class="mr-2 text-gray-400 hover:text-red-500 transition duration-200 p-1"
                                       title="Limpar Pesquisa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <button type="submit" 
                                        class="h-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-r-xl transition duration-200 flex items-center space-x-2">
                                    <span>Buscar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Lista de Plantas -->
                <?php if (empty($plants)): ?>
                    <!-- Estado Vazio -->
                    <div class="text-center py-16 px-6 bg-gradient-to-br from-gray-50 to-green-50 rounded-2xl border-2 border-dashed border-gray-300">
                        <div class="max-w-md mx-auto">
                            <div class="text-8xl mb-6 opacity-60">🌱</div>
                            <h3 class="text-2xl font-bold text-gray-700 mb-3">
                                Jardim Vazio
                            </h3>
                            <p class="text-gray-500 mb-8 text-lg">
                                Sua coleção de plantas está esperando por você!
                            </p>
                            <a href="<?= BASE_URL ?>?route=plant_register" 
                               class="inline-flex items-center px-8 py-4 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Cadastrar Primeira Planta
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Tabela de Plantas -->
                    <div class="overflow-hidden rounded-xl shadow-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-green-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                                        Planta
                                    </th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                        Espécie
                                    </th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider hidden md:table-cell">
                                        Local
                                    </th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">
                                        Aquisição
                                    </th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($plants as $plant): ?>
                                    <tr class="hover:bg-green-50/30 transition duration-150 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition duration-200">
                                                    <span class="text-xl">🌿</span>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900 text-lg">
                                                        <?= htmlspecialchars($plant['name']) ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500 sm:hidden">
                                                        <?= htmlspecialchars($plant['species']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 hidden sm:table-cell">
                                            <?= htmlspecialchars($plant['species']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 hidden md:table-cell">
                                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <?= htmlspecialchars($plant['location']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                            <?= $formatDate($plant['acquisition_date']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-3">
                                                <!-- Visualizar -->
                                                <a href="<?= BASE_URL ?>?route=plant_details&id=<?= $plant['id'] ?>" 
                                                   class="text-blue-500 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition duration-200 transform hover:scale-110"
                                                   title="Ver Detalhes">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <!-- Editar -->
                                                <a href="<?= BASE_URL ?>?route=plant_edit&id=<?= $plant['id'] ?>" 
                                                   class="text-yellow-500 hover:text-yellow-700 p-2 hover:bg-yellow-50 rounded-lg transition duration-200 transform hover:scale-110"
                                                   title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l-2 2M18 9l2-2m-2-2h-3.586a1 1 0 00-.707.293l-1.414 1.414a1 1 0 00-.293.707V13m-2-4h4m-4 4h.01"/>
                                                    </svg>
                                                </a>
                                                <!-- Excluir -->
                                                <a href="<?= BASE_URL ?>?route=plant_delete&id=<?= $plant['id'] ?>" 
                                                   onclick="return confirm('Tem certeza que deseja excluir esta planta?')"
                                                   class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition duration-200 transform hover:scale-110"
                                                   title="Excluir">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.86 13.73A2 2 0 0116.14 22H7.86a2 2 0 01-1.99-2.27L5 7m14 0h-14m2-4h10m-5 4V3"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require 'views/includes/footer.php'; ?>