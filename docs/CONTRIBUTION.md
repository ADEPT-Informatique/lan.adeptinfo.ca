# Guide de contribution pour lanadept.com

## Avant toute chose
Merci! C'est très apprécié que vous prenieez de votre temps pour contribuer à ce projet. Soyez certain que si vos contributions suivent se guide et son bien faites, vous allez définitivement avoir un impact sur l'expérience de LAN de plusieurs centaines de joueurs. Amusez vous à coder! :)

## Architecture générale
Le projet lanadept.com suit une architecture REST, ce qui signifie que des clients autorisés font des requêtes a une ressource backend séparée qui est appelée un API.

 Le repository est séparé en 3 différents projets:
  - `/api` Un API qui s'occupe de toute la logique dans le backend
  - `/client-user` Un client qui s'occupe de toutes les actions du joueur, comme la réservation des places, l'enregistrement aux tournois, l'information générale, etc...
  - `/client-admin` Un client qui s'occupe de toutes les tâches administratives du LAN, comme les dates du LAN, les paramètres des sièges, les options des tournois, la définition des règles générales, etc...
## API
L'API est construit sur un version allégée de [Laravel](https://github.com/laravel/laravel) appelée [Lumen](https://github.com/laravel/lumen), qui est faite spécialement pour des APIs.

Nous recommandons fortement de lire la documentation de Laravel avant de se lancer dans le développement. Vous allez vous rendre compte qu'elle est très accessible, et qu'elle est faite pour être lue.

Quelques choix d'architecture ont été faits par le créateur du projet qui ne sont pas directement liés  Lumen, mais qui sont certainement facilités. Si vous désirez proposer un changement sur les décisions qui ont été prises, n'hésitez pas à contacter le propriétaire du repository!

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
