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

This package provides the core functionality of Cachet and may be installed into existing Laravel applications. Cachet 3.x will ship as a standalone Laravel application that requires this package.

## Development

1. Clone this repository.
2. Run the following commands from within the `core` directory:
    ```shell
   composer update
   npm install
   npm run build
    ```
3. Start running Cachet via the Orchestra Workbench:
    ```shell
   composer start
    ```
4. From within the `core` directory, you can now run to actively compile the frontend assets:
    ```shell
    npm run dev
    ```
5. Make your changes to the frontend. Currently, HMR is not enabled so manual refreshes are needed.

## Security Vulnerabilities

Please review our [security policy](https://github.com/cachethq/cachet/security/policy) on how to report security vulnerabilities.

## License

Cachet Core is open-sourced software licensed under the [MIT license](LICENSE.md).
