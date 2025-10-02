<?php 
// views/protected/plant_edit.php
$errors = $errors ?? [];
$plantData = $plantData ?? [
    'name' => '',
    'species' => '', 
    'acquisition_date' => '',
    'location' => ''
];
$plantId = $_GET['id'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Planta - Meu Jardim</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen p-4">

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow-lg">
        
        <!-- Cabeçalho -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">✏️ Editar Planta</h1>
            <p class="text-gray-600 mt-1">Atualize os dados da sua planta</p>
        </div>

        <!-- Link Voltar -->
        <div class="mb-6">
            <a href="<?= BASE_URL ?>?route=dashboard" class="text-green-600 hover:text-green-800 font-medium text-sm">
                &larr; Voltar ao Dashboard
            </a>
        </div>

        <!-- Mensagens de Erro -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Erros no formulário:
                </div>
                <?php foreach ($errors as $error): ?>
                    <div class="ml-2">• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form action="<?= BASE_URL ?>?route=plant_edit&id=<?= $plantId ?>" method="POST" class="space-y-4">
            
            <!-- Nome da Planta -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Nome da Planta *</label>
                <input 
                    type="text" 
                    name="name" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    value="<?= htmlspecialchars($plantData['name']) ?>"
                    placeholder="Ex: Rosa, Suculenta, Samambaia..."
                    required
                >
            </div>

            <!-- Espécie -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Espécie *</label>
                <input 
                    type="text" 
                    name="species" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    value="<?= htmlspecialchars($plantData['species']) ?>"
                    placeholder="Ex: Rosa gallica, Echeveria elegans..."
                    required
                >
            </div>
            
            <!-- Data de Aquisição -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Data de Aquisição *</label>
                <input 
                    type="date" 
                    name="acquisition_date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    value="<?= htmlspecialchars($plantData['acquisition_date']) ?>"
                    max="<?= date('Y-m-d') ?>"
                    required
                >
            </div>
            
            <!-- Localização -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Localização *</label>
                <input 
                    type="text" 
                    name="location" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    value="<?= htmlspecialchars($plantData['location']) ?>"
                    placeholder="Ex: Sala, Quarto, Varanda, Jardim..."
                    required
                >
            </div>
            
            <!-- Botões -->
            <div class="flex space-x-4 pt-4">
                <a href="<?= BASE_URL ?>?route=dashboard" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2.5 rounded-lg transition duration-200 text-center">
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg transition duration-200"
                >
                    Atualizar Planta
                </button>
            </div>
        </form>
    </div>

</body>
</html>