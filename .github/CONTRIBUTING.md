# Contributing

Thank you for your interest in contributing to Cachet!

## Development

To make local development easier, we use the [Orchestra Workbench](https://packages.tools/workbench.html). This allows us to run Cachet as a package within a test Laravel application, without having to install it as a standalone application.

1. Clone this repository.
2. Run the following commands from within the `core` directory:
    ```shell
   composer update
   npm install
   npm run build
    ```
3. Start running Cachet via Workbench:
    ```shell
   composer start
    ```
4. Within the `core` directory, you may now run to actively compile the frontend assets:
    ```shell
    npm run dev
    ```
5. Make your changes to the frontend. Currently, HMR is not enabled so manual refreshes are required.
