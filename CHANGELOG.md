# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
- Laravel 9, 10, 11, 12 and 13 support added (`illuminate/*` up to `^13.0`, `symfony/console` up to `^8.0`).
- PHPUnit 10, 11 and 12 support for tests; test methods now use `#[Test]` attributes alongside the `@test` annotations.
- Fixed fatal error on Laravel 9+ caused by `HasCommandValidator::execute()` missing the `int` return type required by `Illuminate\Console\Command::execute()`.
- Code style formatted to PSR-12 with Laravel Pint (`composer format` / `composer lint`).
- Replaced Travis CI with GitHub Actions (PHP 8.2–8.4 × Laravel 11/12/13, plus a code style check).
- Added `validatables()`, the correctly spelled name of the overridable `validatebales()` method that supplies the data to validate. `validatebales()` still works but is deprecated; when a command overrides both, `validatables()` wins.
- When `allowValidationFailureOnConsole()` returns `false`, validation failures now throw `Touhidurabir\CommandValidator\Exceptions\CommandValidationException`. It extends the previously thrown `Symfony\Component\Console\Exception\InvalidArgumentException` and keeps the same message, so existing `catch` blocks still work, and it exposes the errors via `getMessageBag()` and the validator via `getValidator()`.


## [1.0.0] - 2021-09-17
- Initial release

## [1.0.1] - 2021-09-17
- Test Update
- Bug Fix
