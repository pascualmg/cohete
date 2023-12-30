# Reactor  
Rx in a sleek API server
![rxphp](logo.png)
## Descripción
Reactor, es un proyecto que he diseñado para facilitar la programación asíncrona en PHP.

Este proyecto se construye sobre las sólidas bases de ReactPHP y RxPHP, ofreciéndote un camino hacia la programación reactiva en PHP. Reactor funciona como más que una simple herramienta; es un marco de trabajo estructurado en torno al Domain-Driven Design (DDD), con su núcleo contenido en unos pocos archivos sencillos de encontrar y entender.
Con esas dos librerías de base se consigue un nuevo nivel de eficiencia y rendimiento.
Podrás manejar casos de uso de manera no bloqueante y realizar múltiples tareas simultáneamente.

La instalación de Reactor es todo menos complicada, se utiliza como plantilla que ya funciona y se pueden seguir los ejemplos o hacer algo diferente.

Por supuesto, más que un framework, es un espacio para el aprendizaje y la exploración. Es una invitación a los autodidactas, a quienes aman descubrir, aprender y compartir sus ideas. Te invito a explorar Reactor, a sumergirte en su código, a desafiar tus propios límites y a compartir tus descubrimientos y experiencias.
Si tienes interés por la programación reactiva y PHP, te invito a que lo pruebes


# Instalación

```bash
make nix-install 

nix develop 
make run
```

## Tip: Configuración Adicional de `direnv`
Si deseas simplificar tu flujo de trabajo evitando la necesidad de ejecutar `nix develop` cada vez que ingresas al directorio del proyecto, puedes optar por la configuración adicional con `direnv`:

1. Asegúrate de tener `direnv` instalado. Puedes confirmar esto ejecutando `direnv` en tu consola. Si el comando no se encuentra, necesitas instalar `direnv`.

2. Configura tu shell para utilizar `direnv`. Si estás usando `bash`, puedes agregar la siguiente línea a tu archivo `.bashrc`. Si estás usando `zsh`, agrega la misma línea a tu archivo `.zshrc`.

    ```bash
    eval "$(direnv hook bash)"
    # o
    eval "$(direnv hook zsh)"
    ```

3. Reinicia tu consola para que los cambios en tu archivo de configuración de shell tengan efecto.

4. Verifica que tu archivo `.envrc` está en el directorio correcto y tiene los valores correctos.

5. Cuando entras a tu directorio (en este caso, el directorio `rxphp`), deberías ver un mensaje de `direnv` solicitándote permitir el uso del archivo `.envrc`. Usa el comando `direnv allow` para esto.

Si después de seguir estos pasos, `direnv` aún no funciona como se esperaba, verifica los detalles de tu instalación y configuración.


## Características

- Mejora la eficiencia y el rendimiento evitando bloquear el hilo principal con operaciones intensivas de E/S.
- Ofrece un control granular sobre la ejecución de las tareas.
- Extiende las capacidades de PHP más allá de las aplicaciones web síncronas tradicionales.

Las posibilidades con PHP son extensas. Reactor es un ejemplo de esto. Este proyecto es un terreno abierto para el intercambio de ideas y el aprendizaje mutuo. Cualquier contribución para su mejora es bienvenida y se considera valiosa.

## Manejo asíncrono de las peticiones (the core)

Al crear el servidor HTTP con ReactPHP, se le pasa una función de manejo de peticiones. Aquí está la función anónima que
se pasa al servidor:

```php
        $httpServer = new HttpServer(
            $clientIPMiddleware,
            function (ServerRequestInterface $request) use ($container, $dispatcher) : PromiseInterface | ResponseInterface {
                try {
                    return self::AsyncHandleRequest(
                        $request,
                        $container,
                        $dispatcher
                    )
                        ->then(function (ResponseInterface $response) {
                            return $response;
                        })
                        ->catch(function (Throwable $exception) {
                            return new Response(
                                409,
                                ['Content-Type' => 'application/json'],
                                self::toJson($exception)
                            );
                        });
                } catch (Throwable $exception) {
                    // Capture only router configuration errors &
                    // other exceptions not related to request handling
                    return new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        self::toJson($exception)
                    );
                }
            }
        );
```

