# Contributing

## Setup

```bash
git clone https://github.com/johnpaulmedina/laravel-usps.git
cd laravel-usps
composer install
```

## Testing

```bash
vendor/bin/phpunit
```

All tests use `Http::fake()` — no USPS credentials needed for development.

## Adding a New API Endpoint

1. Add method to the appropriate class in `src/Usps/` (or create a new class extending `USPSBase`)
2. Set the `protected string $scope` for OAuth
3. Add a facade accessor in `src/Usps/Usps.php`
4. Create a controller in `src/Usps/Http/Controllers/`
5. Create a Form Request in `src/Usps/Http/Requests/`
6. Add the route in `routes/usps.php`
7. Write tests using Orchestra Testbench + `Http::fake()`
8. Update `README.md` and `docs/index.html`

## Commit Convention

Use prefixes for automatic versioning:

- `feat:` — new feature (bumps minor)
- `fix:` — bug fix (bumps patch)
- `docs:` — documentation only
- `chore:` — maintenance (no release)
- `refactor:` — code change (bumps patch)

## Code Standards

- PSR-4 autoloading
- Strict PHP types on all methods
- No `dd()`, `Log::`, `var_dump`, or debug code
- Laravel conventions for controllers, requests, routes
- PHPDoc on public methods with `@param` and `@return` types

## Reference

- [USPS API Examples](https://github.com/USPS/api-examples)
- [USPS Developer Portal](https://developer.usps.com)
