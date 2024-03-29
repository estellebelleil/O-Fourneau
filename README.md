# Bienvenue sur le projet backend d' O'Fourneau 
Ici vous trouverez le guide d'installation du projet back d'O'Fourneau ainsi que le récapitulatif des fonctionnalités mises en place dans l'application. Le projet a été réalisé techniquement parlant en deux semaines et demi. Nous étions quatre collaborateurs : deux développeurs backend et deux développeurs frontend. Côté back-end, nous avons élaboré l'intégralité des fonctionnalités back d'O'Fourneau à savoir la création intégrale de la BDD (avec relation associées et tables pivots), des entités,  des controllers, des API, de l'authentification et de la sécurité, du backoffice et des formulaires associés, des services, des évènements (en Symfony) et de la gestion des conflits et bugs. 

### Installation du projet

Créer un fichier .env depuis le modèle .envExemple

Ajouter les informations liées à votre bdd dans la section DATABASE_URL : 
```
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

Puis lancer le composer : 
```
composer install
```

## Fonctionnalités 

Création et gestion des tables et de la bdd : 

=> ```src/Repository```

=> ```src/Entity```

=> ```migrations```

Création et gestion des API => ```src/Controller/Api```

Création et gestion du backOffice => ```src/Controller/BackOffice```

Création et gestion de l'authentification et des tokens:

=> ```src/Controller/SecurityController```

=> ```src/Security/LoginAuthenticator```

=> ```config/jwt```

=> ```config/packages/security.yaml```

=> ```config/routes.yaml```

Création des vues Twigs => ```templates```

Création des formulaires pour vues Twigs (côté BackOffice) => ```src/Form```

Création des services =>  ```src/Service```

Création des écouteurs d'évenements avec Symfony =>  ```src/EventListener```

Création des fixtures =>  ```src/DataFixtures```

## Envie d'en savoir plus sur mes projets et mon parcours ?

C'est par ici : [estellebelleil.github.io](https://estellebelleil.github.io " Portfolio - Estelle Belleil ")