<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Meu Jardim Virtual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0fdf4; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border border-green-200">
        
        <!-- Cabeçalho com nome do sistema -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-green-700">Meu Jardim Virtual</h1>
            <h2 class="text-lg font-semibold text-gray-800 mt-2">Criar sua conta</h2>
            <p class="text-gray-600 text-sm mt-1">Cadastre-se para começar a gerenciar suas plantas</p>
        </div>

        <!-- Exibição de Erros Gerais -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Erro no cadastro:
                </div>
                <?php foreach ($errors as $error): ?>
                    <div class="ml-2">• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>?route=register">
            
            <!-- Nome -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                <input type="text" id="name" name="name"
                       value="<?= htmlspecialchars($name ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($email ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            <!-- Senha -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha (mín. 6 caracteres)</label>
                <input type="password" id="password" name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            <!-- Confirmar Senha -->
            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-150">
                Cadastrar
            </button>
        </form>

        <!-- Link Login -->
        <div class="text-center mt-6 pt-4 border-t border-gray-200">
            <p class="text-gray-600 text-sm">
                Já tem conta? 
                <a href="<?= BASE_URL ?>?route=login" class="text-green-600 hover:text-green-800 font-medium">
                    Fazer Login
                </a>
            </p>
        </div>
    </div>
</body>
</html>