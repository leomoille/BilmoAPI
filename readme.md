# BileMo

[![Maintainability](https://api.codeclimate.com/v1/badges/e646c7d531f9c1abb08a/maintainability)](https://codeclimate.com/github/leomoille/bilemo/maintainability)

API allowing BileMo clients to manage their customers and products.

## Local Project Installation

Add `.env.local` file inside root project folder with your database connexion parameters:

```dotenv
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=mariadb-10.4.28&charset=utf8mb4"
```

Install dependencies:

```shell
composer install
```

Create the database:

```shell
symfony console doctrine:database:create
```

Run migrations:

```shell
symfony console doctrine:migrations:migrate
```

Load fixtures to populate the database with test data:

```shell
symfony console doctrine:fixtures:load
```

Generate the private and public key pair for the authentication system:

```shell
symfony console lexik:jwt:generate-keypair
```

Start the server:

```shell
symfony server:start
```

Once the server is running, you can access the API documentation via the URL:

**127.0.0.1:8000/api/doc**

*The port may vary depending on availability on your machine.*

## Obtaining a Token

You can obtain an authentication token using the following credentials:

Email: **client@smart.phone**  
Password: **client**

Once you have obtained your token, you can use it as the request header in the following format:

`Authorization: Bearer your_token`

Or directly in the "Authorize" section of the documentation in this format:

`Bearer your_token`
