
# Setup Docker Laravel 11 com PHP 8.3

### Passo a passo
Clone Repositório
```sh
git clone -b main https://github.com/tiagobatista7/service-hub.git teste
```
```sh
cd teste
```

Suba os containers do projeto
```sh
docker-compose up -d
```


Crie o Arquivo .env
```sh
cp .env.example .env
```

Acesse o container app
```sh
docker-compose exec app bash
```


Instale as dependências do projeto
```sh
composer install
```

Gere a key do projeto Laravel
```sh
php artisan key:generate
```

OPCIONAL: Gere o banco SQLite (caso não use o banco MySQL)
```sh
touch database/database.sqlite
```

Rodar as migrations
```sh
php artisan migrate
```

Popular tabelas com seeders
```sh
php artisan db:seed
```

Instale o Laravel Breeze & npm
```sh
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
php artisan migrate
```

Acesse o projeto
[http://localhost:8000](http://localhost:8000)