En la función de manejo de peticiones que se pasa al servidor HTTP de ReactPHP, la petición se procesa de manera
asíncrona mediante la invocación de la función `AsyncHandleRequest`. Esta función procesa la petición y devuelve una
promesa. Esta promesa se resolverá con un objeto implementando `ResponseInterface` y esa respuesta será la que ReactPHP
enviará al cliente HTTP.

Esto es posible gracias a la naturaleza asíncrona y no bloqueante de ReactPHP, que permite realizar operaciones de E/S (
como leer de una base de datos o hacer una solicitud HTTP a otra API) dentro de la función de manejo sin bloquear el
hilo de ejecución principal de la aplicación. Estas operaciones de E/S son asíncronas y devuelven una promesa.

Por lo tanto, al devolverse una promesa en las funciones de manejo, ReactPHP espera a que esta promesa se resuelva antes
de enviar la respuesta al cliente HTTP. Esto permite realizar operaciones de E/S asíncronas y simplemente devolver una
promesa que se resolverá con la respuesta cuando todas las operaciones de E/S hayan finalizado.

En situaciones de error no relacionados con el manejo de la petición (como errores de configuración del enrutador), la
función de manejo puede devolver directamente una respuesta con un código de estado HTTP 500 o similar, lo cual
proporciona flexibilidad para manejar diversas situaciones de error a nivel del servidor.

Esta capacidad de manejar las peticiones de forma asíncrona es especialmente valiosa en situaciones donde hay
operaciones de E/S con un potencial de latencia alta. Por ejemplo, un servidor que tenga que buscar datos en una base de
datos remota para cada petición se beneficiaría enormemente de esta característica. En un modelo de ejecución
sincrónico, el servidor debe esperar a que se complete la operación de la base de datos antes de poder procesar la
siguiente petición. Sin embargo, con ReactPHP y su modelo asíncrono, el servidor puede procesar varias peticiones a la
vez, mientras espera la respuesta de la base de datos. Mientras una petición está en espera, otras peticiones pueden ser
procesadas y enviadas, utilizando de manera eficiente el tiempo de CPU y mejorando significativamente el rendimiento y
la capacidad de respuesta del servidor.

Además, debido a que ReactPHP es no bloqueante, incluso con un número grande de peticiones simultáneas, otras partes de
tu aplicación o de tu sistema no se verán afectadas y podrán seguir funcionando con normalidad. Esto hace a ReactPHP una
excelente opción para aplicaciones que necesiten mantener un alto nivel de rendimiento y eficiencia, incluso bajo una
carga pesada de peticiones.

En resumen, la capacidad de ReactPHP para manejar peticiones de manera asíncrona significa que puede proporcionar un
servicio rápido y eficiente, incluso en situaciones donde un servidor síncrono se bloquearía o se ralentizaría.

# PHP Asíncrono: Una Nueva Perspectiva

PHP, conocido por su uso tradicional en desarrollo web síncrono, puede ser también muy potente en contextos asíncronos, permitiéndonos optimizar la eficiencia de nuestras aplicaciones. Cambiar nuestro "chip" para adoptar este enfoque puede parecer complejo, pero en realidad nos abre una puerta a nuevas y apasionantes posibilidades.

## Cambiando el paradigma

La programación asíncrona significa que podemos empezar tareas sin tener que esperar a que otras terminen, permitiendo a nuestro código avanzar sin quedar bloqueado. Este enfoque es particularmente útil para tareas que dependen de la entrada/salida (I/O), como las operaciones de red, lectura/escritura de archivos, interacciones con bases de datos, entre otras.

