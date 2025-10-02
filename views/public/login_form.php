<?php 
// views/public/login_form.php
$errors = $errors ?? []; 
$email = $email ?? '';
$successMessage = $successMessage ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Meu Jardim Virtual</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-6 rounded-2xl shadow-lg w-full max-w-md">
        
        <!-- Cabeçalho com nome do sistema -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-green-700">Meu Jardim Virtual</h1>
            <h2 class="text-lg font-semibold text-gray-800 mt-2">Entrar na sua conta</h2>
            <p class="text-gray-600 text-sm mt-1">Acesse e gerencie suas plantas</p>
        </div>

        <!-- Mensagem de Sucesso -->
        <?php if (!empty($successMessage)): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Mensagens de Erro -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Erro no login:
                </div>
                <?php foreach ($errors as $error): ?>
                    <div class="ml-2">• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulário Compacto -->
        <form action="<?= BASE_URL ?>?route=login" method="POST" class="space-y-4">
            
            <!-- Email -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    value="<?= htmlspecialchars($email) ?>"
                    required
                >
            </div>

            <!-- Senha -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Senha</label>
                <input 
                    type="password" 
                    name="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    required
                >
            </div>
            
            <!-- Botão -->
            <button 
                type="submit" 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg transition duration-200"
            >
                Entrar
            </button>
        </form>

        <!-- Link Cadastro -->
        <div class="text-center mt-6 pt-4 border-t border-gray-200">
            <p class="text-gray-600 text-sm">
                Não tem conta? 
                <a href="<?= BASE_URL ?>?route=register" class="text-green-600 hover:text-green-800 font-medium">
                    Cadastre-se
                </a>
            </p>
        </div>
    </div>

</body>
</html>