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

- `src/bootstrap.php` - Entry point del servidor HTTP
- `src/mcp-server.php` - Servidor MCP stdio (para Claude Code local)
- `src/ddd/Infrastructure/HttpServer/Kernel/Kernel.php` - Núcleo async
- `src/ddd/Infrastructure/HttpServer/Router/routes.json` - Rutas
- `src/ddd/Infrastructure/MCP/BlogToolHandlers.php` - Tools MCP compartidas
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

## API REST - CRUD Completo

```bash
GET    /post          # Listar todos los posts
GET    /post/{id}     # Obtener un post por UUID
POST   /post          # Crear post (JSON: id, headline, articleBody, author, datePublished)
POST   /post/org      # Crear post desde org-mode (body = contenido org)
PUT    /post/{id}     # Actualizar post (JSON: headline, articleBody, author, datePublished, orgSource?)
DELETE /post/{id}     # Eliminar post
```

## Servidor MCP (Model Context Protocol)

Cohete expone 6 tools MCP para que cualquier IA pueda gestionar el blog:

| Tool | Descripcion |
|------|------------|
| `list_posts` | Lista todos los posts (id, titulo, autor, fecha) |
| `get_post` | Post completo con HTML y orgSource |
| `create_post` | Crea post desde campos JSON |
| `publish_org` | Publica post desde org-mode (pandoc) |
| `update_post` | Actualiza un post existente |
| `delete_post` | Elimina un post |

### Transporte stdio (desarrollo local)

```bash
# Arranca como subprocess de Claude Code
nix develop --command php src/mcp-server.php
```

Configurado en `.claude/mcp.json` - las tools aparecen automaticamente en Claude Code.

### Transporte HTTP/SSE (integrado en Cohete, mismo puerto)

Integrado en el propio servidor Cohete. Cualquier IA remota conecta a:
- `GET  https://pascualmg.dev/mcp/sse` - Abre SSE stream (recibe endpoint URL)
- `POST https://pascualmg.dev/mcp/message?clientId=xxx` - Envia JSON-RPC

No necesita proceso aparte ni puerto extra. Mismo servidor, mismo event loop.

### Arquitectura MCP

- `BlogToolHandlers.php` - 6 tools compartidas (usa `await()` para bridge async->sync)
- `CoheteTransport.php` - Transport custom integrado en Cohete (no crea servidor propio)
- `McpServerFactory.php` - Construye Server + Protocol + Transport y los conecta
- `McpSseController.php` - GET /mcp/sse (abre SSE stream)
- `McpMessageController.php` - POST /mcp/message (recibe JSON-RPC)
- `mcp-server.php` - Stdio transport standalone (Claude Code local)
- SDK: `php-mcp/server` v2 (ReactPHP nativo)

## Filosofía

DDDD = Domain Driven Design for Developers
- Si necesitas docs para entender el código, el código está mal
- Menos es más
- Async por defecto, no como afterthought
