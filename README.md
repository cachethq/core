# Cachet Core [![run-tests](https://github.com/cachethq/core/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cachethq/core/actions/workflows/run-tests.yml)

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
