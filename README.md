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


***Install***

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

