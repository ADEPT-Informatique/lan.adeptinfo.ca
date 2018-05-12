# API LAN de l'ADEPT

Cet api représente le backend complet du site web du LAN de l'ADEPT. Il rassemble donc le côté utilisateur, ainsi que le côté administrateur du site.

## Information générale

 - Version de Lumen: 5.6
 - Documentation Lumen: https://lumen.laravel.com/docs/5.6 
 - Documentation Laravel: https://laravel.com/docs/5.6


## Développer en local

### Outils recommandés

 - Un IDE polyvalent pour développer en PHP (ex: atom, sublime, PhpStorm, etc...)
 - Postman

### Exécuter pour la première fois

 - Avec un terminal de commande, se placer à la racine du projet API
 - Exécuter `composer install` (prend un certain temps)
 - Exécuter `php artisan key:generate`
 - Exécuter `php artisan migrate`
 - Exécuter `php artisan passport:install`
 - Exécuter `php -S localhost:8000 -t public`
 - Ouvrir un navigateur à l'URL suivante: [http://localhost:8000](http://localhost:8000)

### Exécuter
 - Avec un terminal de commande, se placer à la racine du projet API
 - Exécuter `php -S localhost:8000 -t public`
 - Ouvrir un navigateur à l'URL suivante: [http://localhost:8000](http://localhost:8000)

### Déboguer avec PhpStorm

 - Prérequis: Interpréteur PHP CLI
 - Créer une nouvelle configuration "PHP Built-in Web Server"
 - Host: `localhost`
 - Document root: `[...]/lanadept.com/api`
 - Use router script (coché): `[...]/lanadept.com/api/public/index.php`
- Interpreter options:
  - -dxdebug.remote_enable=1
  - dxdebug.remote_mode=req
  - dxdebug.remote_port=9000
  - dxdebug.remote_host=127.0.0.1
 - (Optionnel) "Cocher Single Instance Only"
 - Cliquer sur "Apply"
