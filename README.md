### Setup application
- Copy `.env.example` to `.env` and populate

### Build
- docker compose build
- docker build -t blog-cli -f docker/local/php-cli/Dockerfile .
- docker run --rm -v .:/home/application/blog -it blog-cli php ../composer.phar install
- docker compose up -d
- docker run --rm --network blog-network -v .:/home/application/blog -it blog-cli php artisan migrate

### Run
- docker compose up -d
