# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Cohete is an asynchronous PHP framework built on ReactPHP and RxPHP, implementing Domain-Driven Design (DDD) patterns. The project follows a clean architecture with clear separation between Application, Domain, and Infrastructure layers.

## Architecture

The codebase follows DDD structure:

- **Application Layer** (`src/ddd/Application/`): Contains command handlers, queries, and application services
- **Domain Layer** (`src/ddd/Domain/`): Contains entities, value objects, domain services, and repository interfaces
- **Infrastructure Layer** (`src/ddd/Infrastructure/`): Contains concrete implementations of repositories, HTTP server, message buses, and external integrations

Key architectural components:
- **Message Bus**: Handles commands and queries using either BunnieMessageBus or ReactMessageBus
- **Async Repositories**: Non-blocking database operations with MySQL and file-based implementations
- **HTTP Server**: ReactPHP-based async HTTP server with custom routing
- **Value Objects**: Strongly typed domain primitives

## Development Commands

### Core Development
```bash
# Run the application
make run
# Alternative: make start (same as make run)

# Watch for file changes during development
make watch

# Run in production mode with Nix
make run-prod
```

### Testing
```bash
# Run PHPUnit tests
./vendor/bin/phpunit

# Run Behat acceptance tests
make behat
# or
./vendor/bin/behat
```

### Database
```bash
# Run database migrations
make migrate

# Seed database with fixtures
make fixtures

# Start MySQL Docker container
make mysql
```

### Code Quality
```bash
# Fix code style issues
make fix

# Run PHPStan static analysis (level: max)
./vendor/bin/phpstan analyse

# Run Psalm static analysis (level: 7)
./vendor/bin/psalm
```

### Performance Testing
```bash
# Run load tests with Apache Bench
make test_ab
```

### Infrastructure
```bash
# Start RabbitMQ Docker container
make rabbitmq

# Install Nix (if not already installed)
make install-nix
```

## Key Files

- `src/bootstrap.php`: Application entry point with ReactPHP event loop setup
- `src/ddd/Infrastructure/HttpServer/Kernel/Kernel.php`: Core HTTP request handling with async promise-based routing
- `src/ddd/Infrastructure/HttpServer/ReactHttpServer.php`: Async HTTP server implementation
- `src/ddd/Infrastructure/HttpServer/Router/routes.json`: Route definitions mapped to controllers
- `src/ddd/Infrastructure/PSR11/ContainerFactory.php`: Dependency injection container setup
- `composer.json`: Dependencies and autoloading configuration

## Environment Setup

The project uses Nix for reproducible development environments. After running `make install-nix`, use `nix develop` to enter the development shell with all dependencies.

## Testing Strategy

- Unit tests in `test/` directory mirror the `src/` structure
- Integration tests use Behat for behavior-driven development
- Test configuration excludes Infrastructure layer from coverage
- PHPUnit configured for strict testing with coverage metadata

## Static Analysis

- PHPStan runs at maximum level with bootstrap support
- Psalm configured at error level 7 with unused code detection
- Both tools focus on the `src/` directory and ignore vendor files

## Implementation Examples

When adding new features, follow these patterns:

### Command/Query Handlers
- Commands: `src/ddd/Application/Post/CreatePostCommand.php` and `CreatePostCommandHandler.php`
- Queries: `src/ddd/Application/Post/FindAllPostsQuery.php` and query handlers
- Controllers: `src/ddd/Infrastructure/HttpServer/RequestHandler/` for HTTP endpoints

### Async Repository Pattern
- Interface: `src/ddd/Domain/Entity/PostRepository.php`
- MySQL implementation: `src/ddd/Infrastructure/Repository/Post/AsyncMysqlPostRepository.php`
- Returns React\Promise\PromiseInterface for non-blocking operations

### Value Objects
- Extend base classes: `StringValueObject`, `UuidValueObject`, `AtomDateValueObject`
- Example: `src/ddd/Domain/Entity/Post/ValueObject/PostId.php`