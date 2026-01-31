
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

Instale o Plugin Vue do Vite
```sh
npm install --save-dev @vitejs/plugin-vue
```

Instale o Inertia + Vue3
```sh
npm install @inertiajs/inertia @inertiajs/inertia-vue3 vue@3
```

Instale o Bootstrap 5 via npm
```sh
npm install bootstrap@5
```

Rode o npm
```sh
npm run dev
```

Link para acessar o projeto:
[http://localhost:8000](http://localhost:8000)



Para iniciar os testes insale o Pest no Laravel
```sh
composer require pestphp/pest --dev
```

Para integração com Laravel faça o comando:
```sh
php artisan pest:install
```

Agora para testar os asserts com o Pest use o comando:
```sh
./vendor/bin/pest
```




