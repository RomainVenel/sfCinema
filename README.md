# sfCinema

Petit projet réalisé en Symfony5.4 avec récupération d'affiches de film via une api + interface d'inscription/connexion pour visualiser les films.

##  <a name='Cloneandinstall'></a>Clone and install

```bash
git clone https://github.com/RomainVenel/sfCinema.git
cd sfCinema
```

##  <a></a>Modifier le fichier .env
```bash
Modifier DATABASE_URL en fonction des paramètres de notre bdd
Rajouter sa clé api comme variable d'environnement dans la variable APP_APIKEY dans le fichier .env
```

##  <a></a>Lancer la migration de la bdd

```bash
php bin/console d:m:m
```

##  <a></a>Composer

```bash
composer install
```

Si l'application est installé sur le port 8000

- Application => http://localhost:8000
