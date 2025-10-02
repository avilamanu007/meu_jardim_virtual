<?php 
require 'views/includes/header.php'; 
$errors = $errors ?? [];
$careData = $careData ?? [
    'plant_id' => '',
    'care_type' => '',
    'care_date' => date('Y-m-d'),
    'observations' => '',
    'next_maintenance_date' => ''
];
$plants = $plants ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cuidado - Meu Jardim</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen p-4">

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow-lg">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">💧 Registrar Cuidado</h1>
            <p class="text-gray-600 mt-1">Registre um cuidado para sua planta</p>
        </div>

        <div class="mb-6">
            <a href="<?= BASE_URL ?>?route=dashboard" class="text-green-600 hover:text-green-800 font-medium text-sm">
                &larr; Voltar ao Dashboard
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <?php foreach ($errors as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>?route=care_register" method="POST" class="space-y-4">
            
            <!-- Selecionar Planta -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Planta *</label>
                <select name="plant_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    <option value="">Selecione uma planta</option>
                    <?php foreach ($plants as $plant): ?>
                        <option value="<?= $plant['id'] ?>" <?= $plant['id'] == $careData['plant_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($plant['name']) ?> - <?= htmlspecialchars($plant['species']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tipo de Cuidado -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Tipo de Cuidado *</label>
                <select name="care_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    <option value="">Selecione o tipo</option>
                    <option value="rega" <?= $careData['care_type'] == 'rega' ? 'selected' : '' ?>>🌱 Rega (3 dias)</option>
                    <option value="adubacao" <?= $careData['care_type'] == 'adubacao' ? 'selected' : '' ?>>🧪 Adubação (30 dias)</option>
                    <option value="poda" <?= $careData['care_type'] == 'poda' ? 'selected' : '' ?>>✂️ Poda (90 dias)</option>
                    <option value="transplante" <?= $careData['care_type'] == 'transplante' ? 'selected' : '' ?>>🔄 Transplante (180 dias)</option>
                    <option value="tratamento" <?= $careData['care_type'] == 'tratamento' ? 'selected' : '' ?>>💊 Tratamento (14 dias)</option>
                    <option value="limpeza" <?= $careData['care_type'] == 'limpeza' ? 'selected' : '' ?>>🧹 Limpeza (15 dias)</option>
                    <option value="outro" <?= $careData['care_type'] == 'outro' ? 'selected' : '' ?>>📝 Outro (30 dias)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">A próxima manutenção será calculada automaticamente</p>
            </div>

            <!-- Data do Cuidado -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Data do Cuidado *</label>
                <input 
                    type="date" 
                    name="care_date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    value="<?= htmlspecialchars($careData['care_date']) ?>"
                    max="<?= date('Y-m-d') ?>"
                    required
                >
            </div>

            <!-- Próxima Manutenção (APENAS LEITURA) -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Próxima Manutenção</label>
                <input 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                    value="<?= htmlspecialchars($careData['next_maintenance_date']) ?>"
                    readonly
                    disabled
                >
                <p class="text-xs text-gray-500 mt-1">Calculada automaticamente baseada no tipo de cuidado</p>
            </div>

            <!-- Observações -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Observações</label>
                <textarea 
                    name="observations" 
                    rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Ex: Planta estava com folhas amarelas, usei adubo orgânico..."
                ><?= htmlspecialchars($careData['observations']) ?></textarea>
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg transition duration-200 mt-6"
            >
                Registrar Cuidado
            </button>
        </form>
    </div>

</body>
</html>
<?php require 'views/includes/footer.php'; ?>