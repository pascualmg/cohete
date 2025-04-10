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
                return $attributes[$match]['value'];
            },
            $userProfiles
        )
    )
    ->flatMap(
        function (array $photoUrls) use ($browser) {
            return Observable::fromArray($photoUrls)
                // Agrupar en lotes de 2
                ->bufferWithCount(2)
                // Procesar cada lote secuencialmente
                ->concatMap(
                    function (array $urlBatch) use ($browser) {
                        return Observable::fromArray($urlBatch)
                            // Añadir retraso entre peticiones de un mismo lote
                            ->concatMap(
                                function (string $url) use ($browser) {
                                    return Observable::timer(
                                        0
                                    ) // Retraso de 1 segundo entre peticiones
                                    ->flatMap(function () use ($browser, $url) {
                                        return Observable::fromPromise(
                                            $browser->request('GET', $url, [
                                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                                                'Accept' => 'image/webp,image/*,*/*',
                                                'Referer' => 'https://vocx.evolok.net/'
                                            ])
                                        )->map(
                                            function (
                                                ResponseInterface $response
                                            ) use ($url) {

                                                // Escribir directamente al archivo para ahorrar memoria
                                                $filename = basename($url);
                                                $file = fopen($filename, 'wb');
                                                $body = $response->getBody();

                                                $bytesWritten = 0;
                                                while (!$body->eof()) {
                                                    $bytesWritten += fwrite(
                                                        $file,
                                                        $body->read(8192)
                                                    ); // Leer en bloques pequeños
                                                }

                                                fclose($file);
                                                echo "File $filename written with $bytesWritten bytes\n";

                                                // Liberar memoria explícitamente
                                                gc_collect_cycles();

                                                return [
                                                    'url' => $url,
                                                    'bytesWritten' => $bytesWritten
                                                ];
                                            }
                                        )->catch(
                                            function (\Throwable $e) use ($url
                                            ) {
                                                // Capturar cualquier error (conexión, timeout, etc.)
                                                echo "Error procesando $url: " . $e->getMessage(
                                                    ) . "\n";
                                                return Observable::of([
                                                                          'url' => $url,
                                                                          'error' => $e->getMessage(
                                                                          ),
                                                                          'bytesWritten' => 0
                                                                      ]);
                                            }
                                        );
                                    });
                                }
                            );
                    }
                );
        }
    )
    ->subscribe(
        function ($result) {
            // Ya no necesitamos hacer nada aquí porque los archivos se guardan en el map
            if (isset($result['bytesWritten'])) {
                // La escritura ya se hizo arriba
                echo "File {$result['url']} written with {$result['bytesWritten']} bytes\n";
            }
        },
        function (Throwable $error) {
            echo $error;
        },
        function () {
            echo "completed";
        }

    );

echo "hola";
