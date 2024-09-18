<?php

class OllamaAPI
{
    private $baseUrl;

    public function __construct($baseUrl = 'http://localhost:11434')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    private function sendRequest(
        $endpoint,
        $data = [],
        $method = 'POST',
        $encoded = true
    ) {
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

    public function generateCompletion($model, $prompt, $options = null)
    {
        $data = [
            'model' => $model,
            'prompt' => $prompt,
            'async' => false
        ];
        if (!is_null($options)) {
            $data['options'] = $options;
        }
        $rawResponse = $this->sendRequest('/api/generate', $data, 'POST', false);
        return $this->extractText($rawResponse);
    }

    //TODO: hacer lo mismo que en la de arriba
    public function generateChatCompletion($model, $messages, $options = null)
    {
        $data = [
            'model' => $model,
            'messages' => $messages
        ];
        if (!is_null($options)) {
            $data['options'] = $options;
        }
        return $this->sendRequest('/api/chat', $data);
    }

    public function createModel($name, $modelfile, $path = null)
    {
        $data = [
            'name' => $name,
            'modelfile' => $modelfile
        ];
        if (!is_null($path)) {
            $data['path'] = $path;
        }
        return $this->sendRequest('/api/create', $data);
    }

    public function listModels()
    {
        return $this->sendRequest('/api/tags', [], 'GET');
    }

    public function showModelInfo($name)
    {
        return $this->sendRequest('/api/show', ['name' => $name]);
    }

    public function copyModel($source, $destination)
    {
        $data = [
            'source' => $source,
            'destination' => $destination
        ];
        return $this->sendRequest('/api/copy', $data);
    }

    public function deleteModel($name)
    {
        return $this->sendRequest('/api/delete', ['name' => $name], 'DELETE');
    }

    public function pullModel($name, $insecure = false)
    {
        $data = [
            'name' => $name,
            'insecure' => $insecure
        ];
        return $this->sendRequest('/api/pull', $data);
    }

    public function pushModel($name, $insecure = false)
    {
        $data = [
            'name' => $name,
            'insecure' => $insecure
        ];
        return $this->sendRequest('/api/push', $data);
    }

    public function generateEmbeddings($model, $prompt, $options = null)
    {
        $data = [
            'model' => $model,
            'prompt' => $prompt
        ];
        if (!is_null($options)) {
            $data['options'] = $options;
        }
        return $this->sendRequest('/api/embeddings', $data);
    }

    public function listRunningModels()
    {
        return $this->sendRequest('/api/ps', [], 'GET');
    }

    private function extractText($rawResponse): string
    {
        /*
        * {"model":"llama3.1","created_at":"2024-09-17T22:09:44.831536906Z","response":" la","done":false}
        * {"model":"llama3.1","created_at":"2024-09-17T22:09:45.045239846Z","response":" luz","done":false}
        * {"model":"llama3.1","created_at":"2024-09-17T22:09:45.253182319Z","response":".","done":false}
        * {"model":"llama3.1","created_at":"2024-09-17T22:09:45.496478101Z","response":"","done":true,"done_reason":"stop","context":[128006,882,128007,271,8193,15677,1208,115256,7583,409,1208,59425,5969,128009,128006,78191,128007,271,8921,115256,7583,409,1208,59425,5969,22800,2047,20006,4247,17971,55152,665,220,7028,20,11,379,924,82040,5840,71301,513,3543,1832,390,658,80689,330,50,38708,1208,4135,24409,258,36334,3074,409,2537,57395,981,665,89126,1,586,2172,665,220,7028,20,13,91230,513,3118,13055,8924,1989,67111,81467,3916,25,330,31282,24434,38468,85409,63613,4669,19571,390,5203,99194,17352,1744,1208,409,1208,65566,7673,379,330,6777,269,7583,409,1208,59425,5969,33397,11690,8921,115256,7583,409,1208,59425,5969,1560,653,3678,1030,1028,1832,56347,1744,671,58180,8924,13189,437,61942,1624,66584,2442,72,22893,1473,16,13,220,3146,6882,20053,5969,33397,68063,56808,97591,409,1208,115256,7583,22800,3118,2649,4247,55152,665,924,80689,409,220,7028,20,11,379,513,39239,12273,95437,665,658,89126,14113,68,379,665,1208,99194,738,5048,409,2537,57395,981,13,17652,25155,15491,115256,7583,11,15887,2537,9466,18745,304,8150,320,288,50018,11,79376,2353,1744,42939,2016,72,978,303,974,264,41609,13910,6926,288,7589,1645,8023,5840,8,59305,42550,9526,277,5252,67514,300,514,9891,65946,15540,627,17,13,220,3146,6882,20053,5969,4689,68063,56808,97591,22800,586,2649,665,220,7529,20,11,379,513,39239,12273,665,1208,1099,4234,329,8112,5203,2917,85,25282,1624,66584,2442,72,22893,25540,2649,4247,1208,71789,379,1208,4602,7583,13,2998,15491,115256,7583,11,1208,1099,4234,329,912,1560,5203,39751,4458,1744,1180,90739,9465,57395,981,665,89126,52904,11158,14707,5203,20503,65072,409,1208,76509,5808,28355,978,376,3074,1624,66584,2442,72,22893,627,18,13,220,3146,3617,5824,3614,61942,68063,5034,115256,7583,409,1208,59425,5969,6111,7453,8924,7546,3614,12762,3916,512,262,353,256,4072,93153,409,1208,44575,56944,409,514,9891,65946,15540,3429,15887,2537,9466,18745,304,24985,645,25,39776,93153,52764,346,1744,5252,514,9891,409,1208,65946,3074,4538,887,978,406,15540,3429,31201,5252,32525,665,89126,14113,68,627,262,353,256,4072,93153,409,1208,99194,29830,57592,25,17652,25155,15491,115256,7583,11,912,29253,5203,99194,16757,264,1208,409,1208,65566,13],"total_duration":88492478408,"load_duration":17509515989,"prompt_eval_count":19,"prompt_eval_duration":952188000,"eval_count":406,"eval_duration":69986951000}
         *
         */

        //split in lines with each json
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

// Generar una respuesta de chat
//echo "\nGenerando respuesta de chat...\n";
//$messages = [
//    ['role' => 'user', 'content' => '¿Cuál es la capital de Francia?']
//];
//$chatResponse = $api->generateChatCompletion('llama3.1', $messages);
//print_r($chatResponse);
//
// Listar modelos disponibles
//echo "\nListando modelos disponibles...\n";
//$models = $api->listModels();
//print_r($models);

// Obtener información de un modelo
//echo "\nObteniendo información del modelo...\n";
//$modelInfo = $api->showModelInfo('llama3.1');
//print_r($modelInfo);

// Listar modelos en ejecución
//echo "\nListando modelos en ejecución...\n";
//$runningModels = $api->listRunningModels();
//print_r($runningModels);

// Nota: Los siguientes métodos no se ejecutan por defecto para evitar modificaciones no deseadas
// Descomentar y usar según sea necesario

// Crear un modelo
// $createModelResponse = $api->createModel('mi-modelo', 'FROM llama3.1\nSYSTEM You are a helpful assistant.');
// print_r($createModelResponse);

// Copiar un modelo
// $copyModelResponse = $api->copyModel('llama3.1', 'llama3.1-copia');
// print_r($copyModelResponse);

// Eliminar un modelo
// $deleteModelResponse = $api->deleteModel('llama3.1-copia');
// print_r($deleteModelResponse);

// Descargar un modelo
// $pullModelResponse = $api->pullModel('llama3.1');
// print_r($pullModelResponse);

// Subir un modelo
// $pushModelResponse = $api->pushModel('mi-modelo:latest');
// print_r($pushModelResponse);

// Generar embeddings
// $embeddingsResponse = $api->generateEmbeddings('llama3.1', 'Este es un texto de ejemplo');
// print_r($embeddingsResponse);
