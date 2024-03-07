# Bienvenue sur le projet backend d' O'Fourneau 
Ici vous trouverez le guide d'installation du projet back d'O'Fourneau ainsi que le récap des fonctionnalités mises en place dans l'application. Le projet a été réalisé techniquement parlant en deux semaines et demi. Nous étions quatre collaborateurs : deux développeurs backend et deux développeurs frontend. J'ai été lead back-end et me suis occupée majoritairement de la création intégrale de la BDD (avec relation associées et tables pivots), des entités, des API, des controllers, de l'authentification et de la sécurité, des services et de la gestion des conflits et bugs.

### Installation du projet

```
composer require symfony/runtime
```
Puis copier coller les données contenu dans le .envExemple et créer un fichier .env

Ajouter les informations liées à votre bdd dans la section DATABASE_URL : 
```
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```
### Pour activer les tokens et l'identification : 

 Installer LexikJWTAuthenticationBundle
```bash
composer require "lexik/jwt-authentication-bundle" 
```

Générez notre clé privé et publique:

```
php bin/console lexik:jwt:generate-keypair 
```

Maintenant dans le dossier /config, vous avez un nouveau dossier jwt/ qui vient d'être crée avec deux fichiers jwt créés. Ces 2 clés vont permettre (avec la PASSPHRASE) de "signer" les token d'authentification que j'enverrais aux utilisateurs qui ont accès au site.

## Fonctionnalités 

Création et gestion des tables et de la bdd => 

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

Création des formulaires pour vues Twig (côté backOffice) => ```src/Form```

Création des services =>  ```src/Service```

Création des écouteurs d'évenements avec Symfony =>  ```src/EventListener```

Création des fixtures =>  ```src/DataFixtures```

## En savoir plus sur mes projets et mon parcours ?

C'est par ici : [estellebelleil.github.io](estellebelleil.github.io " Portfolio - Estelle Belleil ")