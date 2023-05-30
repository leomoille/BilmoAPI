# BileMo

[![Maintainability](https://api.codeclimate.com/v1/badges/e646c7d531f9c1abb08a/maintainability)](https://codeclimate.com/github/leomoille/bilemo/maintainability)

API permettant aux clients de BileMo de gérer leurs clients et produits.

## Installation

Mettez à jour les paramètres de connexion à la base de données dans le fichier `.env` :

```dotenv
DATABASE_URL="mysql://root@127.0.0.1:3306/bilmo?serverVersion=mariadb-10.4.28&charset=utf8mb4"
```

Installation des dépendances :

```shell
composer install
```

Création de la base de données :

```shell
symfony console doctrine:database:create
```

Exécution des migrations :

```shell
symfony console doctrine:migrations:migrate
```

Exécution des fixtures pour charger le jeu de données de test :

```shell
symfony console doctrine:fixtures:load
```

Lancement du serveur :

```shell
symfony server:start
```

Une fois le serveur lancé, vous pouvez accéder à la documentation de l'API via l'URL :

**127.0.0.1:8000/api/doc**

*Le port peut varier en fonction des disponibilités sur votre machine.*

## Nettoyage du cache

Bilmo met automatiquement en cache les données pour des raisons de performance. Le cache est actualisé en fonction des
données ajoutées ou modifiées.

Vous pouvez cependant vider manuellement le cache :

```shell
symfony console cache:clear
```
