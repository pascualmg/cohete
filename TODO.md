# Cohete - TODO

## Chat en tiempo real (WebSocket / Ratchet)

**Prioridad**: Alta - potencial pelotazo
**Estado**: Pendiente (ya existio un prototipo funcional)

Pascual ya tenia un componente chat funcionando con Ratchet (WebSocket) integrado en el mismo proceso ReactPHP. Se ejecutaba en cualquier ventana o post y permitia hablar con otros usuarios conectados en tiempo real.

**Que paso**: Al desplegar detras de Cloudflare dejo de funcionar (Cloudflare y WebSockets requieren config especifica).

**Que hay que hacer**:
- Recuperar/reescribir el componente chat con Ratchet
- Investigar si con Cloudflare Tunnels los WebSockets van (antes con Cloudflare proxy no iban)
- El componente chat original usaba un Observable para pintar los mensajes -- momento de genialidad que merece un post didactico explicando la potencia de los Observables en PHP async
- Integrarlo como Web Component en el blog (ventana flotante en cada post)
- Mismo event loop, mismo proceso, mismo bootstrap.php

**Por que es importante**: Un blog donde humanos e IAs publican juntos Y ademas pueden hablar en tiempo real entre ellos. Eso no existe en ningun sitio.

**Post didactico**: Explicar como funciona el chat con Observables -- desde el WebSocket hasta el render. Ejemplo perfecto de por que ReactPHP + RxPHP no es una locura sino una ventaja real. El post que Pascual siempre quiso escribir.

## Boton compartir en redes sociales

**Prioridad**: Media
**Estado**: Pendiente

Boton de compartir directo a LinkedIn, Instagram, X, etc. desde cada post. Ahora solo hay "Copiar URL".

## Frontend con Web Components nativos

**Prioridad**: Media
**Estado**: Pendiente

Refactorizar todo el frontend (blog index, post detail, session banner) a Web Components nativos siguiendo el patron Atomic Design que ya tiene el portfolio. Draft actual funciona pero esta todo inline en los controllers PHP.

## Persistir avatar_style en servidor

**Prioridad**: Baja
**Estado**: Pendiente

Ahora el estilo de avatar (bottts, pixel-art, etc.) solo se guarda en localStorage. Deberia guardarse en la tabla author para que sea consistente entre dispositivos.
