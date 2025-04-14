<?php

require_once dirname(__DIR__, 6) . '/vendor/autoload.php';


use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Http\Browser;
use Rx\Observable;

use Rx\Scheduler;

use Rx\Scheduler\EventLoopScheduler;

$loop = Loop::get();

//activamos calendarizador de rx
$scheduler = new EventLoopScheduler($loop);
try {
    Scheduler::setDefaultFactory(static fn() => $scheduler);
} catch (Exception $e) {
}

$browser = new Browser(
    $loop
);

$logger = new \Monolog\Logger('image_migration');
$logger->pushHandler(
    new \Monolog\Handler\StreamHandler(
        'image_migration.log'
    )
);

function logToFile(array $data): void
{
    global $logger;
    $logger->info('Migración de imagen', $data);
}

/**
 * Genera un archivo CSV con los datos de la migración
 * guid | profile_image | uploadedFileUrl | error
 * @param array $data
 * @return void
 */
function generateCSV(array $data): void
{
    $filename = 'migracion.csv';
    $file = fopen($filename, 'ab');

    // Si el archivo no existe, escribimos la cabecera
    if (filesize($filename) === 0) {
        fputcsv($file, ['guid', 'profile_image', 'uploadedFileUrl', 'error']);
    }

    // Escribimos los datos
    fputcsv($file, $data);

    fclose($file);
}
/**
 * Cross Mark Modificar en evolok el campo profile_image con la url que apunte a la imagen subida por ftp, que seguirán el patron https://s1.sportstatics.com/secure/ruta-hasta-la-imagen.  (Opcionalmente, podríamos solicitar a infra una carpeta y user ftp distintos para que en lugar de /secure sea /tumbsnails o lo que queramos)
 */
function uploadFileToFtp(string $fileContent, string $fileName): string
{
    $ftpServer = 'ftp-sports.srv.vocento.in';
    $ftpUsername = 'fdepsecure';
    $ftpPassword = base64_decode('bGhyb2t4UDU=');
    $ftpImageDir = '';


    $localTempFile = $fileName; //que se llame igual

    $filepathOnFtpServer = $ftpImageDir . $fileName;

    // Guardar el contenido en un archivo temporal SIN MANIPULACIÓN
    file_put_contents($localTempFile, $fileContent);

    // Conectar al servidor FTP
    $ftpConnection = ftp_connect($ftpServer);
    if (!$ftpConnection) {
        unlink($localTempFile);
        throw new Exception('No se pudo conectar al servidor FTP');
    }

    // Iniciar sesión
    if (!ftp_login($ftpConnection, $ftpUsername, $ftpPassword)) {
        ftp_close($ftpConnection);
        unlink($localTempFile);
        throw new Exception('Error de autenticación FTP');
    }

    // Modo pasivo - CRÍTICO para algunos firewalls y routers
    ftp_pasv($ftpConnection, true);

    // SOLUCIÓN: Forzar tipo binario explícitamente con comando raw
    ftp_raw($ftpConnection, "TYPE I");

    // Subir el archivo de imagen en modo estrictamente binario
    if (!ftp_put(
        $ftpConnection,
        $filepathOnFtpServer,
        $localTempFile,
        FTP_BINARY
    )) {
        ftp_close($ftpConnection);
        unlink($localTempFile);
        throw new Exception('Error al subir el archivo al servidor FTP');
    }

    // Verificar el tamaño del archivo subido
    $localSize = filesize($localTempFile);
    $remoteSize = ftp_size($ftpConnection, $filepathOnFtpServer);

    if ($remoteSize != -1 && $localSize != $remoteSize) {
        ftp_close($ftpConnection);
        unlink($localTempFile);
        throw new Exception(
            'El tamaño del archivo subido no coincide con el original'
        );
    }

    // Cerrar la conexión FTP
    ftp_close($ftpConnection);

    // Eliminar el archivo temporal LOCAL
    // unlink($localTempFile);

    echo "Archivo subido al servidor FTP: $filepathOnFtpServer\n";

    // Retornar URL completa (sin barra adicional)
    return 'https://s1.sportstatics.com/secure/' . $filepathOnFtpServer;
}

function updateProfileImageInEvolok(array $payload): Observable
{
    //curl -X PUT "https://<tu_instancia_evolok>/ic/api/userProfile/{guid}" \
    //-H "Content-Type: application/json" \
    //-H "Accept: application/json" \
    //-H "Authorization: Evolok evolok.api.service=ejemplo_servicio evolok.api.sessionId=xxxx-xxxx-xxxx-xxxx" \
    //-d '{
    //  "attributes": [
    //    {
    //      "name": "profile_picture_url",
    //      "value": "https://ejemplo.com/nueva_imagen.jpg"
    //    }
    //  ]
    //}'

    $guid = $payload['guid'];
    $newProfileImage = $payload['uploadedFileUrl'];


    $url = 'https://vocx.evolok.net/ic/api/userProfile/' . $guid;
    $data = [
        'attributes' => [
            [
                'name' => 'profile_image',
                'value' => $newProfileImage
            ]
        ]
    ];
    $jsonData = json_encode($data, JSON_THROW_ON_ERROR);

    $browser = new Browser(
        Loop::get()
    );


    return Observable::fromPromise(
        $browser->request(
            'PUT',
            $url,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Evolok evolok.api.key=d25d886b-1148-4352-87c2-45da614851d3'
            ],
            $jsonData
        )
    );
}