Nuestro enfoque para explicar este cambio de paradigma será a través de dos casos de uso: `FindAllPostController` y `FindPostById`. Aunque los detalles específicos y el código serán añadidos posteriormente, lo importante a recordar es cómo aprovechamos esta asincronía en nuestra lógica de negocio.

## Declarativo sobre Imperativo

La clave del enfoque asíncrono es adoptar un estilo de programación más declarativo que imperativo. En lugar de decir cómo hacer algo con instrucciones detalladas, describimos qué queremos lograr y dejamos que el sistema decida cómo implementarlo. Esto es particularmente evidente en DDD (Domain-Driven Design), donde nuestro código refleja el dominio del negocio de forma más clara y abstracta.

Nuestra estrategia será recibir una petición, iniciar la lógica de negocio correspondiente, y devolver una respuesta sin tener que esperar a que esta lógica termine completamente. Esto permite mantener la agilidad de nuestra aplicación, mejorando la experiencia de usuario al minimizar los tiempos de espera.

Este cambio de "chip" puede ser desafiante, pero las recompensas en términos de eficiencia y rendimiento son enormes. Te invitamos a descubrir más a medida que profundizamos en estos casos de uso con PHP asíncrono.


#  Algunas 🔋 incluidas

Este framework  ofrece una serie de características robustas para los proyectos que buscan optimizar la eficiencia y rendimiento en tareas asíncronas con PHP.

## Contenedor de Dependencias con Autowiring

Implementado con PHP-DI, el contenedor de dependencias, facilita la gestión de las dependencias de la aplicación y contribuye a un diseño de código limpio y de fácil mantenimiento.

## Enrutador

Con el uso de FastRoute como enrutador, el framework permite una definición y manejo claro de las rutas en el código, mejorando su legibilidad y acelerando el desarrollo.

## Bus Asíncrono

El bus asíncrono, basado en ReactPHP y Evenement, gestiona eficientemente la comunicación entre las diversas partes de la aplicación, mejorando su rendimiento.

Aunque estas características amplían la funcionalidad, permanecen completamente desacopladas del núcleo del framework, que sigue siendo una función simples que recibe una solicitud y devuelve una respuesta.

## Migraciones y fixtures

