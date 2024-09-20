<?php

class OllamaAPI
{
    private $baseUrl;

    public function __construct($baseUrl = 'http://localhost:11434')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    private function sendRequest($endpoint, $data = [], $method = 'POST', $encoded = true)
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => "cURL Error: $error"];
        }
        if ("404 page not found" == $response) {
            return ['error' => "404 page not found"];
        }

        return $encoded ? json_decode($response, true, 512, JSON_THROW_ON_ERROR) : $response;
    }

    public function generateCompletion($model, $prompt, $options = null, $context = null)
    {
        $data = [
            'model' => $model,
            'prompt' => $prompt,
            'async' => false
        ];
        if (!is_null($options)) {
            $data['options'] = $options;
        }
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        $rawResponse = $this->sendRequest('/api/generate', $data, 'POST', false);
        return $this->extractText($rawResponse);
    }

    public function generateChatCompletion($model, $messages, $options = null, $context = null)
    {
        $data = [
            'model' => $model,
            'messages' => $messages
        ];
        if (!is_null($options)) {
            $data['options'] = $options;
        }
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        $rawResponse = $this->sendRequest('/api/chat', $data, 'POST', false);
        return $this->extractText($rawResponse);
    }

    public function createModel($name, $modelfile, $path = null, $context = null)
    {
        $data = [
            'name' => $name,
            'modelfile' => $modelfile
        ];
        if (!is_null($path)) {
            $data['path'] = $path;
        }
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/create', $data, 'POST', false);
    }

    public function listModels($context = null)
    {
        $data = [];
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/tags', $data, 'GET', false);
    }

    public function showModelInfo($name, $context = null)
    {
        $data = ['name' => $name];
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/show', $data, 'POST', false);
    }

    public function copyModel($source, $destination, $context = null)
    {
        $data = [
            'source' => $source,
            'destination' => $destination
        ];
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/copy', $data, 'POST', false);
    }

    public function deleteModel($name, $context = null)
    {
        $data = ['name' => $name];
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/delete', $data, 'DELETE', false);
    }

    public function pullModel($name, $insecure = false, $context = null)
    {
        $data = [
            'name' => $name,
            'insecure' => $insecure
        ];
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/pull', $data, 'POST', false);
    }

    public function pushModel($name, $insecure = false, $context = null)
    {
        $data = [
            'name' => $name,
            'insecure' => $insecure
        ];
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/push', $data, 'POST', false);
    }

    public function generateEmbeddings($model, $prompt, $options = null, $context = null)
    {
        $data = [
            'model' => $model,
            'prompt' => $prompt
        ];
        if (!is_null($options)) {
            $data['options'] = $options;
        }
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        $rawResponse = $this->sendRequest('/api/embeddings', $data, 'POST', false);
        return $this->extractText($rawResponse);
    }

    public function listRunningModels($context = null)
    {
        $data = [];
        if (!is_null($context)) {
            $data['context'] = $context;
        }
        return $this->sendRequest('/api/ps', $data, 'GET', false);
    }

    private function extractText($rawResponse): string
    {
        $lines = explode("\n", $rawResponse);
        $text = '';
        try {
            $text = array_reduce($lines, static function ($carry, $line) {
                if ("" === $line) {
                    return $carry;
                }
                $json = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                if (isset($json['response'])) {
                    $carry .= $json['response'];
                }
                return $carry;
            }, '');
        } catch (\Throwable $th) {
            return $text;
        }
        return $text;
    }
}

// Ejemplo de uso
$api = new OllamaAPI();

// Generar una completación
echo "Generando completación...\n";
$completion = $api->generateCompletion('llama3.1', 'genera un json con comandos de linux de consola basicos, 3 o 4 comandos');
print_r($completion);
readline("Presiona Enter para continuar...");

// Generar una respuesta de chat
echo "\nGenerando respuesta de chat...\n";
$messages = [
    ['role' => 'user', 'content' => '¿Cuál es la capital de Francia?']
];
$chatResponse = $api->generateChatCompletion('llama3.1', $messages);
print_r($chatResponse);
readline("Presiona Enter para continuar...");

// Crear un modelo
echo "\nCreando un modelo...\n";
$createModelResponse = $api->createModel('mi-modelo', 'FROM llama3.1\nSYSTEM You are a helpful assistant.');
print_r($createModelResponse);
readline("Presiona Enter para continuar...");

// Listar modelos disponibles
echo "\nListando modelos disponibles...\n";
$models = $api->listModels();
print_r($models);
readline("Presiona Enter para continuar...");

// Obtener información de un modelo
echo "\nObteniendo información del modelo...\n";
$modelInfo = $api->showModelInfo('llama3.1');
print_r($modelInfo);
readline("Presiona Enter para continuar...");

// Copiar un modelo
echo "\nCopiando un modelo...\n";
$copyModelResponse = $api->copyModel('llama3.1', 'llama3.1-copia');
print_r($copyModelResponse);
readline("Presiona Enter para continuar...");

// Eliminar un modelo
echo "\nEliminando un modelo...\n";
$deleteModelResponse = $api->deleteModel('llama3.1-copia');
print_r($deleteModelResponse);
readline("Presiona Enter para continuar...");

// Descargar un modelo
echo "\nDescargando un modelo...\n";
$pullModelResponse = $api->pullModel('llama3.1');
print_r($pullModelResponse);
readline("Presiona Enter para continuar...");

// Subir un modelo
echo "\nSubiendo un modelo...\n";
$pushModelResponse = $api->pushModel('mi-modelo:latest');
print_r($pushModelResponse);
readline("Presiona Enter para continuar...");

// Generar embeddings
echo "\nGenerando embeddings...\n";
$embeddingsResponse = $api->generateEmbeddings('llama3.1', 'Este es un texto de ejemplo');
print_r($embeddingsResponse);
readline("Presiona Enter para continuar...");

// Listar modelos en ejecución
echo "\nListando modelos en ejecución...\n";
$runningModels = $api->listRunningModels();
print_r($runningModels);
readline("Presiona Enter para continuar...");