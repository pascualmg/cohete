<?php

define('LLAMA_API_ENDPOINT', 'http://localhost:11434/api/generate');
define('API_KEY', 'your_api_key_here');
define('MAX_TOKENS', 131072);
define('WARNING_THRESHOLD', 0.9); // Advertencia cuando se alcanza el 90% del límite

function scanDirectory($dir) {
    $result = [];
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        if ($file->isFile()) {
            $path = $file->getPathname();
            $relativePath = str_replace($dir . DIRECTORY_SEPARATOR, '', $path);
            $result[$relativePath] = [
                'path' => $relativePath,
                'content' => file_get_contents($path),
                'size' => $file->getSize(),
            ];
        }
    }

    return $result;
}

function prepareContext($files, $maxTokens) {
    $context = '';
    $totalTokens = 0;
    $filesIncluded = 0;
    $totalFiles = count($files);

    foreach ($files as $file) {
        $fileContent = "File: {$file['path']}\n\n{$file['content']}\n\n";
        $tokenEstimate = strlen($fileContent) / 4; // Estimación aproximada de tokens

        if ($totalTokens + $tokenEstimate > $maxTokens) {
            echo "Advertencia: Se alcanzó el límite de tokens. No se pudieron incluir todos los archivos.\n";
            echo "Archivos incluidos: $filesIncluded de $totalFiles\n";
            break;
        }

        $context .= $fileContent;
        $totalTokens += $tokenEstimate;
        $filesIncluded++;

        if ($totalTokens >= $maxTokens * WARNING_THRESHOLD && $filesIncluded < $totalFiles) {
            echo "Advertencia: Se ha alcanzado el " . (WARNING_THRESHOLD * 100) . "% del límite de tokens.\n";
        }
    }

    $tokenUsagePercentage = ($totalTokens / $maxTokens) * 100;
    echo "Uso de tokens: " . number_format($totalTokens, 0) . " de " . number_format($maxTokens, 0) . " (" . number_format($tokenUsagePercentage, 2) . "%)\n";
    echo "Archivos incluidos en el contexto: $filesIncluded de $totalFiles\n";

    return $context;
}
function queryLlama($context, $instruction) {
    $headers = [
        'Content-Type: application/json'
    ];
    $data = [
        'model' => 'llama2',
        'prompt' => "Project Context:\n$context\n\nInstruction: $instruction",
        'stream' => false
    ];

    $ch = curl_init(LLAMA_API_ENDPOINT);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    var_dump($response);

    if (curl_errno($ch)) {
        echo 'Error en la solicitud cURL: ' . curl_error($ch) . "\n";
        return null;
    }

    curl_close($ch);

    $responseData = json_decode($response, true);
    return $responseData['response'] ?? null;
}

// Escanear el directorio actual
$currentDir = getcwd();
echo "Escaneando el directorio: $currentDir\n";
$files = scanDirectory($currentDir);

// Preparar el contexto
$context = prepareContext($files, MAX_TOKENS);

// Bucle principal
while (true) {
    $instruction = readline("Ingresa tu instrucción (o 'salir' para terminar): ");
    if (strtolower($instruction) === 'salir') {
        break;
    }

    $response = queryLlama($context, $instruction);
    if ($response !== null) {
        echo "\nRespuesta de Llama 3.1:\n";
        echo $response . "\n\n";
    } else {
        echo "No se pudo obtener una respuesta del modelo.\n";
    }
}

echo "¡Gracias por usar el asistente Llama 3.1!\n";

?>
