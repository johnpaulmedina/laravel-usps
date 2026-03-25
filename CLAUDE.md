# CLAUDE.md — Laravel USPS Package

## Overview
Laravel package wrapping the USPS API v3 (OAuth2). Covers all 20 USPS API domains.

## Architecture
- `src/Usps/USPSBase.php` — Abstract base: OAuth2 token caching, HTTP methods (GET/POST/PUT/PATCH/DELETE)
- `src/Usps/Usps.php` — Main class registered as `usps` singleton. Facade accessor methods return typed API clients.
- Each API domain is a separate class extending `USPSBase` with its own OAuth scope.
- `config/usps.php` — Publishable config (`USPS_CLIENT_ID`, `USPS_CLIENT_SECRET`)

## Conventions
- PSR-4 autoload: `Johnpaulmedina\Usps\` → `src/Usps/`
- Strict PHP types on all methods
- Each API class sets `protected string $scope` for per-scope OAuth tokens
- Tests use Orchestra Testbench + `Http::fake()`
- No `Log::`, `dd()`, or debug code

## API Reference
See https://github.com/USPS/api-examples for official USPS API examples and OpenAPI specs.

## Testing
```bash
composer test
# or
./vendor/bin/phpunit
```
