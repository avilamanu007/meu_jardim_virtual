<?php 
// views/includes/footer.php
// Este arquivo encerra a estrutura HTML e inclui o rodapé.
?>
    
    </main> <!-- Fecha a tag <main> que foi aberta no header -->

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Footer apenas para usuários logados -->
        <footer class="bg-white shadow-inner border-t border-gray-100 mt-8 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-gray-500">
                    &copy; <?= date('Y') ?> <?= htmlspecialchars(defined('APP_NAME') ? APP_NAME : 'Meu Jardim Virtual') ?>. 
                    Todos os direitos reservados.
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Cultivando sonhos verdes 🌱
                </p>
            </div>
        </footer>
    <?php else: ?>
        <!-- Footer para páginas públicas (login/registro) -->
        <footer class="bg-gray-50 border-t border-gray-200 mt-auto py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-gray-600">
                    &copy; <?= date('Y') ?> <?= htmlspecialchars(defined('APP_NAME') ? APP_NAME : 'Meu Jardim Virtual') ?>
                </p>
            </div>
        </footer>
    <?php endif; ?>

    <!-- Scripts JavaScript (opcional) -->
    <script>
        // Scripts globais podem vir aqui
        console.log('<?= defined('APP_NAME') ? APP_NAME : 'Sistema' ?> carregado com sucesso!');
    </script>
</body>
</html>