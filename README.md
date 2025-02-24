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
</p>

## Introduction

This package provides the core functionality of [Cachet](https://github.com/cachethq/cachet) and may be installed into existing Laravel applications. Cachet 3.x will ship as a standalone Laravel application that requires this package.

## Project Status

Cachet 3.x is currently in development and is not yet completely ready for production use. We are actively working on the project and will provide updates as we progress.

- [x] Incident Management
- [x] Incident Updates
- [x] Scheduled Maintenance
- [x] Scheduled Maintenance Updates
- [x] Components
- [x] Metrics
- [ ] Subscribers
- [x] API
  - Complete but may need some tweaks as we progress.
- [x] Webhooks
- [x] Dashboard
- [x] Localization
- [x] Status Page Customization
- [ ] Meta
- [ ] Tags
- [x] User Management

## Stack

Cachet is built on:

- [Laravel 11.x](https://laravel.com)
- [Filament 3.x](https://filamentphp.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)

## Development

To get started developing Cachet, please check out the [Development Guide](https://docs.cachethq.io/v3.x/development).

### Dashboard Credentials

When running Cachet via the `composer dev` command, Workbench will seed a user that you can use to log in to the dashboard.
Login to the account at `/dashboard` and use credentials:

- **Email:** `test@test.com`
- **Password:** `test123`

## Sponsors

<p align="center">
    <a href="https://jump24.co.uk"><img width="100px" src="https://github.com/jumptwentyfour.png" alt="Jump24"></a>
    <a href="https://dreamtilt.com.au"><img width="100px" src="https://github.com/dreamtilt.png" alt="Dreamtilt"></a>
    <a href="https://xyphen-it.nl"><img width="100px" src="https://github.com/xyphen-it.png" alt="Xyphen-IT"></a>
    <a href="https://coderabbit.ai/"><img width="100px" src="https://github.com/coderabbitai.png" alt="Code Rabbit"></a>
    <a href="https://scramble.dedoc.co/"><img width="100px" src="https://github.com/dedoc.png" alt="de:doc"></a>
</p>

## Security Vulnerabilities

Please review our [security policy](https://github.com/cachethq/cachet/security/policy) on how to report security vulnerabilities.

## License

Cachet Core is open-sourced software licensed under the [MIT license](LICENSE.md).

## Trademarks

Copyright (c) 2023-present Alt Three Services Limited. The Cachet name and logo are trademarks of Alt Three Services Limited. Please see our [trademark guidelines](https://github.com/cachethq/core/blob/main/TRADEMARKS.md) for info on acceptable usage.

## Community

Have questions, comments or feedback? [Start a discussion](https://github.com/cachethq/cachet/discussions/new). For the latest news and release notes, visit [cachethq.io](https://cachethq.io).

## Contributors

<a href="https://github.com/cachethq/core/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=cachethq/core&max=400&columns=20" width="100%"/>
</a>