$promiseOfEvolokRequest = $browser->request(
    'GET',
    'https://vocx.evolok.net/ic/api/userProfile?attribute.profile_image=https://cdns.gigya.com%25&realm=abc',
    [
        'Accept' => 'application/json',
        'Authorization' => 'Evolok evolok.api.key=d25d886b-1148-4352-87c2-45da614851d3'
    ]
);
$ofEvolok = Observable::fromPromise($promiseOfEvolokRequest);


$ofEvolok
    ->map(fn(ResponseInterface $response) => json_decode(
        $response->getBody()->getContents(),
        true,
        512,
        JSON_THROW_ON_ERROR
    )['userProfile'])
    ->map(
        static fn(array $userProfiles) => array_map(
            static function (array $userProfile) {
                $attributes = $userProfile['attributes'];
                $match = array_search(
                    'profile_image',
                    array_column($attributes, 'name'),
                    true
                );
                if ($match === false) {
                    return null;
                }
                return [
                    'guid' => $userProfile['guid'],
                    'profile_image' => $attributes[$match]['value']
                ];
            },
            $userProfiles
        )
    )
    ->flatMap(
        function (array $photoUrlsAndGuids) use ($browser) {
            return Observable::fromArray($photoUrlsAndGuids)
                // Agrupar en lotes de 2
                ->bufferWithCount(2)
                // Procesar cada lote secuencialmente
                ->concatMap(
                    function (array $urlBatch) use ($browser) {
                        return Observable::fromArray($urlBatch)
                            // Añadir retraso entre peticiones de un mismo lote
                            ->concatMap(
                                function (array $tupleGuidAndProfileImage) use (
                                    $browser
                                ) {
                                    return Observable::timer(
                                        0
                                    ) // Retraso de 1 segundo entre peticiones
                                    ->flatMap(
                                        function () use ($browser, $tupleGuidAndProfileImage) {
                                            return Observable::fromPromise(
                                                $browser->request(
                                                    'GET',
                                                    $tupleGuidAndProfileImage['profile_image'],
                                                    [
                                                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                                                        'Accept' => 'image/webp,image/*,*/*',
                                                        'Referer' => 'https://vocx.evolok.net/'
                                                    ]
                                                )
                                            )->map(
                                                function (
                                                    ResponseInterface $response
                                                ) use ($tupleGuidAndProfileImage
                                                ) {

                                                    //aqui ya tentemos el contenido de la imagen
                                                    $imgContent = $response->getBody()->getContents();
                                                    $imageInfo = getimagesizefromstring($imgContent);
                                                    $extensionFromMime = image_type_to_extension($imageInfo[2]);


                                                    $guid = $tupleGuidAndProfileImage['guid'];
                                                    $newFilename = sprintf(
                                                        "migrated_%s%s",
                                                        $guid,
                                                        $extensionFromMime
                                                    );


                                                    $uploadedFileUrl = uploadFileToFtp(
                                                        $imgContent,
                                                        $newFilename
                                                    );

                                                    // Liberar memoria explícitamente
                                                    gc_collect_cycles();

                                                    return [
                                                        'guid' => $tupleGuidAndProfileImage['guid'],
                                                        'profile_image' => $tupleGuidAndProfileImage['profile_image'],
                                                        'uploadedFileUrl' => $uploadedFileUrl,
                                                        'error' => null,
                                                    ];
                                                }
                                            )->catch(
                                                function (\Throwable $e) use (
                                                    $tupleGuidAndProfileImage
                                                ) {
                                                    // Capturar cualquier error (conexión, timeout, etc.)
                                                    $url = $tupleGuidAndProfileImage['profile_image'];
                                                    echo "Error procesando $url: " . $e->getMessage() . "\n";
                                                    //return Observable

                                                    return Observable::of([
                                                        'guid' => $tupleGuidAndProfileImage['guid'],
                                                        'profile_image' => $url,
                                                        'uploadedFileUrl' => null,
                                                        'error' => $e->getMessage(),
                                                    ]);
                                                }
                                            );
                                        }
                                    );
                                }
                            );
                    }
                );
        }
    )
    ->do('logToFile')
    ->do('generateCSV')
//    ->flatMap('updateProfileImageInEvolok')
//    ->map(
//        static function (ResponseInterface $response) {
//            return json_decode(
//                $response->getBody()->getContents(),
//                true,
//                512,
//                JSON_THROW_ON_ERROR
//            );
//        }
//    )->catch(
//        static function (\Throwable $e) {
//            echo "Error en la petición a Evolok: " . $e->getMessage() . "\n";
//            return Observable::empty();
//        }
//    )
//    ->do('logToFile')
    ->subscribe(
        function ($result) use ($logger){
            //actualizo el profile id en evolok
            var_export($result);
        },
        function (Throwable $error) {
            echo $error;
        },
        function () {
            echo "completed";
        }

    );

echo "hola";
