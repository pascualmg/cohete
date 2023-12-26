# Reactor  
Async RxPHP in a sleek API server

## Descripción

Este proyecto permite el manejo de casos de uso de forma asíncrona. Usando el potencial de un enfoque asíncrono en la programación PHP. Se implementa mediante un servidor personalizado creado con ReactPHP, una biblioteca libre y de código abierto utilizada para la programación impulsada por eventos. La clave de este proyecto es el concepto de promises, que abre la puerta a la utilización de observables y la poderosa biblioteca de programación reactiva RxPHP.

El enfoque asíncrono permite el manejo de casos de uso de manera no bloqueante, proporcionando una mejora significativa de rendimiento en comparación con el enfoque PHP sincrónico tradicional. Este modelo de ejecución permite que múltiples tareas se procesen en paralelo, mejorando la eficiencia y el rendimiento, y permitiendo la creación de código altamente interactivo y fácil de entender.

El proyecto se basa en el servidor HTTP de ReactPHP, probado en entornos de producción y reconocido por su solidez. Compatible con cualquier middleware PSR-15, hace fácil crear y adaptar propios middlewares para personalizar la lógica del servidor.

En cuanto a la estructura del proyecto, está diseñada para seguir la arquitectura de Domain-Driven Design (DDD). Sin embargo, es importante mencionar que la estructura actual sirve principalmente como un esqueleto de ejemplo. El núcleo del proyecto reside en dos o tres archivos fácilmente localizables, lo que facilita su comprensión y adaptabilidad.
## Beneficios

- Mejora la eficiencia y el rendimiento al no bloquear el hilo principal con operaciones de E/S intensivas.
- Ofrece un control granular sobre cómo y cuándo se ejecutan las tareas.
- Mejora las capacidades de PHP más allá de las aplicaciones web síncronas tradicionales.

Las posibilidades con PHP son mayores de lo que se suele pensar. Este proyecto es una muestra de ello. Te invito a explorarlo, a aprender conmigo y a hacer tus propias contribuciones. ¡Hagamos juntos de este proyecto algo increíble!

## Manejo asíncrono de las peticiones (the core)

Al crear el servidor HTTP con ReactPHP, se le pasa una función de manejo de peticiones. Aquí está la función anónima que
se pasa al servidor:

```injectablephp
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
# Instalacion

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

# Flexibilidad y Facilidad de Modificación

Las funcionalidades presentadas, como la arquitectura DDD, son únicamente una propuesta inicial. El framework está diseñado con una arquitectura flexible que facilita la modificación, adición o eliminación de funcionalidades según sean necesarias. De esta manera, el framework se puede ajustar para satisfacer las necesidades específicas de cada proyecto.