<?php
require 'views/includes/header.php';
?>

<main class="container mx-auto p-4 md:p-8">
    <div class="bg-white p-6 md:p-10 rounded-xl shadow-2xl border border-gray-100">
        
        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b pb-6">
            <div class="flex items-center space-x-3">
                <div class="bg-orange-100 p-2 rounded-lg">
                    <span class="text-2xl">⏰</span>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        Cuidados Pendentes
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">Registre os cuidados realizados</p>
                </div>
            </div>
            
            <a href="<?= BASE_URL ?>?route=care_register" 
               class="mt-4 md:mt-0 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-300 whitespace-nowrap flex items-center space-x-2">
                <span>+</span>
                <span>Novo Cuidado</span>
            </a>
        </div>

        <!-- Mensagens -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Lista de Pendências -->
        <?php if (empty($pendingCares)): ?>
            <div class="text-center py-12 px-6 bg-gradient-to-br from-gray-50 to-green-50 rounded-2xl border-2 border-dashed border-gray-300">
                <div class="max-w-md mx-auto">
                    <div class="text-6xl mb-4">🎉</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">
                        Nenhum cuidado pendente!
                    </h3>
                    <p class="text-gray-500 mb-6">
                        Todas as suas plantas estão em dia com os cuidados.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($pendingCares as $care): ?>
                    <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition duration-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-xl">
                                    <?= $care['icon'] ?>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            <?= htmlspecialchars($care['plant_name']) ?>
                                        </h3>
                                        <span class="md:ml-4 px-3 py-1 rounded-full text-sm font-medium <?= $care['badge_color'] ?>">
                                            <?= $care['status'] ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mt-1">
                                        <span class="font-medium"><?= $care['care_type'] ?></span> • 
                                        <?= $care['days_text'] ?> • 
                                        <?= htmlspecialchars($care['location']) ?>
                                    </p>
                                    <?php if ($care['observations']): ?>
                                        <p class="text-sm text-gray-500 mt-2">
                                            <?= nl2br(htmlspecialchars($care['observations'])) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mt-4 md:mt-0 md:ml-6">
                                <button 
                                    onclick="openModal(<?= $care['care_id'] ?>, '<?= htmlspecialchars($care['plant_name']) ?>', '<?= $care['care_type'] ?>')"
                                    class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-200 whitespace-nowrap">
                                    Registrar Feito
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<!-- Modal para registrar cuidado -->
<div id="careModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 m-4 max-w-md w-full">
        <h3 class="text-xl font-semibold text-gray-800 mb-4" id="modalTitle">
            Registrar Cuidado
        </h3>
        
        <form action="<?= BASE_URL ?>?route=care_complete" method="POST">
            <input type="hidden" name="care_id" id="modalCareId">
            
            <div class="mb-4">
                <p class="text-gray-600" id="modalDescription"></p>
            </div>
            
            <div class="mb-6">
                <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">
                    Observações (opcional)
                </label>
                <textarea 
                    name="observations" 
                    id="observations" 
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ex: Planta estava com folhas amarelas..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button 
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200">
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(careId, plantName, careType) {
    document.getElementById('modalCareId').value = careId;
    document.getElementById('modalTitle').textContent = `Registrar ${careType}`;
    document.getElementById('modalDescription').textContent = `Confirmar que o cuidado "${careType}" foi realizado na planta "${plantName}"?`;
    document.getElementById('careModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('careModal').classList.add('hidden');
}

// Fechar modal ao clicar fora
document.getElementById('careModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php require 'views/includes/footer.php'; ?>