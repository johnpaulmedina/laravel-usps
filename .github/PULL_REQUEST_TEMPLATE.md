## Summary

<!-- Brief description of the change -->

## Type

- [ ] Bug fix
- [ ] New feature / API endpoint
- [ ] USPS API sync (upstream changes)
- [ ] Dependency update
- [ ] Documentation
- [ ] Other

## Checklist

- [ ] Tests pass (`vendor/bin/phpunit`)
- [ ] New API methods have tests with `Http::fake()`
- [ ] Form Requests have validation rules matching USPS spec
- [ ] README.md updated (if new endpoints/commands)
- [ ] `docs/index.html` updated (if new endpoints)
- [ ] No debug code (`dd()`, `Log::`, `var_dump`)
- [ ] Strict types on all methods