Para manejar las migraciones y fixtures de la base de datos en este proyecto, se seleccionó [Phinx](https://phinx.org) debido a su versatilidad.

Puedes ejecutar las migraciones utilizando el comando make:

```bash
make migrations
```

O puedes hacerlo directamente a través de Phinx con:

```bash
./bin/vendor/phinx
```

Además, este proyecto utiliza [Faker](https://github.com/fzaninotto/Faker) para generar fixtures. Faker es una biblioteca PHP que genera datos ficticios para rellenar nuestras bases de datos. Permite crear un conjunto de datos realistas, haciendo que nuestras pruebas sean más robustas.

Recuerda actualizar tus migraciones y fixtures según sea necesario para reflejar cualquier cambio en la estructura de tus datos.
# Flexibilidad y Facilidad de Modificación

Las funcionalidades presentadas, como la arquitectura DDD, son únicamente una propuesta inicial. El framework está diseñado con una arquitectura flexible que facilita la modificación, adición o eliminación de funcionalidades según sean necesarias. De esta manera, el framework se puede ajustar para satisfacer las necesidades específicas de cada proyecto.

# Ejemplos de Mysql no bloqueante . 

## Una consulta simple
**tradicional**
```injectablephp
public function findById(int $postId): ?Post 
{
    $mysqli = new mysqli("localhost", "usuario", "contraseña", "base_de_datos");

    $stmt = $mysqli->prepare("SELECT * FROM post WHERE post.id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rawPostData = $result->fetch_assoc();

    return $rawPostData === null ? null : new Post(
        $rawPostData['id'],
        $rawPostData['title'] . $rawPostData['content'],
        new \DateTimeImmutable($rawPostData['created_at'])
    );
}
```
**asíncrono con Promises**
```injectablephp
    public function findById(int $postId): PromiseInterface //of Post or Null
    {
        $deferred = new Deferred();

        $this->mysqlClient->query(
            "SELECT * FROM post where post.id = ?",
            [$postId]
        )->then(function (MysqlResult $mysqlResult) use ($deferred) {
            $rawPostData = $mysqlResult->resultRows[0] ?? null;

            $deferred->resolve(
                $rawPostData === null ? null : new Post(
                    $rawPostData['id'],
                    $rawPostData['title'] . $rawPostData['content'],
                    new \DateTimeImmutable($rawPostData['created_at'])
                )
            );
        });

        return $deferred->promise();
    }
```

## Ejemplo de transacción

**tradicional**

```injectablephp
$mysqli = new mysqli("localhost", "usuario", "contraseña", "base_de_datos");

$amount = 100; // Transferir $100 de la cuenta 1 a la cuenta 2
try {
    $mysqli->autocommit(FALSE);

    $stmt = $mysqli->prepare('UPDATE account SET balance = balance - ? WHERE id = 1');
    $stmt->bind_param("i", $amount);
    $stmt->execute();
    
    $stmt = $mysqli->prepare('UPDATE account SET balance = balance + ? WHERE id = 2');
    $stmt->bind_param("i", $amount);
    $stmt->execute();

    $mysqli->commit(); // Si todo fue exitoso, confirma la transacción
} catch (\Exception $e) {
    $mysqli->rollback(); // Si algo falló, revierte la transacción
    throw $e; // Lanza la excepción para manejarla en el código externo
};
```

**asíncrono con promises**

```injectablephp
use React\MySQL\ConnectionInterface;

$connection = new ConnectionInterface;  // Asegúrate de tener una instancia de ConnectionInterface y reemplaza esto según tu configuración de conexión

$connection->query('BEGIN')
    ->then(function() use ($connection) {
        $amount = 100;  // Suponemos que estamos transfiriendo $100 de la cuenta 1 a la cuenta 2

        return $connection->query('UPDATE account SET balance = balance - ? WHERE id = 1', [$amount])
            ->then(function() use ($connection, $amount) {
                return $connection->query('UPDATE account SET balance = balance + ? WHERE id = 2', [$amount]);
            });
    })
    ->then(function () use ($connection) {
        return $connection->query('COMMIT');
    })
    ->catch(function (\Exception $e) use ($connection) {
        $connection->query('ROLLBACK');
        throw $e;
    });
```

**con rxPHP!? :)**
```injectablephp
use React\MySQL\ConnectionInterface;
use Rx\Observable;

$connection = new ConnectionInterface; // Asegúrate de tener una instancia de ConnectionInterface y reemplaza esto según tu configuración de conexión

// Iniciar la transacción
$beginTransaction = Observable::fromPromise($connection->query('BEGIN'));

// Enviar la consulta de debito
$debitAccount = Observable::fromPromise(
    $connection->query('UPDATE account SET balance = balance - ? WHERE id = 1', [$amount = 100]) // Transferir $100 de la cuenta 1 a la cuenta 2
);

// Enviar la consulta de credito
$creditAccount = Observable::fromPromise(
    $connection->query('UPDATE account SET balance = balance + ? WHERE id = 2', [$amount])
);

// Enviar el COMMIT si todo fue exitoso
$commitTransaction = Observable::fromPromise($connection->query('COMMIT'));

// Secuenciando las operaciones anteriores
$transaction = $beginTransaction
    ->concat($debitAccount)
    ->concat($creditAccount)
    ->concat($commitTransaction)
    ->share();

// Lidiando con los éxitos
$transaction
    ->subscribe(
        function() { echo "Operación exitosa \n"; },
        // En caso de error, hacer un rollback
        function(\Exception $e) use ($connection) {
            echo "Hubo un error, haciendo rollback \n";
            $connection->query('ROLLBACK');
            throw $e;
        },
        function() { echo "La transacción ha sido completada \n"; }
    );
```

