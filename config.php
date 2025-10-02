<?php
// config.php

// Define o nome da aplicação (usado no <title>)
define('APP_NAME', 'Meu Jardim Virtual');

/**
 * Define a URL base para links e redirecionamentos.
 * Usa o nome da pasta do projeto no XAMPP/htdocs.
 */
// URL Completa de Acesso (Deve coincidir com a pasta no htdocs do XAMPP)
define('BASE_URL', '/meu_jardim_virtual/index.php');


// --- Configurações do Banco de Dados (MySQL) ---
// **IMPORTANTE:** No XAMPP, a senha é geralmente vazia ('').
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_plantas'); // Certifique-se que este BD existe no phpMyAdmin
define('DB_USER', 'root');
define('DB_PASS', ''); 


// --- Configurações de Hash e Segurança ---
// Define o algoritmo de hash usado para armazenar senhas.
// PASSWORD_DEFAULT é o mais recomendado.
define('PASSWORD_ALGORITHM', PASSWORD_DEFAULT);
