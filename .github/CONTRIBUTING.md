# Guide de contribution pour lanadept.com

## Avant toute chose
Merci! C'est très apprécié que vous prenieez de votre temps pour contribuer à ce projet. Soyez certain que si vos contributions suivent se guide et son bien faites, vous allez définitivement avoir un impact sur l'expérience de LAN de plusieurs centaines de joueurs. Amusez vous à coder! :)

## Architecture générale
Le projet lanadept.com suit une architecture REST, ce qui signifie que des clients autorisés font des requêtes a une ressource backend séparée qui est appelée un API.

 Le repository est séparé en 3 différents projets:
  - `/api` Un API qui s'occupe de toute la logique dans le backend. Vous trouverez une documentation des appels HTTP disponibles [ici](https://adept-informatique.github.io/lan.adeptinfo.ca/)
  - `/client-user` Un client qui s'occupe de toutes les actions du joueur, comme la réservation des places, l'enregistrement aux tournois, l'information générale, etc...
  - `/client-admin` Un client qui s'occupe de toutes les tâches administratives du LAN, comme les dates du LAN, les paramètres des sièges, les options des tournois, la définition des règles générales, etc...
## API
L'API est construit sur un version allégée de [Laravel](https://github.com/laravel/laravel) appelée [Lumen](https://github.com/laravel/lumen), qui est faite spécialement pour des APIs.

Nous recommandons fortement de lire la documentation de Laravel avant de se lancer dans le développement. Vous allez vous rendre compte qu'elle est très accessible, et qu'elle est faite pour être lue.

Quelques choix d'architecture ont été faits par le créateur du projet qui ne sont pas directement liés  Lumen, mais qui sont certainement facilités. Si vous désirez proposer un changement sur les décisions qui ont été prises, n'hésitez pas à contacter le propriétaire du repository!

Pour en savoir davantage sur l'API et comment configurer votre environnement de développement, allez au [dossier de l'API](https://github.com/ADEPT-Informatique/lan.adeptinfo.ca/tree/master/api)

### Structure
 - **app**
   - **Services**:Interfaces qui définissent toute la logique de l'application
     - **Implementation**: Implementations pour les interfaces des services. Ces classes vont être injectées et utilisés par un contrôleur. 
    - **Repositories**: Interface qui définie les accès aux données (BD)
      - **Implementation**: Implementations pour les interfaces des repositories.Ces classes vont être injectés et utilisées par un service. 

### Tests
Ça serait super cool d'inclure des tests pour les fonctionalitées qui vous développez. Les tests ne sont pas requis, mais vous pouvez toujours vous donner une tape dans le dos quand vous en faites...  

### Autres librairies
Nous utilisons aussi quelque librairies / ressources externes

 - **[Passport](https://packagist.org/packages/dusterio/lumen-passport)**: Package oauth2 qui rend les logins plus sécuritaire et les tokens plus faciles à gérer.
 - **[Dingo](https://github.com/dingo/api)**: Gestion de ressource d'API. En ce moment s'occupe des routes.
 - **[seats.io](https://github.com/seatsio/seatsio-php)**: Un API de gestion de place très utile qui offre des librairies frontend et backend pour montrer la disponibilité des places.
 - **[Laravel Cors](https://github.com/barryvdh/laravel-cors)** : Une librairie qui s'occupe de tous les maux de tête liés CORS.
 - **[Laravel-lang](https://github.com/caouecs/Laravel-lang)** : Traductions des réponses de serveur qui sont déjà existantes dans laravel (Nous utilisons surtout les traduction des validations).

 ### Lignes directrices pour le développement de l'API.
 - Validation : 
     - Le développeur qui créer des accès HTTP s'engage à produire des accès robustes, soit qui prennent en charge l'ensemble des valeurs possibles pour les paramètres qu'il rends disponible au client. (Ex: que se passe-t-il quand un paramètre est nulle, qu'il ne comporte pas le type qui est attendu, qu'il est en dehors des limites attendues, etc...).
     - Chaques erreur doit retourner une erreur significative, qui aide le client à comprendre ce qui a pu causer l'erreur, et comment la corriger. Les messages d'erreurs sont situés dans : `resource/lang/fr/validation` (français) et `resource/lang/en/validation` (anglais).
 - Tests :
     - Chaques accès HTTP doit être au minimum testé selon l'ensemble de ses limites, et ses différents cas fonctionnels (Ex: paramètre absent et présent). Voir le dossier `Unit/Controller`.
     - Chaques services doit être au minimum testé selon ses limites, et ses différents cas fonctionnels (Ex: paramètre absent et présent). Voir le dossier `Unit/Services`. (Devrait être relativement similaire aux tests de l'accès HTTP).
     - Chaques repository doit être au minimum testé selon sa fonctionnalité principale.
 - Pour chaques accès administrateur :
     - Le nom et la description doivent être définis dans les fichiers de ressource `resource/lang/en/permission` (anglais) et `resource/lang/fr/permission` (français), selon la convention de nommage suivante pour le nom à afficher pour la permission: `display-name-"nom-de-la-permission"` et pour la description: `description-"nom-de-la-permission"`.
     - Le nom interne (unique) doit être ajouté dans `app/Console/Commands/GeneratePermissions.php`, sous la fonction `getPermissions()`, en [kebab case](http://wiki.c2.com/?KebabCase).
     - Pour créer les permissions de l'API dans la base de donnée, exécuter la commande `php artisan lan:permissions`. Il est à noter que l'application des deux étapes précédentes est cruciale à ce que le système de permission puisse être fonctionnel. La permière étape permet permet l'affichage pour le client des permissions, alors que la deuxième étape permet la dispinibilité  l'interne, soit dans l'API de la permission.
     - La vérification de la permission doit être faite dans le controlleur.
