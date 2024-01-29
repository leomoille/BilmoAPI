# BileMo

[![Maintainability](https://api.codeclimate.com/v1/badges/e646c7d531f9c1abb08a/maintainability)](https://codeclimate.com/github/leomoille/bilemo/maintainability)

API allowing BileMo clients to manage their customers and products.

Les commits suivants sont des améliorations et mises à jour hors du cadre de mon parcours.

## Prérequis

Pour pouvoir mettre en place BilMo API, vous aurez besoin des outils suivants :

- PHP 8.2
- Composer
- NodeJS (et npm)
- Symfony CLI
- Docker

## 1 - Installer les dépendances PHP

Depuis un terminal dans le dossier du projet, lancez la commande suivante :

```shell
composer install
```

## 2 - Démarrer le container Docker

Démarrez le container contenant la base de données, le mail catcher ainsi qu'un phpMyAdmin

```shell
docker compose up -d
```

## 3 - Exécutez les migrations

Depuis un terminal dans le dossier du projet, lancez la commande suivante :

```shell
symfony console d:m:m -n
```

## 4 - Charger les fixtures

Depuis un terminal dans le dossier du projet, lancez la commande suivante :

```shell
symfony console d:f:l -n
```

## 5 - Démarrer le serveur local

Depuis un terminal dans le dossier du projet, lancez la commande suivante :

```shell
symfony serve -d
```

## 6 - Découvrir BilMo API !

Une fois le serveur démarré, vous pouvez vous rendre sur [127.0.0.1:8000/api/docs](http://127.0.0.1:8000/api/docs) pour
naviguer sur le site.

> Par défaut, le serveur écoute sur le port `8000` mais si ce dernier est indisponible le port sera différent. Consultez
> l'output du terminal pour connaitre le port utilisé.

## 7 - Obtenir un token

Vous pouvez obtenir un token en utilisant un de ces comptes de démo :

| Email               | Password |
|---------------------|----------|
| client1@smart.phone | password |
| client2@smart.phone | password |
| client3@smart.phone | password |

Une fois votre token obtenu, utilisez le en le passant dans le header de votre requête :

`Authorization: Bearer your_token`

Ou directement depuis la partie Authorize de la documentation de l'API :

`Bearer your_token`
