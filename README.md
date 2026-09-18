# HMNR Developer Toolbox

## Features

Phase 1 provides a responsive developer workspace with a registry-backed dashboard, category filtering, global search, keyboard command palette, favorites, recent tools, theme switching, and responsive navigation. Phase 2 adds JSON Formatter, Validator, Minifier, Escape, Unescape, and Diff workspaces.

## Tech Stack

- Laravel 12 and PHP 8.3+
- Livewire 3
- Blade, Tailwind CSS 4, Vite
- Alpine.js for local UI state
- No database or authentication required for the MVP

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

The project includes `composer.phar` for environments without a global Composer binary. Use `php composer.phar install` when needed.

## Development

```bash
php artisan serve
npm run dev
```

Open `http://localhost:8000`.

## Testing

```bash
php artisan test
```

## Project Structure

- `app/Support/ToolRegistry.php` is the single source of tool metadata.
- `app/Livewire/Dashboard.php` owns dashboard filtering.
- `app/Livewire/Tools/Json/Workbench.php` owns JSON processing.
- `resources/views/layouts/app.blade.php` is the application shell.
- `resources/views/livewire/dashboard.blade.php` and `resources/views/livewire/tools/json/workbench.blade.php` are the main screens.

## Adding a New Tool

1. Add metadata to `ToolRegistry::all()` with a unique slug, category, description, tags, and status.
2. Create a focused Livewire component under `app/Livewire/Tools` when processing needs server-side PHP.
3. Add a Blade view under `resources/views/livewire/tools` using the shared layout and UI patterns.
4. Register a named route in `routes/web.php`. Keep sensitive or network-facing processing isolated in its own service.
5. Add focused feature or unit tests for valid input, invalid input, and security boundaries.

## Security Considerations

Tool inputs are treated as untrusted. The MVP does not execute submitted code, commands, or generated cURL requests, and it does not persist credentials, passwords, or request bodies. Browser localStorage is limited to theme, favorite slugs, and recent slugs. Server-side HTTP execution should only be added behind URL validation, private-network blocking, strict timeouts, and response-size limits.

## Roadmap

- Expand encoding, generators, date/time, API, and developer tools.
- Add dedicated services and tests as tool logic grows.
- Consider an authenticated personal workspace only when persistence becomes useful.<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
