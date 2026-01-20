<?php

/**
 * Teste SIMPLES de conexão com a API MyAnimeList
 * Apenas verifica se consegue conectar - não busca dados
 */

// Ler o Client ID do .env
$envFile = __DIR__ . '/../.env';
$clientId = null;

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'MAL_CLIENT_ID=') === 0) {
            $clientId = trim(str_replace('MAL_CLIENT_ID=', '', $line));
            break;
        }
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  TESTE DE CONEXÃO - API MYANIMELIST\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// 1. Verificar Client ID
echo "1. Verificando Client ID...\n";
if (!$clientId || $clientId === 'your_client_id_here') {
    echo "   ✗ Client ID não configurado!\n\n";
    exit(1);
}
echo "   ✓ Client ID encontrado: " . substr($clientId, 0, 10) . "...\n\n";

// 2. Verificar se cURL está disponível
echo "2. Verificando cURL...\n";
if (!function_exists('curl_init')) {
    echo "   ✗ cURL não está disponível!\n\n";
    exit(1);
}
echo "   ✓ cURL disponível\n\n";

// 3. Testar conexão básica (apenas HEAD request)
echo "3. Testando conexão com api.myanimelist.net...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.myanimelist.net/v2/anime/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request apenas
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitar verificação SSL para teste
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-MAL-CLIENT-ID: ' . $clientId
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ✗ Erro cURL: $error\n\n";
    exit(1);
}

if ($httpCode === 0) {
    echo "   ✗ Não foi possível conectar ao servidor\n";
    echo "   Verifique sua conexão com a internet\n\n";
    exit(1);
}

echo "   ✓ Conexão estabelecida! (HTTP $httpCode)\n\n";

// 4. Testar com requisição GET completa
echo "4. Testando autenticação com a API...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.myanimelist.net/v2/anime/1?fields=id,title');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-MAL-CLIENT-ID: ' . $clientId
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ✗ Erro cURL: $error\n\n";
    exit(1);
}

echo "   HTTP Status: $httpCode\n";

if ($httpCode === 200) {
    echo "   ✓ Autenticação bem-sucedida!\n";
    $data = json_decode($response, true);
    if ($data && isset($data['title'])) {
        echo "   ✓ Dados recebidos: " . $data['title'] . "\n\n";
    }
} elseif ($httpCode === 401) {
    echo "   ✗ Erro de autenticação - Client ID inválido\n\n";
    exit(1);
} elseif ($httpCode === 403) {
    echo "   ✗ Acesso negado - Verifique as permissões do Client ID\n\n";
    exit(1);
} else {
    echo "   ✗ Erro inesperado\n";
    echo "   Response: $response\n\n";
    exit(1);
}

// Sucesso!
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  ✓ CONEXÃO COM A API ESTABELECIDA COM SUCESSO!        ║\n";
echo "║  Você pode usar o sistema normalmente.                ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

exit(0);
