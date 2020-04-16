# API LAN de l'ADEPT

Cet api représente le backend complet du site web du LAN de l'ADEPT. Il rassemble donc le côté utilisateur, ainsi que le côté administrateur du site.

# Table des matières
  1. [Information générale](#information-générale)  
  2. [Développer avec Homestead (recommandé)](#développer-avec-homestead-recommandé)   
      1. [Outils requis avec Homestead](#outils-requis-avec-homestead)  
      2. [Installation de Homestead](#installation-de-homestead)
      3. [Déboguer Homestead avec PhpStorm](#déboguer-homestead-avec-phpstorm) 
      4. [Connection à la base de donnée en local, sur PhpStorm](#connection-à-la-base-de-donnée-en-local-sur-PhpStorm) 
  3. [Développer en local](#développer-en-local)
      1. [Outils requis en local](#outils-requis-en-local)  
      2. [Exécuter pour la première fois](#exécuter-pour-la-première-fois)  
      3. [Exécuter](#exécuter)  
      4. [Déboguer en local avec PhpStorm](#déboguer-en-local-avec-phpstorm) 
      5. [Connection à la base de donnée avec Homestead, sur PhpStorm](#connection-à-la-base-de-donnée-avec-homestead-sur-PhpStorm) 
  4. [Postman](#postman)  
      1. [Mise en place de Postman](#mise-en-place-de-postman)

## Information générale

 - Version de Lumen: 5.6
 - Documentation Lumen: https://lumen.laravel.com/docs/5.6 
 - Documentation Laravel: https://laravel.com/docs/5.6
 - Documentation de l'API: https://adept-informatique.github.io/lan.adeptinfo.ca/

## Développer avec Homestead (Recommandé)
Homestead est un environnement de développement fourni par les développeurs de Laravel. L'objectif de homestead est de fournir un environement de développement standardisé qui est garanti de fonctionner avec Laravel (et Lumen). Ce qui signifie qu'aucune configuration ou installation de package n'est nécessaire pour commencer à développer une fois que l'environnement est lancé. Pour plus d'information sur Homestead et vagrant, vous pouvez lire les ressources suivantes:
  - [Homestead](https://laravel.com/docs/5.6/homestead)
  - [Vagrant](https://www.vagrantup.com/docs/index.html)

 ### Outils requis avec Homestead
   - PHP 7.2
      - L'extension fileinfo doit être activée (Ajouter la ligne `extension=php_fileinfo.dll` dans php.ini)
  - [Composer](https://getcomposer.org/)
  - [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
  - [Vagrant](https://www.vagrantup.com/downloads.html)
  
### Installation de Homestead
Les configurations de la VM sont déjà dans le projet, à la racine sous `Vagrantfile` et `after.sh`. Cependant certaines informations doivent être fournies par l'utilisateur.  
:bulb: N'oubliez pas d'activer les technologies de virtualisation dans votre BIOS: vt-x pour Intel, et amd-v pour AMD.*
  - Si vous n'avez pas encore de clé ssh, vous devez en générer une. (Si vous ne savez pas ce que c'est, c'est probablement que vous n'en avez pas)
    - Voici les instructions sous linux (et probablement mac)
    - Dans un terminal, exécuter `ssh-keygen -t rsa -b 4096 -C "votre_courriel@example.com"`
    - Exécuter `eval "$(ssh-agent -s)"`
    - Exécuter `ssh-add -k ~/.ssh/id_rsa`
  - Avec un terminal de commande, se placer à la racine du projet API
  - Exécuter `composer install`, qui installe les dépendances du projet.
  - Exécuter `composer update`, pour s'assurer que les dépendances du projet sont à jour.
  - Exécuter `vagrant box add laravel/homestead`, qui ajoute Homestead aux machines virtuelles de Vagrant.
  - À la racine du projet, exécuter `php vendor/bin/homestead make` si vous êtes sur linux ou mac, ou `vendor\\bin\\homestead make` si vous êtes sur Windows.
. Un fichier nommé Homestead.yaml devrait avoir été généré. Si vous ouvrez ce fichier, vous devriez voir quelques informations sur la configuration de votre projet.
  - Si vous désirez accéder à la machine virtuelle créée, simplement exécuter `vagrant ssh`.
  
  ### Déboguer Homestead avec PhpStorm
  - Assurez vous que votre machine virtuelle est allumée. Il est possible de la mettre en marche via les menus de PhpStorm: `Tools/Vagrant/Up`
  - Configurez PHP Storm pour écouter le débogeur (Bouton  côté de démarrer)
  - Sous `Settings/Language & Framework/PHP` :
    - À côté de "CLI interpreter", cliquer sur les [...]
    - Cliquez sur + et entrez sélectionnez "From Docker, Vagrant, VM, Remote..."
    - Sélectionner le bouton radio "Vagrant"
    - Cliquez sur "OK".
    - Cliquez sur "APPLIQUER".
  - Sous `Settings/Language & Framework/PHP/Test Frameworks`:
    - Cliquez sur + et entrez sélectionnez "PHPUnit by Remote Interpreter"
    - Dans le menu déroulant, sélectionnez l'interpreteur CLI que vous venez d'ajouter à l'étape précédente. Exemple: Remote PHP 7.2 (...)
    - Cliquez sur "OK"
    - Sous la section `Test Runner`, cocher `Default configuration file:`
    - Sur la ligne `Default configuration file:`, sélectionner le chemin vers le fichier `phpunit.xml` du projet.
  - Ajoutez et faites le point d'arrêt que vous voulez atteindre et votre navigateur ou depuis Postman, accédez à l'adresse qui attendra le point d'arrêt
  - Une fenêtre contextuelle devrait apparaître. Dans la section en bas, sélectionnez la première option ((...)`/api/public/index.php`) et appuyez sur "ACCEPT"
  - Veuillez suivre les prochaines étapes uniquement si le point de d'arrêt n'a pas été atteint.
  - Une erreur devrait appraître dans le log d'événements, avec des liens. Sélectionnez `PHP|Server`. Si l'erreur ne s'est pas affiché, simplement naviguer vers `Settings/Language & Framework/PHP/Servers`
  - Dans la hiérarchie de fichiers qui s'affichent, à droite de l'entrée qui indique ((...)`lan.adeptinfo.ca/api`), cliquer, et entrer `home/vagrant/code`
  - Cliquez sur "APPLIQUER" et fermez la fenêtre.
  - Vos points d'arrêt devraient maintenant être atteints
  
### Connection à la base de donnée avec Homestead sur PhpStorm
 - Ouvrez l'onglet "Database", cliquez sur "+", et sélectionnez "Data source", et finalement "MySQL"
 - Vous pouvez cliquer sur le lien "Download drivers" si c'est offert
 - Les champs doivent être remplis comme suit :
    - Host: `homestead.test`
    - Database: `homestead`
    - User: `homestead`
    - Password: `secret`
 - Pour valider, vous pouvez cliquer sur "TEST CONNECTION", puis cliquer sur "APPLY"
 - Pour sélectionner uniquement les shémas qui nous intéressent, à côté de l'entrée "homestead@homestead.test", cliquez sur le chiffre. Ne laissez coché que "homestead" et "lanadepttest"

## Développer en local

 ### Outils requis en local
  - PHP 7.2
  - [Composer](https://getcomposer.org/)
  - Une instance de MySQL server, un utilisateur qui possède tous les droits, ainsi que deux bases de données: `lanadept` et `lanadepttest`.

### Exécuter pour la première fois

 - Avec un terminal de commande, se placer à la racine du projet API
 - Exécuter `composer install` (prend un certain temps)
 - Exécuter `composer update`, pour s'assurer que les dépendances du projet sont à jour.
 - Copier le fichier .env.example pour .env et informer les champs.
    - Veuillez contacter un administrateur du projet pour avoir une configuration de .env préremplie.

 - Si vous êtes sous linux:
      - Ouvrir le fichier php.ini qui devrait se trouver sous `/etc/php/7.x/cli`.
      - Décommenter `;extension=pdo_mysql.so` dans la section Dynamic Extensions du fichier en retirant le `;` au début de la ligne.

 - Sous Windows:
      - S'assurer que la ligne `extension=php_fileinfo.dll` a bien été rajouté dans php.ini(Si vous ne l'avez pas déjà fait).
 - Exécuter `php artisan key:generate`
 - Exécuter `php artisan migrate`
 - Exécuter `php artisan passport:install`
 - Exécuter `php artisan lan:permissions`
 - Exécuter `php artisan lan:roles`
 - Exécuter `php artisan lan:general-admin`
 - Exécuter `php -S localhost:8000 -t public`
 - Ouvrir un navigateur à l'URL suivante: [http://api.localhost:8000](http://api.localhost:8000)

### Exécuter
 - Avec un terminal de commande, se placer à la racine du projet API
 - Exécuter `php -S localhost:8000 -t public`
 - Ouvrir un navigateur à l'URL suivante: [http://api.localhost:8000](http://api.localhost:8000)

### Déboguer en local avec PhpStorm

 - Sous `Settings/Language & Framework/PHP`:
    - À côté de CLI interpreter, cliquer sur les [...]
    - Cliquer sur + et entrez le chemin vers votre interpreteur PHP. Sur linux ce sera `/usr/bin/php` la plupart du temps.
    - Cliquer sur OK.
 - Sous `Settings/Language & Framework/PHP/Debug/DBGp Proxy`
    - IDE key: `PHPSTORM`
    - Host: `127.0.0.1`
    - Port: `9000`
 - Sous `Settings/Language & Framework/PHP/Test Frameworks`:
    - Sous la section `Test Runner`, cocher `Default configuration file:`
    - Sur la ligne `Default configuration file:`, sélectionner le chemin vers le fichier `phpunit.xml` du projet.
 - Configuration Xdebug. Sur linux le chemin est `/etc/php/7.2/cli/conf.d/20-xdebug.ini`
    - [Xdebug]
    - zend_extension=xdebug.so
    - xdebug.remote_autostart=1
    - xdebug.default_enable=1
    - xdebug.remote_port=9001
    - xdebug.remote_host=127.0.0.1
    - xdebug.remote_connect_back=1
    - xdebug.remote_enable=1
    - xdebug.idekey=PHPSTORM
 - Créer une nouvelle configuration "PHP Built-in Web Server"
    - Host: `127.0.0.1`
    - Port: 8000
    - Document root: `[...]/lanadept.com/api`
    - Use router script (coché): `[...]/lanadept.com/api/public/index.php`
    - Interpreter options: `-dxdebug.remote_enable=1 -dxdebug.remote_mode=req -dxdebug.remote_port=9000 -dxdebug.remote_host=127.0.0.1`
    - (Optionnel) "Cocher Single Instance Only"
    - Cliquer sur "Apply"
 - Configurez PHP Storm pour écouter le débogeur (Bouton  côté de démarrer).
 - Démarrez le serveur.
 
 ### Connection à la base de donnée en local, sur PhpStorm
 - Ouvrez l'onglet "Database", cliquez sur "+", et sélectionnez "Data source", et finalement "MySQL"
 - Vous pouvez cliquer sur le lien "Download drivers" si c'est offert
 - Les champs doivent être remplis comme suit :
    - Host: `localhost`
    - Database: `lanadept`
    - User: `votre-nom-d'utilisateur`
    - Password: `votre-mot-de-passe-de-bd`
 - Pour valider, vous pouvez cliquer sur "TEST CONNECTION", puis cliquer sur "APPLY"
 - Pour sélectionner uniquement les shémas qui nous intéressent, à côté de l'entrée "homestead@homestead.test", cliquez sur le chiffre. Ne laissez coché que "homestead" et "lanadepttest"

 ## Postman
 ### Mise en place de Postman
 Une liste de requête a déjà été montée par le créateur du reposiory. Pour obtenir cette liste simplement contacter [Pierre-Olivier Brillant](https://github.com/PierreOlivierBrillant).
 - Créer un environnement pour le projet avec les paramètres suivants
    - server-address: si vous développez en local: `http://api.localhost:8000`. Si vous développez avec Homestead: `http://api.homestead.test`. Vous pouvez aussi créer un environnement pour chaque options, puisqu'il s'agit en effet "d'environnements" de développement différents.
    - client-secret: La clé qui a été généré après avoir entré la commande `php artisan passport:install`. La clé est aussi dans la base de donnée sous la table `oauth_clients`, l'entrée avec l'id 2, la colonne "secret".
 - Créez un utilisateur avec l'appel `User/sign up`
 - Configuration de la fenêtre Get new access token (Sous la section `Authorization` d'une des requêtes, le lien `lanadept.com`, le bouton `get new access token` 
    - Token name: `Lumen`
    - Grant Type: `Password Credential`
    - Access Token URL `{{server-address}}/api/oauth/token`
    - Username: `karl.marx@unite.org`
    - Password: `Passw0rd!`
    - Client ID: `2`
    - Client Secret: `{{client-secret}}`
    - Scope: 
    - Client Authentication: `Send as Basic Auth header`
 - Cliquer sur `Get new access token`
 - *Attention* Si le serveur vous renvoit un code d'erreur `401` avec le message `Unauthorized`, il est possible que vous ayez à sélectionner votre token dans le menu déroulant dans la section `Authorization`. Cela ce produit puisque l'ancien token est sauvegardé dans Postman.
