<?php
// views/protected/dashboard.php
// Esta view é carregada pelo PlantController::index()

// Assume que as variáveis $plants, $searchQuery, e BASE_URL estão definidas no Controller.
// O Controller deve garantir que $plants seja um array (mesmo que vazio) e $searchQuery uma string.

// 1. Inclui o cabeçalho
require 'views/includes/header.php'; 

// Ajuste nas variáveis por segurança
$plants = $plants ?? [];
$searchQuery = $searchQuery ?? '';
?>

<main class="container mx-auto p-4 md:p-8">
    <div class="bg-white p-6 md:p-10 rounded-xl shadow-2xl border border-gray-100">
        
        <!-- Cabeçalho e Botão -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b pb-6">
            <div class="flex items-center space-x-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <span class="text-2xl">🌱</span>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        Meu Jardim
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">Dashboard de plantas</p>
                </div>
            </div>
            
            <a href="<?= BASE_URL ?>?route=plant_register" 
               class="mt-4 md:mt-0 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-300 whitespace-nowrap flex items-center space-x-2">
                <span>+</span>
                <span>Cadastrar Nova Planta</span>
            </a>
        </div>

        <!-- Barra de Pesquisa -->
        <div class="mb-8">
            <form action="<?= BASE_URL ?>?route=dashboard" method="GET" class="relative">
                <input type="hidden" name="route" value="dashboard">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Pesquisar por Nome, Espécie ou Localização..." 
                        value="<?= htmlspecialchars($searchQuery) ?>"
                        class="w-full px-6 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-green-600 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <?php if (empty($plants)): ?>
            <!-- Estado Vazio -->
            <div class="text-center py-12 px-6 bg-gradient-to-br from-gray-50 to-green-50 rounded-2xl border-2 border-dashed border-gray-300">
                <div class="max-w-md mx-auto">
                    <div class="text-6xl mb-4">🌿</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">
                        Nenhuma planta encontrada
                    </h3>
                    <p class="text-gray-500 mb-6">
                        Comece adicionando sua primeira planta ao jardim
                    </p>
                    <a href="<?= BASE_URL ?>?route=plant_register" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-300">
                        <span class="mr-2">+</span>
                        Cadastrar Primeira Planta
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Lista de Plantas -->
            <div class="overflow-hidden shadow-lg rounded-2xl border border-gray-200">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-50 to-gray-50 border-b border-gray-200">
                            <th class="py-4 px-6 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm">
                                Nome
                            </th>
                            <th class="py-4 px-6 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm hidden sm:table-cell">
                                Espécie
                            </th>
                            <th class="py-4 px-6 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm hidden md:table-cell">
                                Localização
                            </th>
                            <th class="py-4 px-6 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm hidden lg:table-cell">
                                Aquisição
                            </th>
                            <th class="py-4 px-6 text-center text-gray-700 font-semibold uppercase tracking-wider text-sm">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($plants as $plant): ?>
                            <tr class="hover:bg-green-50/30 transition duration-200 ease-in-out group">
                                <td class="py-4 px-6 text-left">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition duration-200">
                                            <span class="text-lg">🌿</span>
                                        </div>
                                        <span class="font-medium text-gray-800">
                                            <?= htmlspecialchars($plant['name']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-left hidden sm:table-cell">
                                    <span class="text-gray-600">
                                        <?= htmlspecialchars($plant['species'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-left hidden md:table-cell">
                                    <span class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-800 rounded-full text-xs font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <?= htmlspecialchars($plant['location']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-left hidden lg:table-cell">
                                    <span class="text-gray-500 text-sm">
                                        <?= date('d/m/Y', strtotime($plant['acquisition_date'])) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <!-- Botão Ver Detalhes -->
                                        <a href="<?= BASE_URL ?>?route=plant_details&id=<?= $plant['id'] ?>" 
                                           title="Ver Detalhes" 
                                           class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-100 rounded-lg transition duration-200 transform hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        
                                         <!-- Botão Editar (JÁ TEM O LÁPIS) -->
                            <a href="<?= BASE_URL ?>?route=plant_edit&id=<?= $plant['id'] ?>" 
                            title="Editar" 
                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-100 rounded-lg transition duration-200 transform hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l-2 2M18 9l2-2m-2-2h-3.586a1 1 0 00-.707.293l-1.414 1.414a1 1 0 00-.293.707V13m-2-4h4m-4 4h.01"/>
                                </svg>
                            </a>
                                        
                                        <!-- Botão Excluir -->
                                        <a href="<?= BASE_URL ?>?route=plant_delete&id=<?= $plant['id'] ?>" 
                                           title="Excluir" 
                                           onclick="return confirm('Tem certeza que deseja excluir esta planta?')" 
                                           class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-100 rounded-lg transition duration-200 transform hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
</main>

<?php 
// 2. Inclui o rodapé
require 'views/includes/footer.php'; 
?>