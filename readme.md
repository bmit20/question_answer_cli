   copy file .env :
- `cp .env.example .env`
- `cp source\.env.example source\.env`

   run docker:
- `docker-compose up --build`

- `docker-compose run --rm composer update`

- create a database with command:
- `docker-compose run --rm artisan db:create`

- `docker-compose run --rm artisan migrate`

Start the interactive Question and Answer using this command :

- `docker-compose run --rm artisan qanda:interactive`

Reset the progress using this command :

- `docker-compose run --rm artisan qanda:reset`

Run the unit tests using this command :

- `docker-compose run --rm artisan test`


`
