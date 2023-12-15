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


