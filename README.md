## Installation ⚒️

To run this application locally, follow these steps:

1. Clone this repository:

   ```bash
   git clone https://github.com/MugosaDanilo/sistem-za-mobilnost-fit.git
   ```
2. Change to the project directory
    ```bash
    cd sistem-za-mobilnost-fit
    ```
3. Install the project dependencies
    ```bash
    composer install
    npm install
    ```
4. Copy the .env.example file to .env and configure your environment variables, including your database settings and any other necessary configuration.
    ```bash
    copy .env.example .env
    ```
5. Generate an application key
    ```bash
    php artisan key:generate
    ```
6. Start the development server
    ```bash
    php artisan serve
    ```
7. Access the application in your browser at http://localhost:8000