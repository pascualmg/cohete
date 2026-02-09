# Cohete - Instrucciones para Claude

## El Proyecto

Cohete es un framework PHP asíncrono con ReactPHP/RxPHP y arquitectura DDD.

## Agente Especializado

Usa el agente `cohete-expert` para:
- Desarrollar nuevas features
- Debugging de código async
- Arquitectura y patrones DDD
- Code review

## Comandos de Desarrollo

```bash
# Entrar al entorno
nix develop

# Arrancar servidor
make run

# Tests
nix develop --command bash -c 'vendor/bin/phpunit'
nix develop --command bash -c 'vendor/bin/behat'

# Migraciones
make migrate
make fixtures
```

## Estructura Clave

- `src/bootstrap.php` - Entry point
- `src/ddd/Infrastructure/HttpServer/Kernel/Kernel.php` - Núcleo async
- `src/ddd/Infrastructure/HttpServer/Router/routes.json` - Rutas
- `src/ddd/Infrastructure/webserver/html/` - Frontend (Web Components)

## Patrones Importantes

1. **Handlers invocables**: `($handler)($command)` no `$handler->handle()`
2. **Todo async**: Siempre devolver `PromiseInterface`
3. **Observable + Promise**: `Observable::fromPromise()->map()->toPromise()`
4. **Value Objects**: No primitivos en Domain

## Frontend

Portfolio SPA con Web Components nativos (Atomic Design):
- `html/js/atomic/atom/` - Componentes básicos
- `html/js/atomic/molecule/` - Composiciones
- `html/js/atomic/organism/` - Páginas

## Filosofía

DDDD = Domain Driven Design for Developers
- Si necesitas docs para entender el código, el código está mal
- Menos es más
- Async por defecto, no como afterthought
