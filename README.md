<p align="center">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://cachethq.io/assets/cachet-logo-dark.svg">
      <img alt="Cachet Logo" src="https://cachethq.io/assets/cachet-logo-light.svg">
    </picture>
</p>

<p align="center">
    <a href="https://github.com/cachethq/core/actions">
        <img src="https://github.com/cachethq/core/workflows/run-tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://github.com/sponsors/cachethq/?sponsor=1">
        <img src="https://img.shields.io/github/sponsors/cachethq" alt="GitHub Sponsors">
    </a>
    <a href="https://packagist.org/packages/cachethq/core">
        <img src="https://img.shields.io/packagist/dt/cachethq/core" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/cachethq/core">
        <img src="https://img.shields.io/packagist/v/cachethq/core" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/cachethq/core">
        <img src="https://img.shields.io/github/license/cachethq/core" alt="License">
    </a>
</p>

## Introduction

This package provides the core functionality of [Cachet](https://github.com/cachethq/cachet) and may be installed into existing Laravel applications. Cachet 3.x will ship as a standalone Laravel application that requires this package.

## Project Status

Cachet 3.x is currently in development and is not yet completely ready for production use. We are actively working on the project and will provide updates as we progress.

- [x] Incident Management
- [x] Incident Updates
- [x] Scheduled Maintenance
- [ ] Scheduled Maintenance Updates
  - WIP - https://github.com/cachethq/core/pull/109
- [x] Components
- [ ] Metrics
  - API and dashboard are working.
  - Frontend is in progress.
- [ ] Subscribers
- [x] API
  - Complete but may need some tweaks as we progress.
- [ ] Webhooks
- [x] Dashboard
- [x] Localization
- [ ] Status Page Customization

## Stack

Cachet is built on:

- [Laravel 11.x](https://laravel.com)
- [Filament 3.x](https://filamentphp.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)

## Development

1. Clone this repository.
2. Run the following commands from within the `core` directory:
    ```shell
   npm install
   composer update
   composer dev
    ```
3. Develop Cachet. Currently, HMR is not enabled so manual refreshes are needed.

### Dashboard Credentials

When running Cachet via the `composer dev` command, Workbench will seed a user that you can use to log in to the dashboard.
Login to the account at `/dashboard` and use credentials:

- **Email:** `test@test.com`
- **Password:** `test123`

## Sponsors

<p align="center">
    <a href="https://jump24.co.uk"><img width="100px" src="https://github.com/jumptwentyfour.png" alt="Jump24"></a>
    <a href="https://dreamtilt.com.au"><img width="100px" src="https://github.com/dreamtilt.png" alt="Dreamtilt"></a>
    <a href="https://xyhen-it.nl"><img width="100px" src="https://github.com/xyphen-it.png" alt="Xyphen-IT"></a>
</p>


## Security Vulnerabilities

Please review our [security policy](https://github.com/cachethq/cachet/security/policy) on how to report security vulnerabilities.

## License

Cachet Core is open-sourced software licensed under the [MIT license](LICENSE.md).
