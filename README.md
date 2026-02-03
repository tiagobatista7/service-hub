
# Setup Docker Laravel 11 com PHP 8.4

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

Comando para sair do container docker
```sh
exit
```

Instale e rode o npm (*requisito obrigatório)
```sh
npm install && npm run dev
```

Link para acessar o projeto:
[http://localhost:8000/login](http://localhost:8000/login)


Email para acesso administrador
```sh
admin@admin.com
```

Senha inicial do admin
```sh
123456789
```

Agora para iniciar os testes entre no container:
```sh
docker-compose exec app bash
```

Com o npm rodando em outra aba do terminal, digite este comando para testar os asserts:
```sh
./vendor/bin/pest
```



